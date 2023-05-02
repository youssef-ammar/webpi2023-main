<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Form\FormationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use App\Repository\FormationRepository;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Doctrine\Persistence\ManagerRegistry;

class FreelancerController extends AbstractController
{
    #[Route('/listFormationFree', name: 'app_freelancer')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $formations = $entityManager
            ->getRepository(Formation::class)
            ->findAll();
        return $this->render('freelancer/indexFreelancer.html.twig', [
            'controller_name' => 'FrontController',
            'formations' => $formations,
        ]);
    }
    #[Route('/addform', name: 'addform')]
    public function add(EntityManagerInterface $entityManager): Response
    {

        return $this->render('freelancer/new.html.twig');
    }

    #[Route('/freelancer', name: 'app_freelancer_index', methods: ['GET'])]
    public function listFromationFront(EntityManagerInterface $entityManager): Response
    {
        $formations = $entityManager
            ->getRepository(Formation::class)
            ->findAll();

        return $this->render('freelancer/listshowFreelancer.html.twig', [
            'formations' => $formations,
        ]);
    }

    #[Route('/showFrontFreelancer/{idMat}', name: 'app_freelancer_show', methods: ['GET'])]
    public function showFront(int $idMat, EntityManagerInterface $entityManager): Response
    {
        $formation = $entityManager->getRepository(Formation::class)->find($idMat);
        if (!$formation) {
            throw $this->createNotFoundException('Formation not found for id ' . $idMat);
        }
        
        return $this->render('freelancer/showFreelancer.html.twig', [
            'formation' => $formation,
        ]);
    }
  
    #[Route('/freelancer/{idMat}', name: 'app_freelancer_delete')]
    public function deleteFr($idMat, FormationRepository $formationRepository, ManagerRegistry $doctrine): Response
    {
        $Formations = $formationRepository->find($idMat);
        $em = $doctrine->getManager();
        $em->remove($Formations);
        $em->flush();
        return $this->redirectToRoute('app_freelancer_index');

      
    }
    
    #[Route('/freelancernew', name: 'app_freelancer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           // $entityManager=$this->getDoctrine()->getManager();
            $entityManager->persist($formation);//ta3ml ajout
            $entityManager->flush();
            $this->addFlash(
                'notice', 'Formation a été bien ajoutée !'
            );

            return $this->redirectToRoute('app_freelancer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('freelancer/new.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }
    #[Route('/freelancer/{idMat}/edit', name: 'app_freelancer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Formation $formation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash(
                'notice', 'Formation a été bien modifiée ! '
            );

            return $this->redirectToRoute('app_freelancer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('freelancer/editFreelancer.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

}