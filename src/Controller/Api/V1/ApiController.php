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
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[OA\Info(
    version: '1.0.0',
    title: 'Chicken Farm API',
    description: 'API REST de la ferme de poules — produits, commandes, panier, stocks',
)]
#[OA\Server(url: '/api/v1')]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    description: 'Authentification par session Symfony (cookie)'
)]
#[Route('/api/v1')]
class ApiController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
    ) {
    }

    // ─── PRODUITS ───────────────────────────────────────────────

    #[OA\Get(
        path: '/api/v1/produits',
        summary: 'Liste tous les produits',
        tags: ['Produits'],
    )]
    #[OA\Response(
        response: 200,
        description: 'Liste des produits',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Produit::class, groups: ['api:produit:read']))
        ),
    )]
    #[Route('/produits', name: 'api_produits_list', methods: ['GET'])]
    public function listProduits(ProduitRepository $produitRepository): JsonResponse
    {
        $produits = $produitRepository->findAll();

        return $this->jsonResponse($produits, ['api:produit:read']);
    }

    #[OA\Get(
        path: '/api/v1/produits/{id}',
        summary: 'Détail d\'un produit',
        tags: ['Produits'],
    )]
    #[OA\Response(
        response: 200,
        description: 'Produit trouvé',
        content: new OA\JsonContent(ref: new Model(type: Produit::class, groups: ['api:produit:read'])),
    )]
    #[OA\Response(response: 404, description: 'Produit non trouvé')]
    #[Route('/produits/{id}', name: 'api_produits_show', methods: ['GET'])]
    public function showProduit(Produit $produit): JsonResponse
    {
        return $this->jsonResponse($produit, ['api:produit:read']);
    }

    #[OA\Post(
        path: '/api/v1/produits',
        summary: 'Crée un nouveau produit (admin)',
        tags: ['Produits'],
    )]
    #[OA\RequestBody(
        description: 'Données du produit',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'nom', type: 'string', example: 'Œufs frais bio'),
                new OA\Property(property: 'prix', type: 'string', example: '4.50'),
                new OA\Property(property: 'description', type: 'string', example: 'Œufs de nos poules élevées en plein air.'),
            ],
            type: 'object',
        ),
    )]
    #[OA\Response(
        response: 201,
        description: 'Produit créé',
        content: new OA\JsonContent(ref: new Model(type: Produit::class, groups: ['api:produit:read'])),
    )]
    #[OA\Response(response: 403, description: 'Accès interdit (admin requis)')]
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

    #[OA\Get(
        path: '/api/v1/commandes',
        summary: 'Liste les commandes (client = ses commandes, admin = toutes)',
        tags: ['Commandes'],
    )]
    #[OA\Response(
        response: 200,
        description: 'Liste des commandes',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Commande::class, groups: ['api:commande:read'])),
        ),
    )]
    #[OA\Response(response: 401, description: 'Non authentifié')]
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

    #[OA\Get(
        path: '/api/v1/commandes/{id}',
        summary: 'Détail d\'une commande',
        tags: ['Commandes'],
    )]
    #[OA\Response(
        response: 200,
        description: 'Commande trouvée',
        content: new OA\JsonContent(ref: new Model(type: Commande::class, groups: ['api:commande:read'])),
    )]
    #[OA\Response(response: 403, description: 'Accès interdit')]
    #[OA\Response(response: 404, description: 'Commande non trouvée')]
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

    #[OA\Get(
        path: '/api/v1/panier',
        summary: 'Affiche le panier de l\'utilisateur connecté',
        tags: ['Panier'],
    )]
    #[OA\Response(
        response: 200,
        description: 'Panier de l\'utilisateur',
        content: new OA\JsonContent(ref: new Model(type: Panier::class, groups: ['api:panier:read'])),
    )]
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

    #[OA\Post(
        path: '/api/v1/panier/ajouter',
        summary: 'Ajoute un produit au panier',
        tags: ['Panier'],
    )]
    #[OA\RequestBody(
        description: 'Produit et quantité',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'produitId', type: 'integer', example: 1),
                new OA\Property(property: 'quantite', type: 'integer', example: 2),
            ],
            type: 'object',
        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'Produit ajouté au panier',
        content: new OA\JsonContent(ref: new Model(type: Panier::class, groups: ['api:panier:read'])),
    )]
    #[OA\Response(response: 400, description: 'Données invalides')]
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

    #[OA\Get(
        path: '/api/v1/stocks',
        summary: 'Liste tous les stocks',
        tags: ['Stocks'],
    )]
    #[OA\Response(
        response: 200,
        description: 'Liste des stocks',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Stock::class, groups: ['api:stock:read'])),
        ),
    )]
    #[Route('/stocks', name: 'api_stocks_list', methods: ['GET'])]
    public function listStocks(StockRepository $stockRepository): JsonResponse
    {
        $stocks = $stockRepository->findAll();

        return $this->jsonResponse($stocks, ['api:stock:read']);
    }

    #[OA\Get(
        path: '/api/v1/stocks/{id}',
        summary: 'Détail d\'un stock',
        tags: ['Stocks'],
    )]
    #[OA\Response(
        response: 200,
        description: 'Stock trouvé',
        content: new OA\JsonContent(ref: new Model(type: Stock::class, groups: ['api:stock:read'])),
    )]
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
