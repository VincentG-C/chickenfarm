<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Entity\Client;
use App\Entity\Commande;
use App\Entity\Panier;
use App\Entity\PanierProduit;
use App\Entity\Produit;
use App\Entity\Stock;
use App\Repository\CommandeRepository;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use App\Repository\StockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/v1')]
class ApiController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
    ) {
    }

    // ─── PRODUITS ───────────────────────────────────────────────

    #[Route('/produits', name: 'api_produits_list', methods: ['GET'])]
    public function listProduits(ProduitRepository $produitRepository): JsonResponse
    {
        $produits = $produitRepository->findAll();

        return $this->jsonResponse($produits, ['api:produit:read']);
    }

    #[Route('/produits/{id}', name: 'api_produits_show', methods: ['GET'])]
    public function showProduit(Produit $produit): JsonResponse
    {
        return $this->jsonResponse($produit, ['api:produit:read']);
    }

    #[Route('/produits', name: 'api_produits_create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function createProduit(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $produit = $this->deserialize($request, Produit::class, ['api:produit:write']);

        $stock = new Stock();
        $stock->setQuantiteDisponible(0);
        $stock->setSeuilAlerte(10);
        $produit->setStock($stock);

        $em->persist($produit);
        $em->flush();

        return $this->jsonResponse($produit, ['api:produit:read'], Response::HTTP_CREATED);
    }

    // ─── COMMANDES ──────────────────────────────────────────────

    #[Route('/commandes', name: 'api_commandes_list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function listCommandes(CommandeRepository $commandeRepository): JsonResponse
    {
        /** @var Client $client */
        $client = $this->getUser();

        if ($this->isGranted('ROLE_ADMIN')) {
            $commandes = $commandeRepository->findAll();
        } else {
            $commandes = $commandeRepository->findBy(['client' => $client]);
        }

        return $this->jsonResponse($commandes, ['api:commande:read']);
    }

    #[Route('/commandes/{id}', name: 'api_commandes_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function showCommande(Commande $commande): JsonResponse
    {
        if (!$this->isGranted('ROLE_ADMIN') && $commande->getClient() !== $this->getUser()) {
            return $this->json(['error' => 'Accès interdit'], Response::HTTP_FORBIDDEN);
        }

        return $this->jsonResponse($commande, ['api:commande:read']);
    }

    // ─── PANIER ─────────────────────────────────────────────────

    #[Route('/panier', name: 'api_panier_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function showPanier(PanierRepository $panierRepository): JsonResponse
    {
        /** @var Client $client */
        $client = $this->getUser();
        $panier = $panierRepository->findOneByClient($client);

        if ($panier === null) {
            return $this->json(['panier' => null, 'total' => 0]);
        }

        return $this->jsonResponse($panier, ['api:panier:read']);
    }

    #[Route('/panier/ajouter', name: 'api_panier_add', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function addToPanier(
        Request $request,
        PanierRepository $panierRepository,
        ProduitRepository $produitRepository,
        EntityManagerInterface $em,
    ): JsonResponse {
        /** @var Client $client */
        $client = $this->getUser();
        $data = json_decode($request->getContent(), true);

        $produitId = $data['produitId'] ?? null;
        $quantite = (int) ($data['quantite'] ?? 1);

        if ($produitId === null) {
            return $this->json(['error' => 'produitId requis'], Response::HTTP_BAD_REQUEST);
        }

        $produit = $produitRepository->find($produitId);
        if ($produit === null) {
            return $this->json(['error' => 'Produit introuvable'], Response::HTTP_NOT_FOUND);
        }

        $panier = $panierRepository->findOneByClient($client);
        if ($panier === null) {
            $panier = new Panier();
            $panier->setClient($client);
            $em->persist($panier);
        }

        $existant = null;
        foreach ($panier->getProduits() as $item) {
            if ($item->getProduit()->getId() === $produit->getId()) {
                $existant = $item;
                break;
            }
        }

        if ($existant !== null) {
            $existant->setQuantite($existant->getQuantite() + $quantite);
        } else {
            $item = new PanierProduit();
            $item->setPanier($panier);
            $item->setProduit($produit);
            $item->setQuantite($quantite);
            $em->persist($item);
        }

        $panier->setUpdatedAt(new \DateTimeImmutable());
        $em->flush();

        return $this->jsonResponse($panier, ['api:panier:read']);
    }

    // ─── STOCKS ─────────────────────────────────────────────────

    #[Route('/stocks', name: 'api_stocks_list', methods: ['GET'])]
    public function listStocks(StockRepository $stockRepository): JsonResponse
    {
        $stocks = $stockRepository->findAll();

        return $this->jsonResponse($stocks, ['api:stock:read']);
    }

    #[Route('/stocks/{id}', name: 'api_stocks_show', methods: ['GET'])]
    public function showStock(Stock $stock): JsonResponse
    {
        return $this->jsonResponse($stock, ['api:stock:read']);
    }

    // ─── UTILITAIRES ────────────────────────────────────────────

    private function jsonResponse(mixed $data, array $groups, int $status = Response::HTTP_OK): JsonResponse
    {
        $json = $this->serializer->serialize($data, 'json', [
            'groups' => $groups,
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        ]);

        return new JsonResponse($json, $status, [], true);
    }

    private function deserialize(Request $request, string $class, array $groups): object
    {
        return $this->serializer->deserialize(
            $request->getContent(),
            $class,
            'json',
            ['groups' => $groups]
        );
    }
}
