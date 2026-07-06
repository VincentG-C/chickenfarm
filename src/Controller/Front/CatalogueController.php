<?php

declare(strict_types=1);

namespace App\Controller\Front;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/catalogue')]
class CatalogueController extends AbstractController
{
    #[Route('', name: 'app_catalogue')]
    public function index(Request $request, ProduitRepository $produitRepository): Response
    {
        $type = $request->query->get('type');
        $produits = $type
            ? $produitRepository->findByType($type)
            : $produitRepository->findAllOrdered();

        return $this->render('front/catalogue/index.html.twig', [
            'produits' => $produits,
            'type' => $type,
        ]);
    }

    #[Route('/{id}', name: 'app_catalogue_detail')]
    public function detail(Produit $produit): Response
    {
        return $this->render('front/catalogue/detail.html.twig', [
            'produit' => $produit,
        ]);
    }
}
