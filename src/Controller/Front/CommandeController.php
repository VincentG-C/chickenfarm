<?php

declare(strict_types=1);

namespace App\Controller\Front;

use App\Entity\Client;
use App\Entity\Commande;
use App\Entity\CommandeDetail;
use App\Repository\CommandeRepository;
use App\Repository\PanierRepository;
use App\Security\Voter\CommandeVoter;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/commandes')]
#[IsGranted('ROLE_USER')]
class CommandeController extends AbstractController
{
    #[Route('', name: 'app_commandes')]
    public function index(CommandeRepository $commandeRepository): Response
    {
        /** @var Client $client */
        $client = $this->getUser();

        return $this->render('front/commande/index.html.twig', [
            'commandes' => $commandeRepository->findByClientOrdered($client),
        ]);
    }

    #[Route('/{id}', name: 'app_commande_detail', requirements: ['id' => '\d+'])]
    #[IsGranted(CommandeVoter::VIEW, subject: 'commande')]
    public function detail(Commande $commande): Response
    {
        return $this->render('front/commande/detail.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/valider-panier', name: 'app_commande_valider')]
    public function validerPanier(
        PanierRepository $panierRepository,
        EntityManagerInterface $em,
        MailerService $mailerService,
    ): Response {
        /** @var Client $client */
        $client = $this->getUser();
        $panier = $panierRepository->findOneByClient($client);

        if ($panier === null || $panier->getProduits()->isEmpty()) {
            $this->addFlash('error', 'Votre panier est vide.');

            return $this->redirectToRoute('app_panier');
        }

        $commande = new Commande();
        $commande->setClient($client);

        $reference = 'CMD-' . strtoupper(bin2hex(random_bytes(6)));
        $commande->setReference($reference);

        $montantTotal = 0.0;

        foreach ($panier->getProduits() as $item) {
            $detail = new CommandeDetail();
            $detail->setCommande($commande);
            $detail->setProduit($item->getProduit());
            $detail->setQuantite($item->getQuantite());
            $detail->setPrixUnitaire($item->getProduit()->getPrix());
            $em->persist($detail);

            $montantTotal += (float) $item->getProduit()->getPrix() * $item->getQuantite();
        }

        $commande->setMontantTotal((string) $montantTotal);
        $em->persist($commande);

        // Vider le panier
        foreach ($panier->getProduits() as $item) {
            $em->remove($item);
        }
        $em->remove($panier);

        $em->flush();

        $mailerService->sendOrderConfirmation($commande);

        $this->addFlash('success', 'Commande validée avec succès !');

        return $this->redirectToRoute('app_commande_detail', ['id' => $commande->getId()]);
    }
}
