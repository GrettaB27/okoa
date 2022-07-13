<?php

namespace App\Service;

use Stripe\Stripe;
use App\Service\Panier;
use Stripe\PaymentIntent;


class Paiement
{

    private $panier;

    public function __construct(Panier $panier)
    {
        $this->panier = $panier;
    }

    public function create()
    {


        $total = $this->panier->getTotal();

        // This is your test secret API key.
        \Stripe\Stripe::setApiKey('sk_test_51KquOiB0zYLWMil6j4jCHBliCYwJ4V7YLZ99LTKQtuSuROb2MIF0oDOsaBURV80vfAKSifXUc5w4wBEh0pZbTfmf001Ft68wtU');

        // Create a PaymentIntent with amount and currency
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $total * 100,
            'currency' => 'eur',
        ]);

        return $paymentIntent;
    }
}
