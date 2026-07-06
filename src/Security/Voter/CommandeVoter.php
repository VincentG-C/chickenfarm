<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Client;
use App\Entity\Commande;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;

/** @extends Voter<string, Commande> */
class CommandeVoter extends Voter
{
    public const VIEW = 'COMMANDE_VIEW';
    public const EDIT = 'COMMANDE_EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT], true) && $subject instanceof Commande;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        // Non connecté
        if (!$user instanceof Client) {
            return false;
        }

        /** @var Commande $commande */
        $commande = $subject;

        return match ($attribute) {
            self::VIEW => $this->canView($commande, $user),
            self::EDIT => $this->canEdit($commande, $user),
            default => false,
        };
    }

    private function canView(Commande $commande, Client $user): bool
    {
        // Le propriétaire de la commande peut voir
        if ($commande->getClient()?->getId() === $user->getId()) {
            return true;
        }

        // Les admins/managers peuvent voir toutes les commandes
        if (in_array('ROLE_ADMIN', $user->getRoles(), true) || in_array('ROLE_MANAGER', $user->getRoles(), true)) {
            return true;
        }

        return false;
    }

    private function canEdit(Commande $commande, Client $user): bool
    {
        // Seuls les admins peuvent modifier une commande
        return in_array('ROLE_ADMIN', $user->getRoles(), true);
    }
}
