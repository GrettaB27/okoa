<?php

namespace App\Controller;

use App\Service\Panier;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PanierController extends AbstractController
{
    // voir le panier 
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/panier", name="app_panier")
     */
    public function getPanier(SessionInterface $session, Panier $panier): Response
    {
        $panier_full = $panier->getFull();
        $total = $panier->getTotal();


        return $this->render('panier/view.html.twig', [
            'controller_name' => 'PanierController',
            'rows' => $panier_full,
            'total' => $total,
            'shoppingCartCount' => $panier->getTotalQty(),
            'imageProfile'  => !is_null($this->security->getUser()) ?  $this->security->getUser()->getImageProfile() : ' ',

        ]);
    }
    /**
     * @Route("/panier/add/{id}", name="app_panier_add")
     */
    public function addItem(
        $id,
        Panier $panier
    ): Response {
        $panier->add($id);
        return $this->redirectToRoute('app_boutique_index');
    }
    /**
     * @Route("/panier/remove/{id}", name="app_panier_remove")
     */
    public function remove($id, Panier $panier)
    {
        // on utilise le service pour supprimer de notre panier
        $panier->remove($id);

        // on redirige
        return $this->redirectToRoute('app_panier');
    }
}
// faire en sorte que lorsque le panier est vide la vue fonctionne
// une problématique SQL
// on verra l'utilisation de STRIPE 
// Hebergement
// Mail
