<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Formation;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    #[Route('/frontE', name: 'front')]
    public function indexE(EvenementRepository $rep1): Response
    {
        $evenements = $rep1->findAll();
        return $this->render('front/index.html.twig', [
            'evenements' => $evenements,
        ]);
    }
    public function getTypeEvenement()
    {
        return $this->typeEvenement;
    }
    public function details($id)
    {
        // Récupération de l'événement correspondant à l'identifiant $id
        $evenement = $this->getDoctrine()
            ->getRepository(Evenement::class)
            ->find($id);

        // Récupération de l'objet TypeEvenement associé à l'événement
        $typeEvenement = $evenement->getTypeEvenement();

        // Passage des variables $evenement et $typeEvenement au template Twig
        return $this->render('front.html.twig', [
            'evenement' => $evenement,
            'typeEvenement' => $typeEvenement,
        ]);
    }
    #[Route('/listFormation', name: 'app_front')]
    public function indexF(EntityManagerInterface $entityManager): Response
    {
        $formations = $entityManager
            ->getRepository(Formation::class)
            ->findAll();
        return $this->render('front/indexFront.html.twig', [
            'controller_name' => 'FrontController',
            'formations' => $formations,
        ]);
    }

    #[Route('/frontF', name: 'app_front_index', methods: ['GET'])]
    public function listFromationFront(EntityManagerInterface $entityManager): Response
    {
        $formations = $entityManager
            ->getRepository(Formation::class)
            ->findAll();

        return $this->render('front/listshow.html.twig', [
            'formations' => $formations,
        ]);
    }

    #[Route('/showFront/{idMat}', name: 'app_front_show', methods: ['GET'])]
    public function showFront(int $idMat, EntityManagerInterface $entityManager): Response
    {
        $formation = $entityManager->getRepository(Formation::class)->find($idMat);
        if (!$formation) {
            throw $this->createNotFoundException('Formation not found for id ' . $idMat);
        }

        return $this->render('front/showFront.html.twig', [
            'formation' => $formation,
        ]);
    }

}
