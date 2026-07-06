<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Nourriture;
use App\Entity\Oeuf;
use App\Entity\Produit;
use App\Entity\Ticket;
use App\Entity\Viande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom *',
                'attr' => ['placeholder' => 'ex: Œufs fermiers bio'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['rows' => 3, 'placeholder' => 'Description du produit...'],
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Prix (€) *',
                'scale' => 2,
                'html5' => true,
                'attr' => ['step' => '0.01', 'min' => '0'],
            ])
            ->add('prixUnite', TextType::class, [
                'label' => 'Unité de prix',
                'required' => false,
                'attr' => ['placeholder' => 'ex: la pièce, le kg, le lot de 6'],
            ])
            ->add('image', TextType::class, [
                'label' => 'URL de l\'image',
                'required' => false,
            ])
            ->add('quantiteStock', IntegerType::class, [
                'label' => 'Quantité en stock',
                'required' => false,
                'mapped' => false,
                'attr' => ['min' => 0],
            ])
            ->add('seuilAlerte', IntegerType::class, [
                'label' => 'Seuil d\'alerte',
                'required' => false,
                'mapped' => false,
                'attr' => ['min' => 0],
            ])
        ;

        // Champs dynamiques selon le type via PRE_SET_DATA et PRE_SUBMIT
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            /** @var Produit|null $produit */
            $produit = $event->getData();
            $form = $event->getForm();

            if (null === $produit) {
                // Nouveau produit : ajouter le champ type
                $this->addTypeField($form);
                return;
            }

            // Produit existant : ajouter les champs spécifiques selon la classe
            $this->addTypeSpecificFields($form, $produit);
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            $data = $event->getData();
            $form = $event->getForm();

            $type = $data['type'] ?? 'produit';

            // Ajouter le champ type (lecture seule en édition)
            $form->add('type', ChoiceType::class, [
                'label' => 'Type *',
                'mapped' => false,
                'choices' => [
                    'Produit simple' => 'produit',
                    'Œuf' => 'oeuf',
                    'Viande' => 'viande',
                    'Ticket visite' => 'ticket',
                    'Nourriture' => 'nourriture',
                ],
                'data' => $type,
            ]);

            $this->addTypeFieldsByType($form, $type);
        });
    }

    private function addTypeField(FormInterface $form): void
    {
        $form->add('type', ChoiceType::class, [
            'label' => 'Type *',
            'mapped' => false,
            'choices' => [
                'Produit simple' => 'produit',
                'Œuf' => 'oeuf',
                'Viande' => 'viande',
                'Ticket visite' => 'ticket',
                'Nourriture' => 'nourriture',
            ],
            'placeholder' => 'Sélectionnez un type',
        ]);
    }

    private function addTypeSpecificFields(FormInterface $form, Produit $produit): void
    {
        $type = match ($produit::class) {
            Oeuf::class => 'oeuf',
            Viande::class => 'viande',
            Ticket::class => 'ticket',
            Nourriture::class => 'nourriture',
            default => 'produit',
        };

        $form->add('type', ChoiceType::class, [
            'label' => 'Type *',
            'mapped' => false,
            'disabled' => true,
            'choices' => [
                'Produit simple' => 'produit',
                'Œuf' => 'oeuf',
                'Viande' => 'viande',
                'Ticket visite' => 'ticket',
                'Nourriture' => 'nourriture',
            ],
            'data' => $type,
        ]);

        match ($type) {
            'oeuf' => $this->addOeufFields($form),
            'viande' => $this->addViandeFields($form),
            'ticket' => $this->addTicketFields($form),
            'nourriture' => $this->addNourritureFields($form),
            default => null,
        };
    }

    private function addTypeFieldsByType(FormInterface $form, string $type): void
    {
        match ($type) {
            'oeuf' => $this->addOeufFields($form),
            'viande' => $this->addViandeFields($form),
            'ticket' => $this->addTicketFields($form),
            'nourriture' => $this->addNourritureFields($form),
            default => null,
        };
    }

    private function addOeufFields(FormInterface $form): void
    {
        $form
            ->add('calibre', ChoiceType::class, [
                'label' => 'Calibre',
                'choices' => [
                    'Petit' => 'petit',
                    'Moyen' => 'moyen',
                    'Gros' => 'gros',
                    'Extra gros' => 'extra_gros',
                ],
                'placeholder' => 'Sélectionnez un calibre',
            ])
            ->add('estFeconde', CheckboxType::class, [
                'label' => 'Œuf fécondé',
                'required' => false,
            ])
            ->add('datePonte', DateType::class, [
                'label' => 'Date de ponte',
                'widget' => 'single_text',
                'required' => false,
            ])
        ;
    }

    private function addViandeFields(FormInterface $form): void
    {
        $form
            ->add('poidsMoyen', NumberType::class, [
                'label' => 'Poids moyen (kg)',
                'scale' => 2,
                'attr' => ['step' => '0.01', 'min' => '0'],
            ])
            ->add('typeDecoupe', ChoiceType::class, [
                'label' => 'Type de découpe',
                'choices' => [
                    'Entier' => 'entier',
                    'Filet' => 'filet',
                    'Cuisse' => 'cuisse',
                    'Aile' => 'aile',
                    'Blanc' => 'blanc',
                ],
                'placeholder' => 'Sélectionnez un type de découpe',
            ])
        ;
    }

    private function addTicketFields(FormInterface $form): void
    {
        $form
            ->add('dateVisite', DateType::class, [
                'label' => 'Date de visite',
                'widget' => 'single_text',
            ])
            ->add('nbPlaces', IntegerType::class, [
                'label' => 'Nombre de places',
                'attr' => ['min' => 1],
            ])
            ->add('dureeVisite', IntegerType::class, [
                'label' => 'Durée de visite (minutes)',
                'attr' => ['min' => 15],
            ])
        ;
    }

    private function addNourritureFields(FormInterface $form): void
    {
        $form
            ->add('typeAliment', ChoiceType::class, [
                'label' => "Type d'aliment",
                'choices' => [
                    'Granulés' => 'granules',
                    'Graines' => 'graines',
                    'Vitamines' => 'vitamines',
                    'Calcium' => 'calcium',
                    'Autre' => 'autre',
                ],
                'placeholder' => "Sélectionnez un type d'aliment",
            ])
            ->add('composition', TextareaType::class, [
                'label' => 'Composition',
                'required' => false,
                'attr' => ['rows' => 3, 'placeholder' => 'Ingrédients, valeurs nutritionnelles...'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
            'csrf_token_id' => 'produit_form',
        ]);
    }
}
