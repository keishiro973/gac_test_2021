<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImportController
{
    /**
     * @Route("/", name="app_import_csv")
     */
    public function importCsv(Response $response, Request $request)
    {
        return $response;
    }
}