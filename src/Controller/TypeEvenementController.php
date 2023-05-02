<?php

namespace App\Controller;

use App\Entity\TypeEvenement;
use App\Form\TypeEvenement1Type;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TypeEvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TypeEvenementController extends AbstractController
{
    #[Route('/type/evenement', name: 'app_type_evenement_index', methods: ['GET'])]
    public function index(TypeEvenementRepository $typeEvenementRepository): Response
    {
        return $this->render('type_evenement/index.html.twig', [
            'type_evenements' => $typeEvenementRepository->findAll(),
        ]);
    }

    #[Route('/type/evenement/new', name: 'app_type_evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TypeEvenementRepository $typeEvenementRepository): Response
    {
        $typeEvenement = new TypeEvenement();
        $form = $this->createForm(TypeEvenement1Type::class, $typeEvenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typeEvenementRepository->save($typeEvenement, true);

            return $this->redirectToRoute('app_type_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('type_evenement/new.html.twig', [
            'type_evenement' => $typeEvenement,
            'form' => $form,
        ]);
    }

    #[Route('/type/evenement{id}', name: 'app_type_evenement_show', methods: ['GET'])]
    public function show(TypeEvenement $typeEvenement): Response
    {
        return $this->render('type_evenement/show.html.twig', [
            'type_evenement' => $typeEvenement,
        ]);
    }

   

    #[Route('/type/evenement/{id}/edit', name: 'app_type_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypeEvenement $typeEvenement, TypeEvenementRepository $typeEvenementRepository): Response
    {
        $form = $this->createForm(TypeEvenement1Type::class, $typeEvenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typeEvenementRepository->save($typeEvenement, true);

            return $this->redirectToRoute('app_type_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('type_evenement/edit.html.twig', [
            'type_evenement' => $typeEvenement,
            'form' => $form,
        ]);
    }

    #[Route('/type/evenement/remove/{id}', name: 'app_type_evenement_delete', methods: ['GET'])]
    public function delete(Request $request, TypeEvenement $typeEvenement, TypeEvenementRepository $typeEvenementRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typeEvenement->getId(), $request->request->get('_token'))) {
            $typeEvenementRepository->remove($typeEvenement, true);
        }

        return $this->redirectToRoute('app_type_evenement_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/type/evenement/remove/{id}', name: 'app_type_evenement_delete', methods: ['GET'])]
    public function remove(EntityManagerInterface $entityManager, $id): Response
    {
        $typeevenement = $entityManager->getRepository(TypeEvenement::class)->find($id);

        if (!$typeevenement) {
            throw $this->createNotFoundException(
                'No event found for id ' . $id
            );
        }

        $entityManager->remove($typeevenement);
        $entityManager->flush();

        return $this->redirectToRoute('app_type_evenement_index');
    }
}
