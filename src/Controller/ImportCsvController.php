<?php

namespace App\Controller;

use App\Form\CsvImportType;
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
     * @Route("/import/csv", name="import_csv")
     */
    public function index(UploadedFile $file, Request $request, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(CsvImportType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $csvFile = $form->get('file')->getData();

            if ($csvFile) {
                $originalFileName = pathinfo($csvFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $safeFileName.'-'.uniqid().$csvFile->gessExtension();
            }
        }
        return $this->render('import_csv/index.html.twig', [
            'controller_name' => 'ImportCsvController',
        ]);
    }
}
