<?php

namespace App\Controller;

use App\Entity\Categorisation;
use App\Entity\Question;
use App\Entity\ReponsesFournies;
use App\Entity\ReponsesFourniesIndividuelles;
use App\Entity\ReponsesFourniesIndividuellesFerme;
use App\Entity\ReponsesFourniesIndividuellesOuverte;
use Doctrine\Common\Collections\ArrayCollection;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ExcellController
 * @package App\Controller
 * @Route("/admin/excell")
 */
class ExcellController extends AbstractController
{
    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @Route("/all/active",name="admin_excell_get_all_active")
     */
    public function getAllActiveQuestionnaires(){
        $questions = $this->getDoctrine()->getRepository(Question::class)->getQuestionAvecReponses();
        $formulaires = $this->getDoctrine()->getRepository(ReponsesFournies::class)->getAllActives();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValueByColumnAndRow(1,1,"Questionnaire Id");
        $sheet->getColumnDimensionByColumn(1)->setAutoSize(true);

        /**
         * @key = ordre de la question (Unique)
         * @value = colonne affectée à la question
         */
        $maps = new ArrayCollection();


        /**
         * @var integer $key
         * @var  Question $question
         */
        foreach ($questions as $key => $question){
            $sheet->setCellValueByColumnAndRow( $key+2, 1, $question->getOrdre().") ". $question->getQuestion());
            $sheet->getColumnDimensionByColumn($key+2)->setAutoSize(true);
            $maps[$question->getOrdre()] = $key+2;
        }

        /**
         * @var integer $key2
         * @var  ReponsesFournies $formulaire
         */
        foreach ($formulaires as $key2 => $formulaire){
            $sheet->setCellValueByColumnAndRow(1,$key2+2, $formulaire->getQuestionnaireId());

            /** @var ReponsesFourniesIndividuelles $reponse */
            foreach ($formulaire->getReponses() as $reponse){
                $valeur = "";

                if($reponse->getQuestions()->getTypeOfQuestion() === Question::QUESTION_REPONSE_OUVERTE_AVEC_FILTRE || $reponse->getQuestions()->getTypeOfQuestion() === Question::QUESTION_REPONSE_OUVERTE_SANS_FILTRE || $reponse->getQuestions()->getTypeOfQuestion() === Question::SOUS_QUESTION_REPONSE_OUVERTE )
                {
                    /** @var ReponsesFourniesIndividuellesOuverte $reponse */
                    if($reponse->getCategories()->count() > 0){
                        /** @var Categorisation $categorie */
                        foreach ($reponse->getCategories() as $i => $categorie){

                            if($i > 0){
                                $valeur = $valeur."; ";
                            }

                            $valeur = $valeur.$categorie->getCategorie();
                        }
                    }else{
                        $valeur = "Pas de catégorie affectée";
                    }
                }else{
                    /** @var ReponsesFourniesIndividuellesFerme $valeur */
                    $valeur = $reponse->getValeur();
                }

                $column = $maps->get($reponse->getQuestions()->getOrdre());
                $row = $key2+2;

                $sheet->setCellValueExplicitByColumnAndRow($column, $row,$valeur,DataType::TYPE_STRING);
            }
        }

        $sheet->setAutoFilterByColumnAndRow(1,1,$key+2,1);

        $writer = new Xlsx($spreadsheet);

        $fileName = 'Questionnaires.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        $writer->save($temp_file);
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);

    }
}
