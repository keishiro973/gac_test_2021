<?php

namespace App\Controller;

use App\Repository\ExpenseRepository;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(ExpenseRepository $expenseRepository): Response
    {
        $now = new \DateTime();
        $lastMonth = new \DateTime('last month');
        $totalTe = $expenseRepository->totalTeBetween($lastMonth, $now);
        $totalTi = $expenseRepository->totalTiBetween($lastMonth, $now);
        $byCategory = $expenseRepository->sumByCategory($lastMonth, $now);
        $top10Vehicle = $expenseRepository->top10Vehicle($lastMonth, $now);

        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'totalTe' => $totalTe[0],
            'totalTi' => $totalTi[0]                        ,
            'byCategory' => $byCategory,
            'top10Vehicle' => $top10Vehicle,
        ]);
    }
}
