<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\CommandeRepository;
use App\Repository\EnclosRepository;
use App\Repository\JournalFermeRepository;
use App\Repository\ProduitRepository;
use App\Repository\StockRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class HomeController extends AbstractController
{
    #[Route('', name: 'app_admin_home')]
    public function index(
        UserRepository $userRepository,
        CommandeRepository $commandeRepository,
        EnclosRepository $enclosRepository,
        StockRepository $stockRepository,
        JournalFermeRepository $journalFermeRepository,
        ProduitRepository $produitRepository,
    ): Response {
        return $this->render('admin/home/index.html.twig', [
            'totalClients' => $userRepository->countByRole('ROLE_USER'),
            'totalCommandes' => $commandeRepository->countAll(),
            'commandesRecentes' => $commandeRepository->findRecentForDashboard(10),
            'totalEnclos' => $enclosRepository->countAll(),
            'totalProduits' => $produitRepository->countAll(),
            'stocksFaibles' => $stockRepository->findBelowThreshold(),
            'journalRecent' => $journalFermeRepository->findRecent(10),
        ]);
    }
}
