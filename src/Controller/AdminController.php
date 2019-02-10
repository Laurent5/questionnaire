<?php
/**
 * Created by PhpStorm.
 * User: laurent5
 * Date: 5/07/18
 * Time: 11:06
 */

namespace App\Controller;


use App\Entity\Categorisation;
use App\Entity\Fin;
use App\Entity\Question;
use App\Entity\QuestionPrerequis;
use App\Entity\Reponses;
use App\Entity\ReponsesFerme;
use App\Entity\ReponsesFournies;
use App\Entity\ReponsesFourniesIndividuelles;
use App\Entity\ReponsesFourniesIndividuellesOuverte;
use App\Entity\ReponsesOuverte;
use App\Entity\Thematique;
use App\Form\CategorisationFormType;
use App\Form\CategorisationType;
use App\Form\FiltreType;
use App\Form\FinType;
use App\Form\QuestionFermeAvecFiltreType;
use App\Form\QuestionFermeeType;
use App\Form\QuestionOuverteAvecFiltreType;
use App\Form\QuestionOuverteType;
use App\Form\SousQuestionFermeType;
use App\Form\SousQuestionOuverteType;
use App\Form\ThematiqueType;
use App\Service\FormQuestionnaireFactory;
use Doctrine\Common\Collections\ArrayCollection;
use function Sodium\add;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Util\ClassUtils;


/**
 * Class AdminController
 * @package App\Controller
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/home", name="admin_home")
     */
    public function home(){
        return $this->render('admin\home.html.twig',array(
            'thematiques' => $this->getDoctrine()->getRepository(Thematique::class)->findBy(array(),array('ordre'=>'ASC')),
            'questions' => $this->getDoctrine()->getRepository(Question::class)->getQuestions(),
            'questionnaires' => $this->getDoctrine()->getRepository(ReponsesFournies::class)->findAll(),
            'fins' => $this->getDoctrine()->getRepository(Fin::class)->findBy(array(),array('ordre'=>'ASC'))
        ));
    }

    /**
     * @param Request $request
     * @param Question $question
     * @param Categorisation|null $categorisation
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/question/{question}/add/categorisation", name="admin_add_categorisation", requirements={"question"="\d+"})
     * @Route("/question/{question}/categorisation/{categorisation}/update", name="admin_update_categorisation", requirements={"question"="\d+","categorisation"="\d+"})
     */
    public function categorisationManagement(Request $request, Question $question, Categorisation $categorisation = null){

        if($request->get("_route") == "admin_add_categorisation"){
            $categorisation = null;
        }

        $form = $this->createForm(CategorisationFormType::class, $categorisation);
        $form->add(($categorisation===null ? "Ajouter" : "Modifier"),SubmitType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            /** @var Categorisation $categorisationNew */
            $categorisationNew = $form->getData();

            $categorisationNew->setQuestion($question);
            $question->addCategory($categorisationNew);

            $manager = $this->getDoctrine()->getManager();

            $manager->persist($categorisationNew);
            $manager->persist($question);
            $manager->flush();

            $this->addFlash("success","Opération réalisée avec succès !");

            return $this->redirectToRoute("admin_add_categorisation",array(
                'question' => $question->getId()
            ));
        }

        return $this->render('admin\categorisation_management.html.twig',array(
            'question' => $question,
            'form' => $form->createView()
        ));

    }

    /**
     * @param Categorisation $categorisation
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/categorisation/delete/{categorisation}", name="admin_delete_categorisation", requirements={"categorisation"="\d+"})
     */
    public function removeCategorie(Categorisation $categorisation){
        if($categorisation->getReponsesFournies()->count() > 0){
            $this->addFlash("error", "Impossible de supprimer cette catégorie car il y a des réponses qui y sont affectuées");
        }else{
            $this->getDoctrine()->getManager()->remove($categorisation);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash("success","La catégorie a bien été supprimée");
        }

        return $this->redirectToRoute("admin_add_categorisation",array(
            'question' => $categorisation->getQuestion()->getId()
        ));
    }

    /**
     * @param Fin $fin
     * @return Response
     * @Route("/view/fin/{fin}", name="admin_view_fin", requirements={"fin"="\d+"})
     */
    public function viewFin(Fin $fin = null){
        if($fin !== null) {
            return $this->render('questionnaire\fin.html.twig', array(
                'fin' => $fin
            ));
        }else{
            return $this->render('questionnaire\fin_default.html.twig');
        }
    }

    /**
     *@Route("/byQuestion", name="admin_reponses_by_questions")
     */
    public function getReponsesByQuestions(){
        $questions = $this->getDoctrine()->getRepository(Question::class)->getQuestionAvecReponses();
        return $this->render('admin\byQuestion.html.twig',array(
            'questions' => $questions
        ));
    }

    /**
     *@Route("/byQuestionStats", name="admin_reponses_by_questions_stats")
     */
    public function getReponsesByQuestionsStats(){
        $questions = $this->getDoctrine()->getRepository(Question::class)->getQuestionAvecReponsesStats();
        return $this->render('admin\byQuestionStats.html.twig',array(
            'questions' => $questions
        ));
    }

    /**
     * @Route("/categorise",name="admin_categorise")
     */
    public function categoriseReponse(Request $request){

        /** @var null|array $question */
        $reponse = $this->getDoctrine()->getRepository(ReponsesFourniesIndividuellesOuverte::class)->getReponseWithoutCategories();

        if($reponse !== null) {
            $reponse = $reponse[0];
            $form = $this->createForm(CategorisationType::class,$reponse);
            $form->add("Classifier !",SubmitType::class);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){

                /** @var ReponsesFourniesIndividuellesOuverte $data */
                $data = $form->getData();

                $this->getDoctrine()->getManager()->persist($data);
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute("admin_categorise");
            }

        }

        return $this->render('admin\categorisation.html.twig',array(
            'reponse' => $reponse,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/byQuestionnaires", name="admin_reponses_by_questionnaires")
     * @Route("/byQuestionnaire/{questionnaire}", name="admin_reponses_by_questionnaire", requirements={"questionnaire"="\d+"})
     * @param ReponsesFournies|null $questionnaire
     * @return Response
     */
    public function getReponsesByQuestionnaire(ReponsesFournies $questionnaire = null){
        if($questionnaire == null){
            return $this->render('admin\questionnaires.html.twig',array(
                'questionnaires' => $this->getDoctrine()->getRepository(ReponsesFournies::class)->findAll()
            ));
        }else{
            return $this->render('admin\questionnaire.html.twig',array(
                'questionnaire' => $questionnaire
            ));
        }
    }


    /**
     * @param Thematique $thematique
     * @param Request $request
     * @param FormQuestionnaireFactory $factory
     * @Route("/view/thematique/{thematique}", name="admin_view_thematique")
     * @return Response
     */
    public function viewThematique(Thematique $thematique, Request $request, FormQuestionnaireFactory $factory){
        $form = $factory->getFormFor($thematique);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->addFlash("success","Ceci est une simulation - Le formulaire est conssidéré comme valide ...");
        }

        return $this->render('questionnaire/home.html.twig',array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     * @param Thematique|null $thematique
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/thematique/create", name="admin_add_thematique")
     * @Route("/thematique/{thematique}/update", name="admin_update_thematique")
     */
    public function addUpdateThematique(Request $request, Thematique $thematique = null){
        if($thematique == null){
            $thematique = new Thematique();
        }

        $form = $this->createForm(ThematiqueType::class, $thematique);
        $form->add("C'est partit !",SubmitType::class,array(

        ));

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            /** @var ThematiqueType $data */
            $data = $form->getData();

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($data);
            $manager->flush();

            return $this->redirectToRoute("admin_home");
        }

        return $this->render('admin/form.html.twig',array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param Thematique $thematique
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/thematique/{thematique}/remove", name="admin_remove_thematique")
     */
    public function removeThematique(Thematique $thematique){
        $manager = $this->getDoctrine()->getManager();

        $manager->remove($thematique);
        $manager->flush();

        return $this->redirectToRoute("admin_home");
    }

    /**
     * @param Request $request
     * @param Fin $fin
     * @Route("/add/fin", name="admin_add_fin")
     * @Route("/update/fin/{fin}", name="admin_add_fin", requirements={"fin"="\d+"})
     * @return Response
     */
    public function addFin(Request $request, Fin $fin = null){
        $form = $this->createForm(FinType::class, $fin);
        $form->add("Valider",SubmitType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            /** @var Fin $data */
            $data = $form->getData();

            $manager = $this->getDoctrine()->getManager();

            $manager->persist($data);
            $manager->flush();

            $this->addFlash("success","La fin est bien enregistrée");

            return $this->redirectToRoute("admin_home");
        }

        return $this->render('admin\form.html.twig',array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param Fin $fin
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/remove/fin/{fin}", name="admin_remove_fin", requirements={"fin"="\d+"})
     */
    public function removeFin(Fin $fin){
        $this->getDoctrine()->getManager()->remove($fin);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute("admin_home");
    }

    /**
     * @param Request $request
     * @param Question $question
     * @Route("/question/modifier/{question}", name="admin_modifier_question", requirements={"question"="\d+"} )
     */
    public function updateQuestion(Request $request, Question $question){

        switch ($question->getTypeOfQuestion()){
            case Question::QUESTION_REPONSE_OUVERTE_AVEC_FILTRE : return $this->questionOuverteAvecFiltre($request,$question);
            case Question::QUESTION_REPONSE_OUVERTE_SANS_FILTRE : return $this->questionOuverteSansFiltre($request,$question);
            case Question::QUESTION_REPONSE_FERME_AVEC_FILTRE : return $this->questionFermeeAvecFiltre($request,$question);
            case Question::SOUS_QUESTION_REPONSE_FERME: return $this->sousQuestionFermeeSansFiltre($request,$question);
            case Question::SOUS_QUESTION_REPONSE_OUVERTE: return $this->sousQuestionOuverteSansFiltre($request,$question);
            case Question::QUESTION_REPONSE_FERME_SANS_FILTRE : return $this->questionFermeeSansFiltre($request,$question);
        }
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/question/ouverte/sans_filtre/add", name="admin_add_qosf")
     */
    public function questionOuverteSansFiltre(Request $request,Question $question = null){
        return $this->questions($request, QuestionOuverteType::class,$question);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/question/ouverte/avec_filtre/add", name="admin_add_qoaf")
     */
    public function questionOuverteAvecFiltre(Request $request,Question $question = null){
        return $this->questions($request, QuestionOuverteAvecFiltreType::class,$question);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/sous_question/fermee/sans_filtre/add", name="admin_add_sqfsf")
     */
    public function sousQuestionFermeeSansFiltre(Request $request, Question $question = null){
        $data = (($question!==null) ? $question->getReponsePreRequise()->first() : $question);
        return $this->questions($request, SousQuestionFermeType::class, $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/question/fermee/sans_filtre/add", name="admin_add_qfsf")
     */
    public function questionFermeeSansFiltre(Request $request, Question $question = null){
        return $this->questions($request, QuestionFermeeType::class, $question);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/question/fermee/avec_filtre/add", name="admin_add_qfaf")
     */
    public function questionFermeeAvecFiltre(Request $request, Question $question = null){
        return $this->questions($request, QuestionFermeAvecFiltreType::class, $question);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/sous_question/ouverte/sans_filtre/add", name="admin_add_sqosf")
     */
    public function sousQuestionOuverteSansFiltre(Request $request, Question $question = null){
        $data = (($question!==null) ? $question->getReponsePreRequise()->first() : $question);
        return $this->questions($request, SousQuestionOuverteType::class,$data);
    }

    /**
     * @param Request $request
     * @param $type
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    private function questions(Request $request, $type, $data = null){
        $form = $this->createForm($type,$data);
        $form->add('envoyer',SubmitType::class,array(
            'label' => ($data !== null) ? "Modifier la question" : "Créer la question",
        ));
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            /** @var Question $data */
            $data = $form->getData();

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($data);
            $manager->flush();

            return $this->redirectToRoute("admin_home");
        }

        return $this->render('admin\form.html.twig',array(
            'form'=> $form->createView(),
            'h1' => (($data !== null) ? "Modifier" : "Ajouter") . " une question"
        ));
    }

    /**
     * @param Request $request
     * @param Question $question
     * @return string|null
     * @Route("/reponses/get/{question}", condition="request.isXmlHttpRequest()", requirements={"question"="\d+"})
     */
    public function getReponsesFermees(Request $request, Question $question){

        if($question !== null){
            $reponses = $question->getReponses();
            foreach ($reponses as $reponse){
                if(get_class($reponse) != ReponsesFerme::class){
                    return new Response();
                }
            }
            return new Response($this->renderView('admin/_select_options.html.twig',array(
                'reponses' => $reponses
            )));
        }

        return new Response();

    }

    /**
     * @param Question $question
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/remove/{question}", name="admin_remove_question")
     */
    public function removeQuestion(Question $question){
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($question);
        $manager->flush();

        $this->addFlash("success", "La question a bien été supprimée");

        return $this->redirectToRoute("admin_home");
    }

}