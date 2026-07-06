<?php

declare(strict_types=1);

namespace App\Controller\Front;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('front/home/index.html.twig', [
            'produits' => $produitRepository->findAllOrdered(),
        ]);
    }
}
