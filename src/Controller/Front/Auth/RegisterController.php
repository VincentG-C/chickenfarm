<?php

declare(strict_types=1);

namespace App\Controller\Front\Auth;

use App\Entity\Client;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository,
        MailerService $mailerService,
    ): Response {
        if ($this->getUser() instanceof Client) {
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(RegistrationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $plainPassword = $form->get('plainPassword')->getData();

            if ($userRepository->findOneBy(['email' => $data['email']]) !== null) {
                $form->get('email')->addError(new FormError('Cette adresse e-mail est déjà utilisée.'));

                return $this->render('front/auth/register.html.twig', [
                    'form' => $form,
                ]);
            }

            $client = new Client();
            $client->setEmail($data['email']);
            $client->setPasswordHash($passwordHasher->hashPassword($client, $plainPassword));
            $client->setPrenom($data['firstName']);
            $client->setNom($data['lastName']);
            $client->setAssignedRoles(['ROLE_USER']);

            $entityManager->persist($client);
            $entityManager->flush();

            $mailerService->sendWelcomeEmail($client);

            $this->addFlash('success', 'Compte créé avec succès ! Vous pouvez maintenant commander nos produits.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('front/auth/register.html.twig', [
            'form' => $form,
        ]);
    }
}
