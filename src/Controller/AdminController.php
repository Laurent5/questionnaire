<?php
/**
 * Created by PhpStorm.
 * User: laurent5
 * Date: 5/07/18
 * Time: 11:06
 */

namespace App\Controller;


use App\Entity\Question;
use App\Entity\QuestionPrerequis;
use App\Entity\ReponsesFerme;
use App\Entity\ReponsesOuverte;
use App\Entity\Thematique;
use App\Form\FiltreType;
use App\Form\QuestionFermeAvecFiltreType;
use App\Form\QuestionFermeeType;
use App\Form\QuestionOuverteAvecFiltreType;
use App\Form\QuestionOuverteType;
use App\Form\SousQuestionFermeType;
use App\Form\SousQuestionOuverteType;
use App\Form\ThematiqueType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


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
            'questions' => $this->getDoctrine()->getRepository(Question::class)->findBy(array(),array('ordre'=>'ASC'))
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
     * @Route("/admin/thematique/{thematique}/remove", name="admin_remove_thematique")
     */
    public function removeThematique(Thematique $thematique){
        $manager = $this->getDoctrine()->getManager();

        $manager->remove($thematique);
        $manager->flush();

        return $this->redirectToRoute("admin_home");
    }

    /**
     * @param Request $request
     * @param Question $question
     * @Route("/admin/question/modifier/{question}", name="admin_modifier_question", requirements={"question"="\d+"} )
     */
    public function updateQuestion(Request $request, Question $question){
        if($question->getReponses() && get_class($question->getReponses()->first()) == ReponsesFerme::class){
            if($question->getReponsePreRequise()->count() == 0){
                return $this->questionFermeeSansFiltre($request,$question);
            }else{
                if($question->getReponsePreRequise()->count() > 1){
                    return $this->questionFermeeAvecFiltre($request,$question);
                }else{
                   return $this->sousQuestionFermeeSansFiltre($request,$question);
                }
            }
        }elseif ($question->getReponses() && get_class($question->getReponses()->first()) == ReponsesOuverte::class) {
            if ($question->getReponsePreRequise()->count() == 0) {
                return $this->questionOuverteSansFiltre($request,$question);
            } else {
                if ($question->getReponsePreRequise()->count() > 1) {
                    return $this->questionOuverteAvecFiltre($request,$question);
                } else {
                    return $this->sousQuestionOuverteSansFiltre($request,$question);
                }
            }
        }

        throw new LogicException();
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

}