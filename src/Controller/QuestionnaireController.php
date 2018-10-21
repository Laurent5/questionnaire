<?php

namespace App\Controller;

use App\Entity\Fin;
use App\Entity\Question;
use App\Entity\Reponses;
use App\Entity\ReponsesFerme;
use App\Entity\ReponsesFournies;
use App\Entity\ReponsesFourniesIndividuelles;
use App\Entity\ReponsesFourniesIndividuellesFerme;
use App\Entity\Thematique;
use App\Service\FormQuestionnaireFactory;
use Doctrine\Common\Util\ClassUtils;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class QuestionnaireController extends Controller {

    /**
     * @return Response
     * @Route("/recherche", name="recherche")
     */
    public function laRecherche(){
        return $this->render('questionnaire\la_recherche.html.twig');
    }

    /**
     * @param Request $request
     * @Route("/", name="home")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function homeAction(Request $request,FormQuestionnaireFactory $factory, Session $session){
        $session->start();
        $thematique = null;
        $questionnaire = null;

        if($session->get('questionnaire_id',false) && $session->get('last_ok',false)){

            /** @var ReponsesFournies $questionnaire */
            $questionnaire = $this->getDoctrine()->getRepository(ReponsesFournies::class)->findOneBy(array(
                'questionnaire_id' => $session->get('questionnaire_id', false)
            ));

            /** @var Thematique $thematique */
            $thematique = $this->getDoctrine()->getRepository(Thematique::class)->findOneByOrdre($session->get('last_ok')+1);

            if($thematique === null){
                //On est alors à la fin du questionnaire
                return $this->getFin($questionnaire);
            }

        }else{
            /** @var Thematique $thematique */
            $thematique = $this->getDoctrine()->getRepository(Thematique::class)->findOneByOrdre(1);
        }


        $form = $factory->getFormFor($thematique,$questionnaire);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){

            $manager = $this->getDoctrine()->getManager();
            /** @var ReponsesFournies $data */
            $data = $form->getData();

            if($session->get('questionnaire_id',false)){

                /** @var ReponsesFournies $questionnaire */
                $questionnaire = $this->getDoctrine()->getRepository(ReponsesFournies::class)->findOneBy(array(
                    'questionnaire_id' => $session->get('questionnaire_id',false)
                ));

                /** @var ReponsesFourniesIndividuelles $reponses */
                foreach ($data->getReponses() as $reponse){
                    $questionnaire->addReponse($reponse);
                }

                $manager->persist($questionnaire);

            }else{

                $data->setRepondantToken($this->get('security.token_storage')->getToken()->getSecret());

                $manager->persist($data);

                $session->set('questionnaire_id',$data->getQuestionnaireId());
            }

            $session->set('last_ok',$thematique->getOrdre());
            $session->save();

            $manager->flush();

            return $this->redirectToRoute("home");
        }

        return $this->render('questionnaire/home.html.twig',array(
            'form' => $form->createView()
        ));

    }

    public function getFin(ReponsesFournies $questionnaire){
        /** Test des fins alternatives */
        $fins = $this->getDoctrine()->getRepository(Fin::class)->findBy(array(),array('ordre'=>'ASC'));

        /** @var Fin $fin */
        foreach ($fins as $fin){
            /** @var ReponsesFourniesIndividuelles $reponsesFourniesIndividuelles */
            foreach ($questionnaire->getReponses() as $reponsesFourniesIndividuelles){
                if(ClassUtils::getClass($reponsesFourniesIndividuelles) === ReponsesFourniesIndividuellesFerme::class && $reponsesFourniesIndividuelles->getReponsesFerme()->getId() == $fin->getFiltre()->getId()){
                    return $this->render('questionnaire\fin.html.twig',array(
                        'fin' => $fin
                    ));
                }
            }
        }
        return $this->render('questionnaire\fin_default.html.twig');
    }


    /**
     * @param Question $question
     * @Route("/get/questions/for/{reponse}", name="ajax_get_question", requirements = { "reponse" : "\d+" }, condition="request.isXmlHttpRequest()" )
     */
    public function ajaxGetQuestion(ReponsesFerme $reponse, FormQuestionnaireFactory $factory, Session $session){

        if(!$session->isStarted()){
            $session->start();
        }
        dump($session->get('questionnaire_id',false));
        /** @var ReponsesFournies $questionnaire */
        $questionnaire = $this->getDoctrine()->getRepository(ReponsesFournies::class)->find(
            $session->get('questionnaire_id',false)
        );
        dump($questionnaire);
        return new Response($this->renderView('questionnaire/ajax_field_render.html.twig',array(
            'form' => $factory->createFormFromReponse($reponse,$questionnaire)->createView()
        )));
    }


}