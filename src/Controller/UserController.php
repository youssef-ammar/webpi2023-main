<?php

namespace App\Controller;

use App\Entity\ParticipationEv;
use App\Entity\User;
use App\Form\UserType;
use App\Form\UserType2;
use App\Form\UserTypeEditUser;
use App\Repository\EvenementRepository;
use App\Repository\ParticipationEvRepository;
use App\Repository\UserRepository;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])] //je fais l'appel a paginator interface il va me permet de faire la paginataion
    public function index(UserRepository $userRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.state = :state')
            ->setParameter('state', 0)
            ->orderBy('u.date_naissance', 'ASC')
            ->getQuery();

        $donnees = $query->getResult();

        // $donnees = $userRepository->triepardate();//dans la place de findall je fais l'apelle a la fnct tri par date
        $users = $paginator->paginate(
            $donnees,// Requête contenant les données à paginer (ici les user)
            $request->query->getInt('page', 1),// Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            3  // Nombre de résultats par page
        );

        $userByadress = []; //TABLEAU  pour stocker les utilisateurs par addesse c
        foreach ($users as $user) {         //BOUCLE FOR
            $lieu = $user->getAdresse();
            //A chaque addresse il incremente le nombre
            if (isset($userByadress[$lieu])) {
                $userByadress[$lieu] += 1;
            } else {// le nombre reste comme il est 1
                $userByadress[$lieu] = 1;
            }
        }
        //TABLEAU contient les parametrs de localisation
        $markers = [];
        //Cette ligne initialise une variable $markers avec un tableau vide.
        // Cette variable sera utilisée pour stocker les informations des marqueurs qui seront affichés sur la carte.
        foreach ($users as $user) {
            //Cette ligne commence une boucle foreach qui va itérer sur chaque élément du tableau $users.
            //Dans ce contexte, $users est une liste d'utilisateurs fournie en entrée.

            $marker = [
                'lat' => $user->getLatitude(),
                'lng' => $user->getLongitude(),
                'popupContent' => $user->getAdresse(),// l adresse afficher dans le menu affiche ("le careau contient l @ ")
            ];
            $markers[] = $marker;
        }
        return $this->render('user/index.html.twig', [
            'users' => $users,
            'markers' => $markers,
            'userbyadress' => $userByadress,
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    //register
    public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, UserPasswordEncoderInterface $passwordencoder, FlashyNotifier $flashy): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getAdresse() == "Tunis") {
                $user->setLatitude(36.8008);
                $user->setLongitude(10.1800);
            } else if ($user->getAdresse() == "Sfax") {
                $user->setLatitude(34.7400);
                $user->setLongitude(10.7600);
            } else if ($user->getAdresse() == "Sousse") {
                $user->setLatitude(35.8333);
                $user->setLongitude(10.6333);
            } else if ($user->getAdresse() == "Kairouan") {
                $user->setLatitude(35.6833);
                $user->setLongitude(10.1000);
            } else if ($user->getAdresse() == "Métouia") {
                $user->setLatitude(33.9600);
                $user->setLongitude(10.0000);
            } else if ($user->getAdresse() == "Kebili") {
                $user->setLatitude(33.7050);
                $user->setLongitude(8.9650);
            } else if ($user->getAdresse() == "Bizerte") {
                $user->setLatitude(37.2744);
                $user->setLongitude(9.8739);
            } else if ($user->getAdresse() == "Sidi Bouzid") {
                $user->setLatitude(35.0381);
                $user->setLongitude(9.4858);
            } else if ($user->getAdresse() == "Gabès") {
                $user->setLatitude(33.8814);
                $user->setLongitude(10.0983);
            } else if ($user->getAdresse() == "Ariana") {
                $user->setLatitude(36.8625);
                $user->setLongitude(10.1956);
            } else {//Béja
                $user->setLatitude(36.7256);
                $user->setLongitude(9.1817);
            }
            $existingUseremail = $entityManager->getRepository(User::class)->findOneBy([
                'email' => $user->getEmail()
            ]);

            $encoded = $passwordencoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);
            if($user->getRole()=="Client"){
                $user->setRoles("ROLE_CLIENT");
            }
            else{
                $user->setRoles("ROLE_FREELANCER");
            }
            if ($existingUseremail !== null) {
                $flashy->error('There is an existing account with this email');
                return $this->redirectToRoute('app_user_new');
            } else {
                $userRepository->save($user, true);
                $flashy->success('Account created');
                return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
            }


        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/new2', name: 'app_user_new2', methods: ['GET', 'POST'])]
    //admin cree un user
    public function new2(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, UserPasswordEncoderInterface $passwordencoder, FlashyNotifier $flashy): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);//affecter les données soumises dans le formulaire à l'objet user

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getAdresse() == "Tunis") {
                $user->setLatitude(36.8008);
                $user->setLongitude(10.1800);
            } else if ($user->getAdresse() == "Sfax") {
                $user->setLatitude(34.7400);
                $user->setLongitude(10.7600);
            } else if ($user->getAdresse() == "Sousse") {
                $user->setLatitude(35.8333);
                $user->setLongitude(10.6333);
            } else if ($user->getAdresse() == "Kairouan") {
                $user->setLatitude(35.6833);
                $user->setLongitude(10.1000);
            } else if ($user->getAdresse() == "Métouia") {
                $user->setLatitude(33.9600);
                $user->setLongitude(10.0000);
            } else if ($user->getAdresse() == "Kebili") {
                $user->setLatitude(33.7050);
                $user->setLongitude(8.9650);
            } else if ($user->getAdresse() == "Bizerte") {
                $user->setLatitude(37.2744);
                $user->setLongitude(9.8739);
            } else if ($user->getAdresse() == "Sidi Bouzid") {
                $user->setLatitude(35.0381);
                $user->setLongitude(9.4858);
            } else if ($user->getAdresse() == "Gabès") {
                $user->setLatitude(33.8814);
                $user->setLongitude(10.0983);
            } else if ($user->getAdresse() == "Ariana") {
                $user->setLatitude(36.8625);
                $user->setLongitude(10.1956);
            } else {//Béja
                $user->setLatitude(36.7256);
                $user->setLongitude(9.1817);
            }

            /////////////////NOTIF //////////////
            // J'ai ajouté un variable existingUseremail
            $existingUseremail = $entityManager->getRepository(User::class)->findOneBy([//je l'adonne un email
                //ME RETOUNE UN UTILSATEUR PAR EMAIl
                'email' => $user->getEmail()
            ]);
            $encoded = $passwordencoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);
            if($user->getRole()=="Client"){
                $user->setRoles("ROLE_CLIENT");
            }
            else{
                $user->setRoles("ROLE_FREELANCER");
            }
            if ($existingUseremail !== null) {
                $flashy->error('There is an existing account with this email');
                return $this->redirectToRoute('app_user_new2');
            } else {
                $userRepository->save($user, true);
                $flashy->success('Account created');
                return $this->redirectToRoute('app_user_show2', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
            }

        }

        return $this->renderForm('user/new2.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    //myprofile
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/showadmin/{id}', name: 'app_user_show2', methods: ['GET'])]
    public function show2(User $user): Response
    {
        return $this->render('user/showAdmin.html.twig', [
            'user' => $user,
        ]);
    }

    //admin updates user's account
    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserRepository $userRepository, UserPasswordEncoderInterface $passwordencoder, FlashyNotifier $flashy): Response
    {
        $form = $this->createForm(UserTypeEditUser::class, $user);
        $form->handleRequest($request);
        if ($user->getRoles()=="ROLE_ADMIN"){
            $user->setRole('Admin');
        }
        elseif ($user->getRoles()=="ROLE_CLIENT"){
            $user->setRole('Client');
        }
        else {
            $user->setRole('Freelancer');
        }
        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getAdresse() == "Tunis") {
                $user->setLatitude(36.8008);
                $user->setLongitude(10.1800);
            } else if ($user->getAdresse() == "Sfax") {
                $user->setLatitude(34.7400);
                $user->setLongitude(10.7600);
            } else if ($user->getAdresse() == "Sousse") {
                $user->setLatitude(35.8333);
                $user->setLongitude(10.6333);
            } else if ($user->getAdresse() == "Kairouan") {
                $user->setLatitude(35.6833);
                $user->setLongitude(10.1000);
            } else if ($user->getAdresse() == "Métouia") {
                $user->setLatitude(33.9600);
                $user->setLongitude(10.0000);
            } else if ($user->getAdresse() == "Kebili") {
                $user->setLatitude(33.7050);
                $user->setLongitude(8.9650);
            } else if ($user->getAdresse() == "Bizerte") {
                $user->setLatitude(37.2744);
                $user->setLongitude(9.8739);
            } else if ($user->getAdresse() == "Sidi Bouzid") {
                $user->setLatitude(35.0381);
                $user->setLongitude(9.4858);
            } else if ($user->getAdresse() == "Gabès") {
                $user->setLatitude(33.8814);
                $user->setLongitude(10.0983);
            } else if ($user->getAdresse() == "Ariana") {
                $user->setLatitude(36.8625);
                $user->setLongitude(10.1956);
            } else {//Béja
                $user->setLatitude(36.7256);
                $user->setLongitude(9.1817);
            }
            $existingUseremail = $entityManager->getRepository(User::class)->findOneBy([
                'email' => $user->getEmail()
            ]);
            $encoded = $passwordencoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);

            $userRepository->save($user, true);
            $flashy->success('Account updated');
            return $this->redirectToRoute('app_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);

        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    //admin updates his account
    #[Route('/{id}/editadmin', name: 'app_user_editadmin', methods: ['GET', 'POST'])]
    public function editadmin(Request $request, User $user, EntityManagerInterface $entityManager, UserRepository $userRepository, UserPasswordEncoderInterface $passwordencoder, FlashyNotifier $flashy): Response
    {
        $form = $this->createForm(UserType2::class, $user);
        $form->handleRequest($request);
        if ($user->getRoles()=="ROLE_ADMIN"){
            $user->setRole('Admin');
        }
        elseif ($user->getRoles()=="ROLE_CLIENT"){
            $user->setRole('Client');
        }
        else {
            $user->setRole('Freelancer');
        }
        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getAdresse() == "Tunis") {
                $user->setLatitude(36.8008);
                $user->setLongitude(10.1800);
            } else if ($user->getAdresse() == "Sfax") {
                $user->setLatitude(34.7400);
                $user->setLongitude(10.7600);
            } else if ($user->getAdresse() == "Sousse") {
                $user->setLatitude(35.8333);
                $user->setLongitude(10.6333);
            } else if ($user->getAdresse() == "Kairouan") {
                $user->setLatitude(35.6833);
                $user->setLongitude(10.1000);
            } else if ($user->getAdresse() == "Métouia") {
                $user->setLatitude(33.9600);
                $user->setLongitude(10.0000);
            } else if ($user->getAdresse() == "Kebili") {
                $user->setLatitude(33.7050);
                $user->setLongitude(8.9650);
            } else if ($user->getAdresse() == "Bizerte") {
                $user->setLatitude(37.2744);
                $user->setLongitude(9.8739);
            } else if ($user->getAdresse() == "Sidi Bouzid") {
                $user->setLatitude(35.0381);
                $user->setLongitude(9.4858);
            } else if ($user->getAdresse() == "Gabès") {
                $user->setLatitude(33.8814);
                $user->setLongitude(10.0983);
            } else if ($user->getAdresse() == "Ariana") {
                $user->setLatitude(36.8625);
                $user->setLongitude(10.1956);
            } else {//Béja
                $user->setLatitude(36.7256);
                $user->setLongitude(9.1817);
            }
            $existingUseremail = $entityManager->getRepository(User::class)->findOneBy([
                'email' => $user->getEmail()
            ]);
            $encoded = $passwordencoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);

            $userRepository->save($user, true);
            $flashy->success('Account updated');
            return $this->redirectToRoute('app_user_show2', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);

        }

        return $this->renderForm('user/editAdmin.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    //user updatdes his account
    #[Route('/{id}/edit2', name: 'app_user_edit2', methods: ['GET', 'POST'])]
    public function edit2(Request $request, User $user, EntityManagerInterface $entityManager, UserRepository $userRepository, UserPasswordEncoderInterface $passwordencoder, FlashyNotifier $flashy): Response
    {
        $form = $this->createForm(UserType2::class, $user);
        $form->handleRequest($request);
        $existingUseremail = $entityManager->getRepository(User::class)->findOneBy([
            'email' => $user->getEmail()
        ]);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getAdresse() == "Tunis") {
                $user->setLatitude(36.8008);
                $user->setLongitude(10.1800);
            } else if ($user->getAdresse() == "Sfax") {
                $user->setLatitude(34.7400);
                $user->setLongitude(10.7600);
            } else if ($user->getAdresse() == "Sousse") {
                $user->setLatitude(35.8333);
                $user->setLongitude(10.6333);
            } else if ($user->getAdresse() == "Kairouan") {
                $user->setLatitude(35.6833);
                $user->setLongitude(10.1000);
            } else if ($user->getAdresse() == "Métouia") {
                $user->setLatitude(33.9600);
                $user->setLongitude(10.0000);
            } else if ($user->getAdresse() == "Kebili") {
                $user->setLatitude(33.7050);
                $user->setLongitude(8.9650);
            } else if ($user->getAdresse() == "Bizerte") {
                $user->setLatitude(37.2744);
                $user->setLongitude(9.8739);
            } else if ($user->getAdresse() == "Sidi Bouzid") {
                $user->setLatitude(35.0381);
                $user->setLongitude(9.4858);
            } else if ($user->getAdresse() == "Gabès") {
                $user->setLatitude(33.8814);
                $user->setLongitude(10.0983);
            } else if ($user->getAdresse() == "Ariana") {
                $user->setLatitude(36.8625);
                $user->setLongitude(10.1956);
            } else {//Béja
                $user->setLatitude(36.7256);
                $user->setLongitude(9.1817);
            }
            $encoded = $passwordencoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);

            $userRepository->save($user, true);
            $flashy->success('Account updated');
            return $this->redirectToRoute('app_front', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit2.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }







    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager)
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {


            $user->setState(1);
            $entityManager->persist($user);
            $entityManager->flush();

        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    // cree un fct me permet d afficher la page stat

    #[Route('/role/stats', name: 'app_user_stats')]
    public function usersByRoleChartAction(EntityManagerInterface $entityManager): Response
    {
        $userRepository = $entityManager->getRepository(User::class);

////////////////////////////////////////////////////////////////////:statsByRole
        //j'ai deja cree la fonction "countByRole" dans repisotory---> userRepository
        // Get the user counts by role from the repository
        $freelancerCount = $userRepository->countByRole('ROLE_FREELANCER');  //je l'appele deux fois pour la donner parfois
        $clientCount = $userRepository->countByRole('ROLE_CLIENT');

        // Create the chart data
        $chartData2 = [
            ['Role', 'Count'],
            ['Freelancer', $freelancerCount],
            ['Client', $clientCount],
        ];

        // Create the chart object and set the data
        $pieChart2 = new PieChart();
        $pieChart2->getData()->setArrayToDataTable($chartData2);
        $pieChart2->getOptions()->setPieHole(0.4);
        $pieChart2->getOptions()->setWidth(500);
        $pieChart2->getOptions()->setHeight(400);
        // Set the chart options
        // $pieChart2->getOptions()->setTitle('User Role Distribution');


        // Render the view with the chart
        return $this->render('user/role_chart.html.twig', [
            'chart2' => $pieChart2,
        ]);
    }

    // j'ai cree deux fonction block et unblock 
    #[Route('/{id}/block', name: 'app_user_block', methods: ['POST', 'GET'])]
    public function block(Request $request, User $user, EntityManagerInterface $entityManager, FlashyNotifier $flashyNotifier): Response
    { //executer block
        $user->setBlock('Blocked');
        $entityManager->flush(); //envoyer vers la base de donné 
        $flashyNotifier->success('User Blocked');//affichage de notif 
        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/unblock', name: 'app_user_unblock', methods: ['POST', 'GET'])]
    public function unblock(Request $request, User $user, EntityManagerInterface $entityManager, FlashyNotifier $flashyNotifier): Response
    {
        $user->setBlock('unBlocked');
        $entityManager->flush();
        $flashyNotifier->success('User unBlocked');
        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
