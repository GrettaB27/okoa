<?php

namespace App\Controller;

use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccueilController extends AbstractController
{   
    private $security;
    private $cookie;


    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/', name: 'app_accueil')]
    public function index(Request $request,SessionInterface $session): ?Response
    {
        if ($request->query->get('consent') && is_null($request->cookies->get('lastSeen'))) {
            $this->cookieConsent($request->query->get('consent'), $request);
        }

        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
            'shoppingCartCount' => count($session->get('panier') ?? []),
            'imageProfile'  => !is_null($this->security->getUser()) ?  $this->security->getUser()->getImageProfile() : ' ',
            'lastSeen' => !is_null($request->cookies->get('lastSeen')) ?  $request->cookies->get('lastSeen')  : $this->cookie ?? null
        ]);
    }

    public function cookieConsent($consent, $request)
    {

        if ($consent == 'accept') {
            $response = new Response();
            $this->cookie  = new Cookie(
                'lastSeen',
                Carbon::now(),
                Carbon::tomorrow(),
                '/', //Chemin de serveur
                'localhost', //Nom domaine
                false, //Https seulement
                false
            ); // Disponible uniquement dans le protocole HTTP
            $response->headers->setCookie($this->cookie);

            return $response->sendHeaders();
        }
        
    }
}
