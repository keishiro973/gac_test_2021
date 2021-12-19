<?php

namespace App\Controller;

use App\Form\DashboardSearchType;
use App\Repository\ExpenseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(Request $request, ExpenseRepository $expenseRepository): Response
    {
        $startDate = new \DateTime('last month');
        $endDate = new \DateTime();

        $form = $this->createForm(DashboardSearchType::class, [
            'BeginDate' => $startDate,
            'endDate' => $endDate,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formDatas = $form->getData();
            $startDate = $formDatas['BeginDate'];
            $endDate = $formDatas['endDate'];
        }

        $totalTe = $expenseRepository->totalTeBetween($startDate, $endDate);
        $totalTi = $expenseRepository->totalTiBetween($startDate, $endDate);
        $byCategory = $expenseRepository->sumByCategory($startDate, $endDate);
        $top10Vehicle = $expenseRepository->top10Vehicle($startDate, $endDate);

        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'totalTe' => $totalTe[0],
            'totalTi' => $totalTi[0],
            'byCategory' => $byCategory,
            'top10Vehicle' => $top10Vehicle,
            'form' => $form->createView(),
        ]);
    }
}
