<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\Writer;
use Scn\DeeplApiConnector\DeeplClient as DeepClient;
use Scn\DeeplApiConnector\Model\TranslationConfig;
use Scn\DeeplApiConnector\Enum\LanguageEnum;

class IndexController extends AbstractController
{

    /**
     * @var string
     */
    private $deeplKey;

    public function __construct(string $deeplKey)
    {

        $this->deeplKey = $deeplKey;
    }

    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $deepl = DeepClient::create($this->deeplKey);

        $fileDir = $this->getParameter('kernel.project_dir').'/files/';
        $csv = Reader::createFromPath($fileDir.'input.csv', 'r');
        $csv->setDelimiter(';');
        $csv->setHeaderOffset(0);

        $header = $csv->getHeader(); //returns the CSV header record
        $records = Statement::create()->process($csv);

        //NEW csv file:
        $fileName = 'translated'.date('Y-m-d-H-i-s').'.csv';
        $writer = Writer::createFromPath($fileDir.$fileName, 'w+');
        $writer->setDelimiter(';');
        $writer->setEnclosure('"');
        $writer->insertOne($header);

        //$deepl = DeepClient::create('e8273a42-1765-d437-1143-77ea4c6eeb4c');

        $i=0;
        foreach ($records as $record){
            $i++;
            $newRecord = [];
            $newRecord['Artikel_ID'] = $record['Artikel_ID'];
            $newRecord['Intern_ID'] = $record['Intern_ID'];
            if(trim($record['Bezeichnung']) != ''){
                $newRecord['Bezeichnung'] = $this->translate($deepl, $record['Bezeichnung']);
            } else {
                $newRecord['Bezeichnung'] = '';
            }

            if(trim($record['Beschreibung']) != ''){
                $newRecord['Beschreibung'] = $this->translate($deepl, $record['Beschreibung']);
            } else {
                $newRecord['Beschreibung'] = '';
            }

            if(trim($record['Beschreibung2']) != ''){
                $newRecord['Beschreibung2'] = $this->translate($deepl, $record['Beschreibung2']);
            } else {
                $newRecord['Beschreibung2'] = '';
            }

            if(trim($record['Anmerkung']) != ''){
                $newRecord['Anmerkung'] = $this->translate($deepl, $record['Anmerkung']);
            } else {
                $newRecord['Anmerkung'] = '';
            }

            $writer->insertOne($newRecord);
            if($i >= 1){
                break;
            }
        }

        return $this->render('index/index.html.twig', [
            'header' => $header,
            'records' => $records,
            'count' => $i,
            'filename' => $fileName
        ]);

    }


    public function translate($deepl, $text)
    {
        $translation = new TranslationConfig(
            $text,
            LanguageEnum::LANGUAGE_EN,
            LanguageEnum::LANGUAGE_DE
        );

        /*
        $trans = $deepl->getTranslation($translation);
        dump($trans->getText());
        */
        return $deepl->getTranslation($translation)->getText();
    }
}
