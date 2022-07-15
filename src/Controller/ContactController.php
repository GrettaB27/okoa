<?php

namespace App\Controller;

use App\Service\Panier;
use App\Form\ContactFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    private $security;
    private $panier;

    public function __construct(Security $security ,Panier $panier)
    {
        $this->security = $security;
        $this->panier= $panier;
    }

    /**
     * @Route("/contact", name="app_contact")
     */
    public function index(Request $request, SessionInterface $session, \Swift_Mailer $mailer): Response
    {

   $form = $this->createForm(ContactFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getViewData(); // data post ou data envoyer depuis le formulaire

            $message = (new \Swift_Message($data['sujet']))
            ->setFrom('okoafrohair@gmail.com')
            ->setTo($data['email'])
            ->setBody(
                $data['message'],
                'text/html'
            );



            $mailer->send($message);

            $this->addFlash('success', 'Votre message a été bien envoyé ! Nous vous recontacterons dans les plus brefs délais.');

            return $this->render(
                // templates/emails/registration.html.twig
                'accueil/index.html.twig',
                [
                    'shoppingCartCount' => $this->panier->getTotalQty(),
                    
                ]
                );
            // $entityManager->persist($user);
            // $entityManager->flush();

            // generate a signed url and email it to the user

            // do anything else you need here, like send an email
        }

        return $this->render('contact/index.html.twig', [
            'contactForm' => $form->createView(),
            'shoppingCartCount' => $this->panier->getTotalQty(),
            'imageProfile'  => !is_null($this->security->getUser()) ?  $this->security->getUser()->getImageProfile() : ' ',
        ]);
    }

}
