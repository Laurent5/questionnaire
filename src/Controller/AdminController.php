<?php
/**
 * Created by PhpStorm.
 * User: laurent5
 * Date: 5/07/18
 * Time: 11:06
 */

namespace App\Controller;


use App\Entity\Question;
use App\Entity\Thematique;
use App\Form\QuestionFermeeType;
use App\Form\QuestionOuverteType;
use App\Form\ThematiqueType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
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
            'thematiques' => $this->getDoctrine()->getRepository(Thematique::class)->findBy(array(),array('ordre'=>'ASC'))
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/question/ouverte/sans_filtre/add", name="admin_add_qosf")
     */
    public function questionOuverteSansFiltre(Request $request){
        return $this->questions($request, QuestionOuverteType::class);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/question/fermee/sans_filtre/add", name="admin_add_qfsf")
     */
    public function questionFermeeSansFiltre(Request $request){
        return $this->questions($request, QuestionFermeeType::class);
    }

    /**
     * @param Request $request
     * @param $type
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    private function questions(Request $request, $type){
        $form = $this->createForm($type);
        $form->add('Envoyer',SubmitType::class,array(

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
            'h1' => "Ajouter une question"
        ));
    }

}