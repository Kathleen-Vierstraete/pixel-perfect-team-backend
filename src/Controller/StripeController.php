<?php

namespace App\Controller;

use Exception;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/stripe', name: 'api_stripe_')]
class StripeController extends AbstractController
{
    #[Route('/create', name: 'create', methods:['POST'])]
    public function createPaymentIntent(Request $request): JsonResponse
    {
        // Initialize Stripe with your secret key
        Stripe::setApiKey($_ENV["STRIPE_SECRET"]);

        // Retrieve necessary data from the request (e.g., items, amount, currency)
        // $requestData = json_decode($request->getContent(), true);
        // // TODO : get properties
        // $items = $requestData['items'];

        try {
            // Create a PaymentIntent
            $paymentIntent = PaymentIntent::create([
                'amount' => 1000, // Replace with the amount in cents
                'currency' => 'usd', // Replace with your desired currency
                'description' => 'Payment for items', // Replace with description
                // Add any additional parameters as needed
            ]);

            // Return the client secret in the response
            return $this->json(['clientSecret' => $paymentIntent->client_secret]);
        } catch (Exception $e) {
            // Handle any errors
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }
}
