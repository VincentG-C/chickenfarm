<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class AdminUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', TextType::class, ['label' => 'Prénom'])
            ->add('nom', TextType::class, ['label' => 'Nom'])
            ->add('email', EmailType::class, ['label' => 'Adresse e-mail'])
        ;

        // Champ rôle dynamique selon si création ou édition
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            /** @var User|null $user */
            $user = $event->getData();
            $form = $event->getForm();

            $isNew = null === $user || null === $user->getId();

            // Ajouter le champ rôle
            $rolesChoices = [
                'Voyageur (Client)' => 'ROLE_USER',
                'Hôte (Fermier)' => 'ROLE_HOST',
                'Administrateur' => 'ROLE_ADMIN',
                'Super Admin' => 'ROLE_SUPER_ADMIN',
            ];

            $form->add('role', ChoiceType::class, [
                'label' => 'Rôle',
                'mapped' => false,
                'choices' => $rolesChoices,
                'data' => $isNew ? 'ROLE_USER' : ($user->getRoles()[0] ?? 'ROLE_USER'),
            ]);

            // Champ mot de passe seulement pour les nouveaux utilisateurs
            $form->add('plainPassword', PasswordType::class, [
                'label' => $isNew ? 'Mot de passe *' : 'Nouveau mot de passe (laisser vide pour conserver)',
                'mapped' => false,
                'required' => $isNew,
                'constraints' => $isNew ? [
                    new Length(min: 8, minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.'),
                ] : [],
            ]);
        });

        // PRE_SUBMIT pour gérer le rôle
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            $data = $event->getData();
            $form = $event->getForm();

            $form->add('role', ChoiceType::class, [
                'label' => 'Rôle',
                'mapped' => false,
                'choices' => [
                    'Voyageur (Client)' => 'ROLE_USER',
                    'Hôte (Fermier)' => 'ROLE_HOST',
                    'Administrateur' => 'ROLE_ADMIN',
                    'Super Admin' => 'ROLE_SUPER_ADMIN',
                ],
                'data' => $data['role'] ?? 'ROLE_USER',
            ]);

            $isNew = !isset($data['id']) || empty($data['id']);
            $form->add('plainPassword', PasswordType::class, [
                'label' => $isNew ? 'Mot de passe *' : 'Nouveau mot de passe (laisser vide pour conserver)',
                'mapped' => false,
                'required' => $isNew,
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_token_id' => 'admin_user_form',
        ]);
    }
}
