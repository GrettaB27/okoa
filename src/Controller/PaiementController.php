<?php

namespace App\Controller;

use App\Service\Panier;
use App\Service\Paiement;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaiementController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    
    /**
     * @Route("/paiement", name="app_paiement")
     */
    public function index(SessionInterface $session, Paiement $paiement, Panier $panier): Response
    {
        $total = $panier->getTotal();
        $intent = $paiement->create();

        return $this->render('paiement/index.html.twig', [
            'clientSecret' => $intent->client_secret,
            'total' => $total,
            'shoppingCartCount' => count($session->get('panier') ?? []),
            'imageProfile'  => !is_null($this->security->getUser()) ?  $this->security->getUser()->getImageProfile() : ' ',
        ]);
    }
}