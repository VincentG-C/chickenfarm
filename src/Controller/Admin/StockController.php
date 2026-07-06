<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Stock;
use App\Repository\StockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/stocks')]
#[IsGranted('ROLE_ADMIN')]
class StockController extends AbstractController
{
    #[Route('', name: 'app_stock_index')]
    public function index(StockRepository $stockRepository): Response
    {
        return $this->render('admin/stock/index.html.twig', [
            'stocks' => $stockRepository->findAll(),
            'alertes' => $stockRepository->findBelowThreshold(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_stock_edit', methods: ['POST'])]
    public function edit(Stock $stock, Request $request, EntityManagerInterface $em): Response
    {
        $quantite = $request->request->getInt('quantite');
        $stock->setQuantiteDisponible($quantite);
        $em->flush();

        $this->addFlash('success', 'Stock mis à jour.');

        return $this->redirectToRoute('app_stock_index');
    }
}
