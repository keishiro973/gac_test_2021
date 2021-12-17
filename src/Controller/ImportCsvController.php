<?php

namespace App\Controller;

use App\Form\CsvImportType;
use App\Service\ImportCsv;
use App\Service\UploadFile;
use PHP_CodeSniffer\Reports\Csv;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImportCsvController extends AbstractController
{
    /**
     * @Route("/import", name="import_csv")
     */
    public function index(Request $request, UploadFile $uploadFile, ImportCsv $importCsv): Response
    {
        $form = $this->createForm(CsvImportType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $csvFile = $form->get('file')->getData();

            if ($csvFile) {
                $newFileName = $uploadFile->upload($csvFile);
                $importCsv->import($uploadFile->getTargetDirectory().'/'.$newFileName);
            }
        }
        return $this->render('import_csv/index.html.twig', [
            'controller_name' => 'ImportCsvController',
            'form' => $form->createView(),
        ]);
    }
}
