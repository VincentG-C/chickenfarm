<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Commande;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailerService
{
    private const FROM_EMAIL = 'ferme-des-poulettes@example.com';
    private const FROM_NAME = 'Ferme des Poulettes';

    public function __construct(
        private readonly MailerInterface $mailer,
    ) {
    }

    public function sendWelcomeEmail(User $user): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address(self::FROM_EMAIL, self::FROM_NAME))
            ->to(new Address($user->getEmail(), $user->getPrenom() . ' ' . $user->getNom()))
            ->subject('Bienvenue à la Ferme des Poulettes !')
            ->htmlTemplate('emails/welcome.html.twig')
            ->context([
                'user' => $user,
            ]);

        $this->mailer->send($email);
    }

    public function sendOrderConfirmation(Commande $commande): void
    {
        $client = $commande->getClient();
        if ($client === null) {
            return;
        }

        $email = (new TemplatedEmail())
            ->from(new Address(self::FROM_EMAIL, self::FROM_NAME))
            ->to(new Address($client->getEmail(), $client->getPrenom() . ' ' . $client->getNom()))
            ->subject('Confirmation de commande n°' . $commande->getReference())
            ->htmlTemplate('emails/order_confirmation.html.twig')
            ->context([
                'commande' => $commande,
                'client' => $client,
            ]);

        $this->mailer->send($email);
    }

    public function sendOrderStatusUpdate(Commande $commande): void
    {
        $client = $commande->getClient();
        if ($client === null) {
            return;
        }

        $statutLabels = [
            'en_attente' => 'En attente',
            'confirmee' => 'Confirmée',
            'en_preparation' => 'En préparation',
            'expediee' => 'Expédiée',
            'livree' => 'Livrée',
            'annulee' => 'Annulée',
        ];

        $statutLabel = $statutLabels[$commande->getStatut()] ?? $commande->getStatut();

        $email = (new TemplatedEmail())
            ->from(new Address(self::FROM_EMAIL, self::FROM_NAME))
            ->to(new Address($client->getEmail(), $client->getPrenom() . ' ' . $client->getNom()))
            ->subject('Votre commande ' . $commande->getReference() . ' est ' . $statutLabel)
            ->htmlTemplate('emails/order_status.html.twig')
            ->context([
                'commande' => $commande,
                'client' => $client,
                'statutLabel' => $statutLabel,
            ]);

        $this->mailer->send($email);
    }
}
