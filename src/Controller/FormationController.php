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
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use App\Repository\FormationRepository;
use Doctrine\Persistence\ManagerRegistry;
#[Route('/formation')]
class FormationController extends AbstractController
{
    #[Route('/', name: 'app_formation_index', methods: ['GET'])]
    public function index(Request $request, $page = 1): Response
{
    $limit = 4; // the number of items per page
    $offset = ($page - 1) * $limit;
    $entityManager = $this->getDoctrine()->getManager();
    $query = $entityManager->getRepository(Formation::class)
        ->createQueryBuilder('e')
        ->setFirstResult($offset)
        ->setMaxResults($limit)
        ->getQuery();

    $paginator = new Paginator($query, $fetchJoinCollection = true);
    $totalItems = $paginator->count();
    $pagesCount = ceil($totalItems / $limit);

    // Add previous and next icons to the pagination links
    $previousPage = $page > 1 ? $page - 1 : null;
    $nextPage = $page < $pagesCount ? $page + 1 : null;

    return $this->render('formation/index.html.twig', [
        'formations' => $paginator,
        'currentPage' => $page,
        'pagesCount' => $pagesCount,
        'previousPage' => $previousPage,
        'nextPage' => $nextPage,
        'limit' => $limit, // Pass the limit to the template
    ]);
}



    #[Route('/new', name: 'app_formation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           // $entityManager=$this->getDoctrine()->getManager();
            $entityManager->persist($formation);//fait l ajout
            $entityManager->flush();
            $this->addFlash(
                'notice', 'Formation a été bien ajoutée !'
            );

            return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('formation/new.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    #[Route('/{idMat}', name: 'app_formation_show', methods: ['GET'])]
    public function show(int $idMat,EntityManagerInterface $entityManager): Response
    {
        $formation = $entityManager->getRepository(Formation::class)->find($idMat);
        if (!$formation) {
            throw $this->createNotFoundException('Formation not found for id ' . $idMat);
        }
        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
        ]);
    }

    #[Route('/{idMat}/edit', name: 'app_formation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Formation $formation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash(
                'notice', 'Formation a été bien modifiée ! '
            );

            return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('formation/edit.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }
 

    #[Route('/{idMat}/delete', name: 'app_formation_delete')]
    public function delete($idMat, FormationRepository $formationRepository, ManagerRegistry $doctrine): Response
    {
      
        $Formations = $formationRepository->find($idMat);
        $em = $doctrine->getManager();
        $em->remove($Formations);
        $em->flush();
        return $this->redirectToRoute('app_formation_index');
       
        
    }

  

    #[Route('/{idMat}/pdf', name: 'app_formation_pdf', methods: ['GET'])]
    public function pdf(Formation $formation): Response
{
    // create new PDF document
    $dompdf = new Dompdf();
    
    // generate HTML content for the document
    $html = $this->renderView('formation/pdf.html.twig', [
        'formation' => $formation, 
        
    ]);

    // load HTML into the PDF document
    $dompdf->loadHtml($html);

    // set paper size and orientation
    

    // render PDF document
    $dompdf->render();

    // create a response object to return the PDF file
    $response = new Response($dompdf->output());
    
    // set content type to application/pdf
    $response->headers->set('Content-Type', 'application/pdf');

    $disposition = $response->headers->makeDisposition(
        ResponseHeaderBag::DISPOSITION_INLINE,
        'formation.pdf'
    );
    $response->headers->set('Content-Disposition', $disposition);

    return $response;
}

#[Route('/search', name: 'app_formation_search', methods: ['GET'])]
 public function searchh(Request $request, FormationRepository $formationRepository)
 {
     $query = $request->query->get('q');
     
     // Perform the search using a repository method or query builder
     $formations = $formationRepository->search($query);
     
     return $this->render('formation/search.html.twig', [
         'formations' => $formations,
         'query' => $query
     ]);
 }
 // METIER AJAX SEARCH :
    //SEARCH

    /**
     * @Route("/ajax_search/", name="ajax_search")
     */
    public function chercherService(\Symfony\Component\HttpFoundation\Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $requestString = $request->get('q');

        $x = $em
            ->createQuery(
                'SELECT P
                FROM App\Entity\Formation P
                WHERE P.titrefr LIKE :str'
            )
            ->setParameter('str', '%' . $requestString . '%')->getResult();

        $formations = $x;
        if (!$formations) {
            $result['formations']['error'] = "formations non trouvé 🙁 ";
        } else {
            $result['formations'] = $this->getRealEntities($formations);
        }
        return new Response(json_encode($result));
    }

    public function getRealEntities($formations)
    {
        foreach ($formations as $formation) {
            $realEntities[$formation->getTitrefr()] = [$formation->getType(), $formation->getContenu(), $formation->getNiveau()];
        }
        return $realEntities;
    }
}
