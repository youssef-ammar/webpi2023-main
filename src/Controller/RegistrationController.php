<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }
    #[Route('/verifytemplate', name: 'app_verify')]
    public function verify(Request $request, TranslatorInterface $translator,FlashyNotifier $flashyNotifier): Response
    {
        return $this->render('user/verify.html.twig');
    }
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager,FlashyNotifier $flashyNotifier): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ( $user->getAdresse() == "Tunis" ) {
                $user->setLatitude(36.8008);
                $user->setLongitude(10.1800);
            } else if ( $user->getAdresse() == "Sfax" ) {
                $user->setLatitude(34.7400);
                $user->setLongitude(10.7600);
            } else if ( $user->getAdresse() == "Sousse" ) {
                $user->setLatitude(35.8333);
                $user->setLongitude(10.6333);
            } else if ( $user->getAdresse() == "Kairouan" ) {
                $user->setLatitude(35.6833);
                $user->setLongitude(10.1000);
            } else if ( $user->getAdresse() == "Métouia" ) {
                $user->setLatitude(33.9600);
                $user->setLongitude(10.0000);
            } else if ( $user->getAdresse() == "Kebili" ) {
                $user->setLatitude(33.7050);
                $user->setLongitude(8.9650);
            } else if ( $user->getAdresse() == "Bizerte" ) {
                $user->setLatitude(37.2744);
                $user->setLongitude(9.8739);
            } else if ( $user->getAdresse() == "Sidi Bouzid" ) {
                $user->setLatitude(35.0381);
                $user->setLongitude(9.4858);
            } else if ( $user->getAdresse() == "Gabès" ) {
                $user->setLatitude(33.8814);
                $user->setLongitude(10.0983);
            } else if ( $user->getAdresse() == "Ariana" ) {
                $user->setLatitude(36.8625);
                $user->setLongitude(10.1956);
            } else {//Béja
                $user->setLatitude(36.7256);
                $user->setLongitude(9.1817);
            }
            $existingUseremail=$entityManager->getRepository(User::class)->findOneBy([
                'email' => $user->getEmail()
            ]);
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            if($user->getRole()=="Client"){
                $user->setRoles("ROLE_CLIENT");
            }
            else{
                $user->setRoles("ROLE_FREELANCER");
            }
            if ($existingUseremail !== null) {
                $flashyNotifier->error('There is an existing account with this email');
                return $this->redirectToRoute('app_user_new');
            }
            else{
                $entityManager->persist($user);
                $entityManager->flush();
                // generate a signed url and email it to the user
                $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                    (new TemplatedEmail())
                        ->from(new Address('freelanci.freelanci@gmail.com', 'Freelanci'))
                        ->to($user->getEmail())
                        ->subject('Please Confirm your Email')
                        ->htmlTemplate('registration/confirmation_email.html.twig')
                );
                $flashyNotifier->success('Account created');
                return $this->redirectToRoute('app_user_edit2', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
            }



        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator,FlashyNotifier $flashyNotifier): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_login');
        }

        $flashyNotifier->success('Your email has been verified');
        $roles = $this->getUser()->getRoles();
        // Tranform this list in array

        // If is a admin we redirect to the backoffice area
        if ($roles=="ROLE_ADMIN"){

            return $this->redirectToRoute('app_user_index');
        }
        else if ($roles=="ROLE_CLIENT"){

            return $this->redirectToRoute('app_front');
        }
        else{

            return $this->redirectToRoute('app_front');
        }
    }
}
