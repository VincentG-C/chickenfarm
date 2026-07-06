<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Client;
use App\Entity\Panier;
use App\Entity\PanierProduit;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;

/** @extends Voter<string, Panier|PanierProduit> */
class PanierVoter extends Voter
{
    public const VIEW = 'PANIER_VIEW';
    public const DELETE = 'PANIER_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::DELETE], true)
            && ($subject instanceof Panier || $subject instanceof PanierProduit);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        if (!$user instanceof Client) {
            return false;
        }

        // Extraire le panier depuis un PanierProduit si nécessaire
        if ($subject instanceof PanierProduit) {
            $panier = $subject->getPanier();
        } else {
            $panier = $subject;
        }

        return match ($attribute) {
            self::VIEW, self::DELETE => $this->isOwner($panier, $user),
            default => false,
        };
    }

    private function isOwner(Panier $panier, Client $user): bool
    {
        return $panier->getClient()?->getId() === $user->getId();
    }
}
