<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Enclos;
use App\Entity\Gallinace;
use App\Entity\Poule;
use App\Entity\Couveuse;
use App\Entity\Incubation;
use App\Entity\JournalFerme;
use App\Entity\Naissance;
use App\Entity\Manager;
use App\Repository\EnclosRepository;
use App\Repository\CouveuseRepository;
use App\Repository\JournalFermeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/manager')]
#[IsGranted('ROLE_MANAGER')]
class FermeController extends AbstractController
{
    #[Route('/enclos', name: 'app_manager_enclos')]
    public function enclosIndex(EnclosRepository $enclosRepository): Response
    {
        return $this->render('admin/ferme/enclos/index.html.twig', [
            'enclos' => $enclosRepository->findAllOrdered(),
        ]);
    }

    #[Route('/enclos/{id}', name: 'app_manager_enclos_detail')]
    public function enclosDetail(Enclos $enclos): Response
    {
        return $this->render('admin/ferme/enclos/detail.html.twig', [
            'enclos' => $enclos,
        ]);
    }

    #[Route('/couveuses', name: 'app_manager_couveuses')]
    public function couveusesIndex(CouveuseRepository $couveuseRepository): Response
    {
        return $this->render('admin/ferme/couveuses/index.html.twig', [
            'couveuses' => $couveuseRepository->findAll(),
        ]);
    }

    #[Route('/couveuses/{id}', name: 'app_manager_couveuse_detail')]
    public function couveuseDetail(Couveuse $couveuse): Response
    {
        return $this->render('admin/ferme/couveuses/detail.html.twig', [
            'couveuse' => $couveuse,
        ]);
    }

    #[Route('/journal', name: 'app_manager_journal')]
    public function journalIndex(JournalFermeRepository $journalFermeRepository): Response
    {
        return $this->render('admin/ferme/journal/index.html.twig', [
            'entries' => $journalFermeRepository->findRecent(50),
        ]);
    }

    #[Route('/journal/ajouter', name: 'app_manager_journal_ajouter', methods: ['GET', 'POST'])]
    public function journalAjouter(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            /** @var Manager $manager */
            $manager = $this->getUser();

            $entry = new JournalFerme();
            $entry->setManager($manager);
            $entry->setType($request->request->get('type'));
            $entry->setDescription($request->request->get('description'));
            $entry->setQuantite($request->request->getInt('quantite', 0));

            $dateStr = $request->request->get('date');
            if ($dateStr) {
                $entry->setDate(new \DateTimeImmutable($dateStr));
            } else {
                $entry->setDate(new \DateTimeImmutable());
            }

            $em->persist($entry);
            $em->flush();

            $this->addFlash('success', 'Entrée ajoutée au journal.');

            return $this->redirectToRoute('app_manager_journal');
        }

        return $this->render('admin/ferme/journal/ajouter.html.twig');
    }
}
