<?php

namespace App\Service;

use App\Repository\ProduitsRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Panier {

protected $session;
protected $produitRepository;

public function __construct(SessionInterface $session,ProduitsRepository $produitRepository)
{
$this->session=$session;
$this->produitsRepository=$produitRepository;
}

public function add(int $id){

       

//Cas N1 : Le panier n'existe pas , la session n'existe pas : je créé la session
//Cas N2 : Le panier existe , la session existe : je modifie la session
$panier = $this->session->get('panier' ) ?? [];


// Cas N3 : Le panier existe , je rajouter un produit deja existant quantite ++.
// si le tableau contient la clé identifiant correspondant au produit
// alors je rajoute une quantité ++
// si non je suis dans le cas classique
if (array_key_exists( $id, $panier) ) {
$panier [$id] = $panier [$id] + 1 ;
}
else {
// Ajouter dans le tableau [] l'identifiant et la quantité = 1
// $cart[ identifiant du produit ] = Quantité 1 par défaut
$panier [$id] = 1;
}


// on ecrit dans la session nommé 'cart' la variable $cart contenant []
// on genere un fichier sur le serveur
$this->session->set('panier',$panier);

}

public function clear(){
// supprimer la variable cart contenant un tableau enregistré en session
$this->session->remove('panier');
}

public function getFull() {

// Recuperation du panier
// Si il existe on aura le tableau rempli sinon un tableau vide
$panier=$this->session->get('panier' ) ?? [];
$panier_full = [];
// EX : $cart[5]=3 $cart[34]=2 $cart[7]=7
/* [
Identifiant produit : 5 , Quantité : 3
Identifiant produit : 34 , Quantité : 2
Identifiant produit : 7 , Quantité : 7
]
*/
// boucle sur le tableau : identifiant_produit => quantité
// Recuperer les données du produits


foreach ($panier as $id=>$quantite){
// $cart[5]=3 $cart[34]=2 $cart[7]=7
/*
$cart_full[O][produit]=[id=5,nom=chaise,prix=300]
$cart_full[O][quantite]=3
$cart_full[1][produit]=
$cart_full[1][quantite]=
...
*/
// $panier_full = [
// 'product'=> (array) $this->patisseriesRepository->find($id) ,
// 'quantite'=>$quantite
// ];

array_push($panier_full, [
'product' => $this->produitsRepository->find($id),
'quantite' => $quantite
]);

// Calcul du TOTAL uniquement
// var_dump($cart_full);



}

return $panier_full;
}

public function getTotal(){

$panier_full=$this->getFull();


$total=0;
if ($panier_full!=""){

foreach ($panier_full as $couple){
$total=$total + ($couple['product']->getPrix()*$couple['quantite']);
}
}
return $total;

}


    public function getTotalQty()
    {
        $panier_full = $this->getFull();
        $totalQty = 0;

        if ($panier_full != "") {
            foreach ($panier_full as $couple) {
                $totalQty += $couple['quantite'];
            }
        }
        return $totalQty;
    }

public function remove(int $id){


// on recupere le panier en session
$panier=$this->session->get('panier' , []);



// on verifie que l'ID est bien présent
// dans le tableau de session
if (!empty($panier [$id])){
// on supprime du tableau la clé correspondante
unset($panier [$id]);
}

// on écrit dans la sessions
$this->session->set('panier',$panier);


}

}
