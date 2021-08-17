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
    /**
     * @var array
     */
    private $translate;

    public function __construct(string $deeplKey, array $translate)
    {

        $this->deeplKey = $deeplKey;
        $this->translate = $translate;
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
            foreach ($header as $column){
                if(in_array($column, $this->translate)){
                    if(trim($record[$column]) != ''){
                        $newRecord[$column] = $this->translate($deepl, $record[$column]);
                    } else {
                        $newRecord[$column] = '';
                    }
                }else{
                    $newRecord[$column] = $record[$column];
                }
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
