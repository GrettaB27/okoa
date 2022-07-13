<?php

namespace App\Controller;

use App\Service\Panier;
use App\Entity\Produits;
use App\Form\ProduitsType;
use App\Repository\ProduitsRepository;
use App\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/boutique")
 */
class BoutiqueController extends AbstractController
{   
    public $produitsRepository;
    public $categorieRepository;
    public $panier;
    public $produits;
    private $security;


    public function __construct(
        ProduitsRepository $produitsRepository,
        CategorieRepository $categorieRepository,
        Panier $panier,Security $security
       
    )
    {
        $this->produitsRepository = $produitsRepository;
        $this->categorieRepository = $categorieRepository;
        $this->panier = $panier;
        $this->produits = new Produits(); 
        $this->security = $security;
    }


    /**
     * @Route("/", name="app_boutique_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('boutique/index.html.twig', [
            'categories' => $this->categorieRepository->findAll(),
            'produits' => $this->produitsRepository->findAll(),
            'shoppingCartCount' => $this->panier->getTotalQty(),
            'imageProfile'  => !is_null($this->security->getUser()) ?  $this->security->getUser()->getImageProfile() : ' ',
        ]);
    }


    /**
     * @Route("/categories/{id}/products", name="app_categories_products_show", methods={"GET"})
     */
    public function getProductsByCategory($id): Response
    { 
        
        return $this->render('boutique/index.html.twig', [
            'categories' => $this->categorieRepository->findAll(),
            'produits' => $this->categorieRepository->findOneById($id)->getProduits()->toArray(),
            'shoppingCartCount' => $this->panier->getTotalQty()
        ]);
    }


    /**
     * @Route("/new", name="app_boutique_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $produits = new Produits();
        $form = $this->createForm(ProduitsType::class, $produits);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->produitsRepository->add($produits);
            return $this->redirectToRoute('app_boutique_index', ['shoppingCartCount' => $this->panier->getTotalQty()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('boutique/new.html.twig', [
            'produits' => $produits,
            'form' => $form,
            'shoppingCartCount' => $this->panier->getTotalQty()
        ]);
    }

    /**
     * @Route("/{id}", name="app_boutique_show", methods={"GET"})
     */
    public function show(): Response
    {
        return $this->render('boutique/show.html.twig', [
            'produits' => $this->produits,
            'shoppingCartCount' => $this->panier->getTotalQty()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_boutique_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request): Response
    {
        $form = $this->createForm(ProduitsType::class, $this->produits);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->produitsRepository->add($this->produits);
            return $this->redirectToRoute('app_boutique_index', ['shoppingCartCount' => $this->panier->getTotalQty()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('boutique/edit.html.twig', [
            'produits' => $this->produits,
            'form' => $form,
            'shoppingCartCount' => $this->panier->getTotalQty()
        ]);
    }

    /**
     * @Route("/{id}", name="app_boutique_delete", methods={"POST"})
     */
    public function delete(Request $request, Produits $produits): Response
    {
        if ($this->isCsrfTokenValid('delete' . $produits->getId(), $request->request->get('_token'))) {
            $this->produitsRepository->remove($produits);
        }

        return $this->redirectToRoute('app_boutique_index', ['shoppingCartCount' => $this->panier->getTotalQty()], Response::HTTP_SEE_OTHER);
    }
}

