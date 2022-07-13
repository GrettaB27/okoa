<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class UserController extends AbstractController
{
 
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    /**
     * @Route("/user/profile/me", name="app_user_profile")
     */
    public function index(SessionInterface $session): Response
    {

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'lastName' => $this->security->getUser()->getNom(),
            'firstName' => $this->security->getUser()->getPrenom(),
            'hasAdminRole' => $this->hasAdminRole($this->security->getUser()->getRoles() ?? []),
            'shoppingCartCount' => count($session->get('panier') ?? []),
            'imageProfile'  => !is_null($this->security->getUser()) ?  $this->security->getUser()->getImageProfile() : ' ',
        ]);
    }

    public  function hasAdminRole($roles = [])
    {
        return $roles[array_search('ROLE_ADMIN', $roles)] == 'ROLE_ADMIN';
    }
}
