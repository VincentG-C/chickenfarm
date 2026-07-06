<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Nourriture;
use App\Entity\Oeuf;
use App\Entity\Produit;
use App\Entity\Stock;
use App\Entity\Ticket;
use App\Entity\Viande;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/produits')]
#[IsGranted('ROLE_ADMIN')]
class ProduitController extends AbstractController
{
    #[Route('', name: 'app_produit_index')]
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('admin/produit/index.html.twig', [
            'produits' => $produitRepository->findAllOrdered(),
        ]);
    }

    #[Route('/nouveau', name: 'app_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $produit = null;
        $form = $this->createForm(ProduitType::class, null, [
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $type = $form->get('type')->getData();
            $quantiteStock = $form->get('quantiteStock')->getData() ?? 0;
            $seuilAlerte = $form->get('seuilAlerte')->getData() ?? 10;

            // Créer l'instance du bon type selon le choix
            $produit = match ($type) {
                'oeuf' => new Oeuf(),
                'viande' => new Viande(),
                'ticket' => new Ticket(),
                'nourriture' => new Nourriture(),
                default => new Produit(),
            };

            // Copier les champs communs du formulaire vers l'entité
            $produit->setNom($formData->getNom());
            $produit->setDescription($formData->getDescription());
            $produit->setPrix((string) $formData->getPrix());
            $produit->setPrixUnite($formData->getPrixUnite());
            $produit->setImage($formData->getImage());

            // Copier les champs spécifiques selon le type
            $this->applyTypeSpecificFields($produit, $form);

            // Créer le stock
            $stock = new Stock();
            $stock->setProduit($produit);
            $stock->setQuantiteDisponible((int) $quantiteStock);
            $stock->setSeuilAlerte((int) $seuilAlerte);

            $em->persist($produit);
            $em->persist($stock);
            $em->flush();

            $this->addFlash('success', 'Produit créé avec succès.');
            return $this->redirectToRoute('app_produit_index');
        }

        return $this->render('admin/produit/form.html.twig', [
            'produit' => null,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Produit $produit, Request $request, EntityManagerInterface $em): Response
    {
        // Préremplir les champs non-mapped depuis le stock
        $form = $this->createForm(ProduitType::class, $produit, [
            'method' => 'POST',
        ]);

        // Initialiser les champs non-mapped avec les valeurs du stock
        if ($produit->getStock()) {
            $form->get('quantiteStock')->setData($produit->getStock()->getQuantiteDisponible());
            $form->get('seuilAlerte')->setData($produit->getStock()->getSeuilAlerte());
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $quantiteStock = $form->get('quantiteStock')->getData();
            $seuilAlerte = $form->get('seuilAlerte')->getData();

            // Copier les champs spécifiques selon le type
            $this->applyTypeSpecificFields($produit, $form);

            // Mettre à jour le stock
            if ($produit->getStock()) {
                $produit->getStock()->setQuantiteDisponible((int) $quantiteStock);
                $produit->getStock()->setSeuilAlerte((int) $seuilAlerte);
            }

            $em->flush();

            $this->addFlash('success', 'Produit modifié avec succès.');
            return $this->redirectToRoute('app_produit_index');
        }

        return $this->render('admin/produit/form.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/supprimer', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(Produit $produit, EntityManagerInterface $em): Response
    {
        $em->remove($produit);
        $em->flush();

        $this->addFlash('success', 'Produit supprimé.');
        return $this->redirectToRoute('app_produit_index');
    }

    /**
     * Applique les champs spécifiques du formulaire à l'entité produit.
     */
    private function applyTypeSpecificFields(Produit $produit, FormInterface $form): void
    {

        if ($produit instanceof Oeuf) {
            $produit->setCalibre($form->get('calibre')->getData() ?? 'moyen');
            $produit->setEstFeconde((bool) $form->get('estFeconde')->getData());
            if ($form->has('datePonte') && $form->get('datePonte')->getData()) {
                $produit->setDatePonte($form->get('datePonte')->getData());
            }
        }

        if ($produit instanceof Viande) {
            $produit->setPoidsMoyen((string) ($form->get('poidsMoyen')->getData() ?? '1.0'));
            $produit->setTypeDecoupe($form->get('typeDecoupe')->getData() ?? 'entier');
        }

        if ($produit instanceof Ticket) {
            $produit->setDateVisite($form->get('dateVisite')->getData());
            $produit->setNbPlaces((int) ($form->get('nbPlaces')->getData() ?? 1));
            $produit->setNbPlacesRestantes((int) ($form->get('nbPlaces')->getData() ?? 1));
            $produit->setDureeVisite((int) ($form->get('dureeVisite')->getData() ?? 60));
        }

        if ($produit instanceof Nourriture) {
            $produit->setTypeAliment($form->get('typeAliment')->getData() ?? 'granules');
            $produit->setComposition($form->get('composition')->getData());
        }
    }
}
