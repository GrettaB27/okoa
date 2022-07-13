<?php

namespace App\Controller;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HistoireController extends AbstractController
{

    private $security;


    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    
    #[Route('/histoire', name: 'app_histoire')]
    public function index(SessionInterface $session): Response
    {
        return $this->render('histoire/index.html.twig', [
            'controller_name' => 'HistoireController',
            'shoppingCartCount' => count($session->get('panier') ?? []),
            'imageProfile'  => !is_null($this->security->getUser()) ?  $this->security->getUser()->getImageProfile() : ' ',
        ]);
    }
}
