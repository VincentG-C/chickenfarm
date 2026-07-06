<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/commandes')]
#[IsGranted('ROLE_ADMIN')]
class OrderController extends AbstractController
{
    #[Route('', name: 'app_order_index')]
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('admin/order/index.html.twig', [
            'commandes' => $commandeRepository->findAllOrdered(),
        ]);
    }

    #[Route('/{id}', name: 'app_order_show')]
    public function show(CommandeRepository $commandeRepository, int $id): Response
    {
        $commande = $commandeRepository->findWithDetails($id);

        if ($commande === null) {
            throw $this->createNotFoundException('Commande introuvable.');
        }

        return $this->render('admin/order/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{id}/statut', name: 'app_order_statut', methods: ['POST'])]
    public function updateStatut(
        Commande $commande,
        Request $request,
        EntityManagerInterface $em,
        MailerService $mailerService,
    ): Response {
        $statut = $request->request->get('statut');
        $statutsValides = ['en_attente', 'confirmee', 'en_preparation', 'expediee', 'livree', 'annulee'];

        if (in_array($statut, $statutsValides, true)) {
            $commande->setStatut($statut);
            $em->flush();

            $mailerService->sendOrderStatusUpdate($commande);

            $this->addFlash('success', 'Statut de la commande mis à jour.');
        }

        return $this->redirectToRoute('app_order_show', ['id' => $commande->getId()]);
    }
}
