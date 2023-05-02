<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Knp\Component\Pager\PaginatorInterface;
use Dompdf\Dompdf;
use Dompdf\Options;


use Symfony\Component\HttpKernel\Event\ExceptionEvent;

#[Route('/reclamation')]
class ReclamationController extends AbstractController
{
    #[Route('/', name: 'app_reclamation_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager,PaginatorInterface $paginator,Request $request): Response
    {
        $reclamations = $entityManager
            ->getRepository(Reclamation::class)
            ->findAll();

        $reclamations = $paginator->paginate(
            $reclamations, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
        5 /*limit per page*/
        );
        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();

        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reclamation);
            $entityManager->flush();

            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{idrec}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{idrec}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{idrec}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getIdrec(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();

        }

        return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/{idrec}/listp', name: 'app_reclamation_listp', methods: ['GET'])]
    public function listp(Reclamation $reclamation): Response
    {

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);



        // Retrieve the HTML generated in our twig file reclamation\listp.html.twig
        $html = $this->renderView('reclamation/listp.html.twig', [
            'reclamation' => $reclamation,
        ]);

        // Load HTML to Dompdf


        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();


        // Output the generated PDF to Browser (force download)
        $dompdf->stream("reclamation.pdf", [
            "Attachment" => true
        ]);

        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
            ]
        );

    }




    #[Route('/reclamationadmin', name: 'app_reclamation_reclamationadmin', methods: ['GET'])]
    public function reclamationadmin(Reclamation $reclamation): Response
    {

        return $this->render('reclamation\reclamationadmin.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }




}
