<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Evenement;
use Dompdf\Dompdf;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use App\Form\Evenement1Type;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Repository\EvenementRepository;

use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Encoding\Encoding;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class EvenementController extends AbstractController
{
    
    //injecter le service pour utiliser le qrcode
    #[Route('/evenement', name: 'app_evenement_index', methods: ['GET'])]
    public function index(EvenementRepository $evenementRepository ): Response
    {
        $evenements = $evenementRepository->findAll();
        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenements,
        ]);
    }
    #[Route('/evenement_pdf', name: 'pdf', methods: ['GET'])]

    public function generatePdf(): Response
{
    // Récupérer les contrats à afficher dans le PDF
    $evenement = $this->getDoctrine()->getRepository(evenement::class)->findAll();

    // Générer le HTML de la liste des contrats
    $html = $this->renderView('evenement/pdf.html.twig',
     ['evenement' => $evenement]);

    // Initialiser Dompdf
    $dompdf = new Dompdf();
    //gerer le fichier pdf a partir d un html
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');

    // Générer le PDF
    $dompdf->render();

    // Retourner une réponse avec le PDF
    $response = new Response($dompdf->output());
    $disposition = $response->headers->makeDisposition(
        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
        'liste_evenements.pdf'
    );
    $response->headers->set('Content-Disposition', $disposition);
    $response->headers->set('Content-Type', 'application/pdf');

    return $response;
}
    
    
    #[Route('/evenement_new', name: 'app_evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,AuthenticationUtils $authenticationUtils ): Response
    {

        // Crée une nouvelle instance de l'entité Evenement
        $evenement = new Evenement();
    
        // Crée un formulaire à partir de l'entité Evenement
        $form = $this->createForm(Evenement1Type::class, $evenement);
    
        // Gère la requête HTTP et met à jour le formulaire en conséquence
        $form->handleRequest($request);
    
        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
    
            // Récupère l'image depuis le formulaire
            $imageFile = $form->get('image')->getData();
    
            // Si une image est sélectionnée
            if ($imageFile) {
    
                // Récupère le nom de fichier original et l'extension
                $originalFilename = pathinfo(
                    $imageFile->getClientOriginalName(),
                    PATHINFO_FILENAME
                );
                $newFilename =
                    $originalFilename .
                    '-' .
                    uniqid() .
                    '.' .
                    $imageFile->guessExtension();
    
                // Essaie de déplacer l'image dans le dossier d'upload
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // En cas d'erreur, redirige vers la page de création d'événement
                    $this->addFlash('error', 'An error occurred while uploading the image.');
                    return $this->redirectToRoute('app_evenement_new');
                }
    
                // Met à jour l'entité Evenement avec le nom du fichier de l'image
                $evenement->setImage($newFilename);
                // Persiste l'entité Evenement
                $entityManager->persist($evenement);
    
                // Enregistre les modifications en base de données
                $entityManager->flush();
    
           
                return $this->redirectToRoute(
                    'app_evenement_index',
                    [],
                    Response::HTTP_SEE_OTHER
                );
            }
        }
    
        // Affiche le formulaire de création d'événement
        return $this->render('evenement/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    
    

    #[Route('/{id}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Evenement $evenement,
        EvenementRepository $evenementRepository
    ): Response {
        $form = $this->createForm(Evenement1Type::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evenementRepository->save($evenement, true);

            return $this->redirectToRoute(
                'app_evenement_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }
    private $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        $this->entityManager = $registry->getManager();
    }
    #[Route('/evenement/remove/{id}', name: 'app_evenement_delete', methods: ['GET'])]
    public function remove(EntityManagerInterface $entityManager, $id): Response
    {
        $evenement = $entityManager->getRepository(Evenement::class)->find($id);

        if (!$evenement) {
            throw $this->createNotFoundException(
                'No event found for id ' . $id
            );
        }

        $entityManager->remove($evenement);
        $entityManager->flush();

        return $this->redirectToRoute('app_evenement_index');
    }
   
    #[Route('/evenement/show/{id}', name: 'detailleEv', methods: ['GET'])]
    public function show(evenement $Evenement , evenementRepository $evenementRepository , $id , ManagerRegistry $doctrine): Response
    {          

        $listev= $doctrine->getRepository(Evenement::class)->find($id);
        $evenement=$evenementRepository->find($id); 
        $qrString = "L' evenement " . $evenement->getNomEv() ." organisé par le groupe freelanci sera le " .  $evenement->getDateEv()->format('d/m/Y') ." , ". $evenement->getHeureEv()."H au " . $evenement->getEmplacement() . "," . $evenement->getRegion()  ;
         $writer = new PngWriter();
 
         $qrCode = QrCode::create($qrString)
             ->setEncoding(new Encoding('UTF-8'))
             ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
             ->setSize(120)
             ->setMargin(0)
             ->setForegroundColor(new Color(0, 0, 0))
             ->setBackgroundColor(new Color(255, 255, 255));
         $logo = Logo::create('images/logo.png')
             ->setResizeToWidth(60);
         $label = Label::create('')->setFont(new NotoSans(8));

         $qrCodes = [];
         $qrCodes['images'] = $writer->write($qrCode, $logo)->getDataUri();


         $qrCodes['simple'] = $writer->write(
                                 $qrCode,
                                 null,
                                 $label->setText('Soyez le Bienvenue')
                             )->getDataUri();

         
       
        
        return $this->render('evenement/show.html.twig', [
            'evenement' => $Evenement,
            'qrCodes' =>$qrCodes,

        ]);
    }
 
}
