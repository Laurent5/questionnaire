<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Reponses;
use App\Entity\ReponsesFerme;
use App\Entity\ReponsesFournies;
use App\Entity\ReponsesFourniesIndividuelles;
use App\Entity\Thematique;
use App\Service\FormQuestionnaireFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class QuestionnaireController extends Controller {

    /**
     * @param Request $request
     * @Route("/", name="home")
     */
    public function homeAction(Request $request,FormQuestionnaireFactory $factory, Session $session){
        $session->start();
        $thematique = null;
        if($session->get('questionnaire_id',false) && $session->get('last_ok',false)){
            /** @var Thematique $thematique */
            $thematique = $this->getDoctrine()->getRepository(Thematique::class)->find($session->get('last_ok')+1);
        }else{
            /** @var Thematique $thematique */
            $thematique = $this->getDoctrine()->getRepository(Thematique::class)->find(1);
        }

        //ByPass
        $thematique = $this->getDoctrine()->getRepository(Thematique::class)->findOneBy(array('ordre'=>'1'));
        //ByPass

        $form = $factory->getFormFor($thematique);
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

            }else{
                $data->setRepondantToken($this->get('security.token_storage')->getToken()->getSecret());
                $session->set('questionnaire_id',$data->getQuestionnaireId());
            }

            $manager->persist($data);

            $session->set('last_ok',$thematique->getId());
            $session->save();

            $manager->flush();

            return $this->redirectToRoute("home");
        }

        return $this->render('questionnaire/home.html.twig',array(
            'form' => $form->createView()
        ));

    }

    /**
     * @param Question $question
     * @Route("/get/questions/for/{reponse}", name="ajax_get_question", requirements = { "reponse" : "\d+" }, condition="request.isXmlHttpRequest()" )
     */
    public function ajaxGetQuestion(ReponsesFerme $reponse, FormQuestionnaireFactory $factory, Session $session){

        /** @var ReponsesFournies $questionnaire */
        $questionnaire = $this->getDoctrine()->getRepository(ReponsesFournies::class)->findOneBy(array(
            'questionnaire_id' => "5b2682b26e1b5" //$session->get('questionnaire_id',false)
        ));
        dump("Changer le questionnaire_id");
        return new Response($this->renderView('questionnaire/ajax_field_render.html.twig',array(
            'form' => $factory->createFormFromReponse($reponse,$questionnaire)->createView()
        )));
    }


}