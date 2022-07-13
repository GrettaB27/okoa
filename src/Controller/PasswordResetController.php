<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\PasswordResetFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\PasswordResetConfirmFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordResetController extends AbstractController
{
    private  $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/password/reset", name="app_password_reset")
     */
    public function passwordReset(Request $request, \Swift_Mailer $mailer, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(PasswordResetFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $email = $form->get('email')->getData();
            $userToUpdate =  $this->userRepository->findOneBy(['email' => $email]);

            if ($userToUpdate) {

                $token = $this->generateToken();
                $userToUpdate->setToken($token);
                $entityManager->flush();

                $body = '<a href="http://127.0.0.1:8000/password/reset/confirm/' . $token . '"> Click </a>';

                $message = (new \Swift_Message('Changement mot de passe'))
                    ->setFrom('okoafrohair@gmail.com')
                    ->setTo($email)
                    ->setBody(
                        $body,
                        'text/html'
                    );

                $mailer->send($message);

                $this->addFlash('success', 'Un mail de  réinitialisation de mot passe a été envoyé à ' . $email);
                return $this->redirectToRoute('app_accueil');
            } else {
                $this->addFlash('danger',  'L\'email **' . $email . '** n\'existe pas');
            }

        }

        return $this->render('password_reset/index.html.twig', [
            'passwordResetForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/password/reset/confirm/{token}", name="app_password_reset_confirm")
     */
    public function passwordResetConfirm(Request $request, $token, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {

        $user = new User();
        $form = $this->createForm(PasswordResetConfirmFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userToUpdate = $this->userRepository->findOneBy(['token' => $token]);

            if ($userToUpdate) {

                $userToUpdate->setToken(' ');
                $userToUpdate->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $entityManager->flush();
            } else {
                $this->addFlash('danger',  'No user found for token ' . $token);
            }

            return $this->redirectToRoute('app_accueil');
        }

        return $this->render('password_reset/confirm.html.twig', [
            'passwordResetConfirmForm' => $form->createView(),
        ]);
    }

    public function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}
