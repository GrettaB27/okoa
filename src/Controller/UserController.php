<?php

namespace App\Controller;

use App\Service\Panier;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
 
    private $security;

    public function __construct(Security $security, Panier $panier)
    {
        $this->security = $security;
        $this->panier =$panier;
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
            'shoppingCartCount' => $this->panier->getTotalQty(),
            'imageProfile'  => !is_null($this->security->getUser()) ?  $this->security->getUser()->getImageProfile() : ' ',
        ]);
    }

    public  function hasAdminRole($roles = [])
    {
        return $roles[array_search('ROLE_ADMIN', $roles)] == 'ROLE_ADMIN';
    }
}
