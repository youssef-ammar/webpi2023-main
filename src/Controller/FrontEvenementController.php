<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Repository\EvenementRepository;
use App\Repository\TypeEvenementRepository;
use App\Repository\UserRepository;
use App\Repository\ParticipationEvRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ParticipationEvType;
use App\Form\Evenement1Type;
use App\Entity\ParticipationEv;
use App\Entity\Evenement;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class FrontEvenementController extends AbstractController
{
    #[Route('/front/evenementList', name: 'liste')]
    public function index(
        EvenementRepository     $evenementRepository,
        TypeEvenementRepository $typeEvenementRepository,
        EntityManagerInterface  $entityManager,
        Request                 $request
    ): Response
    {
        // Récupérer les trois événements avec le plus grand nombre de participants
        //createquery taaml creation personnalisé lRQ
        $em = $this->getDoctrine()->getManager();
        $topevents = $em->createQuery(
            'SELECT e, COUNT(p.id) AS nb_participants
             FROM App\Entity\Evenement e
             JOIN e.participations p
             GROUP BY e.id
             ORDER BY nb_participants DESC'
        )->setMaxResults(3)->getResult();


        // // Afficher les informations des événements et le nombre de participants pour chaque événement
        // $topEvenements = [];
        // foreach ($evenements as $evenement) {
        //     $topEvenements[] = [
        //         'nom_ev' => $evenement['0']->getNomEv(),
        //         'nb_participants' => $evenement['nb_participants'],
        //     ];
        // }

//         $evenementsParType = $evenementRepository->createQueryBuilder('e')
//             ->select('COUNT(e.id) as nb_evenements', 't.domaine')
//             ->leftJoin('e.id_type', 't')
//             ->groupBy('t.id')
//             ->getQuery()
//             ->getResult();
        $evenements = $evenementRepository->findAll();
        $typesEvenement = $typeEvenementRepository->findAll();

        return $this->render('front_evenement/evenementList.html.twig', [
            'evenements' => $evenements,
            'topEvenements' => $topevents,
            'typesEvenement' => $typesEvenement,
//             'evenementsParType' => $evenementsParType,
        ]);
    }

    #[Route('/front/evenementList', name: 'search')]
    public function searchEvenement(Request $request, EvenementRepository $evenementRepository)
    {
        $requestString = $request->get('searchValue');
        $evenements = $evenementRepository->findEvenementByNom($requestString);

        $html = $this->render('front_evenement/row.html.twig', [
            'evenements' => $evenements,
        ]);
        return new Response($html);
    }

    #[Route('/front/evenementList/{id}', name: 'participer')]
    public function getbyid(AuthenticationUtils    $authenticationUtils,
                            EvenementRepository    $evenementRepository,
                            UserRepository         $userRepository,
                            EntityManagerInterface $entityManager,
                                                   $id
    ): Response
    {
        // Récupérer l'événement à partir de l'ID

        $ev = $evenementRepository->findOneBy(['id' => $id]);
        $lastUsername = $authenticationUtils->getLastUsername();
        $user = $userRepository->findOneBy(['email' => $lastUsername]);

        // Récupérer l'utilisateur à partir de l'ID


        // Créer une nouvelle entité ParticipationEvenement et y stocker l'ID de l'événement et de l'utilisateur
        $participation = new ParticipationEv();
        $participation->setIdEv($ev);
        $participation->setUser($user);

        // Enregistrer la nouvelle entité dans la base de données
        $entityManager->persist($participation);
        $entityManager->flush();

        // Rendu de template avec l'événement récupéré
        return $this->redirectToRoute('liste', [], Response::HTTP_SEE_OTHER);
    }


    // #[Route('/front/evenementList', name: 'participer', methods: [ 'POST'])]
    // public function new(
    //     Evenement $evenement,
    //     ParticipationEvRepository $ParticipationEvRepository
    // ): Response {
    //     // Crée une nouvelle instance de l'entité Evenement
    //     $ParticiptionEv = new ParticiptionEv();
    //     $User = new User();
    //     // $Userid = $User->getIdUser();
    //     $Evid = $evenement->getIdEv();
    //     $ParticiptionEv->setIdUser(1);
    //     $ParticiptionEv->setIdEv($Evid);
    //     $form = $this->createForm(ParticipationEvType::class, $ParticipationEv);
    //     $form->handleRequest($request);
    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $ParticipationEvRepository->save($ParticiptionEv, true);
    //         return $this->render('front_evenement/evenementList.html.twig', [
    //             'form' => $form->createView(),
    //         ]);
    //     }
    // }
    // #[Route('/front/evenementList', name: 'app_front_evenement')]
    // public function participer(
    //     Request $request,
    //     ParticipationEv $ParticipationEv,
    //     ParticipationEvRepository $ParticipationEvRepository
    // ): Response {
    //     $form = $this->createForm(ParticipationEvType::class, $ParticipationEv);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $ParticipationEvRepository->save($ParticipationEv, true);

    //     }

    //      return $this->render('front_evenement/evenementList.html.twig', [
    //         'form' => $form,
    //         'ParticipationEv' => $ParticipationEv,

    //     ]);
    // }
}
