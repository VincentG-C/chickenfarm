<?php

declare(strict_types=1);

namespace App\Controller\Front;

use App\Entity\Client;
use App\Entity\Panier;
use App\Entity\PanierProduit;
use App\Entity\Produit;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use App\Security\Voter\PanierVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/panier')]
#[IsGranted('ROLE_USER')]
class PanierController extends AbstractController
{
    #[Route('', name: 'app_panier')]
    public function index(PanierRepository $panierRepository): Response
    {
        /** @var Client $client */
        $client = $this->getUser();
        $panier = $panierRepository->findOneByClient($client);

        return $this->render('front/panier/index.html.twig', [
            'panier' => $panier,
        ]);
    }

    #[Route('/ajouter/{id}', name: 'app_panier_ajouter', methods: ['POST'])]
    public function ajouter(
        Produit $produit,
        Request $request,
        PanierRepository $panierRepository,
        EntityManagerInterface $em,
    ): Response {
        /** @var Client $client */
        $client = $this->getUser();

        $quantite = $request->request->getInt('quantite', 1);

        $panier = $panierRepository->findOneByClient($client);
        if ($panier === null) {
            $panier = new Panier();
            $panier->setClient($client);
            $em->persist($panier);
        }

        $existing = null;
        foreach ($panier->getProduits() as $item) {
            if ($item->getProduit()->getId() === $produit->getId()) {
                $existing = $item;
                break;
            }
        }

        if ($existing !== null) {
            $existing->setQuantite($existing->getQuantite() + $quantite);
        } else {
            $panierProduit = new PanierProduit();
            $panierProduit->setPanier($panier);
            $panierProduit->setProduit($produit);
            $panierProduit->setQuantite($quantite);
            $em->persist($panierProduit);
        }

        $em->flush();

        $this->addFlash('success', 'Produit ajouté au panier.');

        return $this->redirectToRoute('app_panier');
    }

    #[Route('/supprimer/{id}', name: 'app_panier_supprimer', methods: ['POST'])]
    #[IsGranted(PanierVoter::DELETE, subject: 'panierProduit')]
    public function supprimer(
        PanierProduit $panierProduit,
        EntityManagerInterface $em,
    ): Response {
        $em->remove($panierProduit);
        $em->flush();

        $this->addFlash('success', 'Produit retiré du panier.');

        return $this->redirectToRoute('app_panier');
    }

    #[Route('/vider', name: 'app_panier_vider', methods: ['POST'])]
    public function vider(
        PanierRepository $panierRepository,
        EntityManagerInterface $em,
    ): Response {
        /** @var Client $client */
        $client = $this->getUser();
        $panier = $panierRepository->findOneByClient($client);

        if ($panier !== null) {
            foreach ($panier->getProduits() as $item) {
                $em->remove($item);
            }
            $em->remove($panier);
            $em->flush();
        }

        $this->addFlash('success', 'Panier vidé.');

        return $this->redirectToRoute('app_panier');
    }
}
