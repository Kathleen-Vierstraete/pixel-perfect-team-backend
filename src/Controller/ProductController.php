<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class ProductController extends AbstractController
{   
    #[Route('/products', name: 'product_index', methods: ['get'])]
    public function index(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findAll();

        return $this->json($products, 200, [], ['groups'=>'product:read']);
    }

    #[Route('/products/{id<\d+>}', name: 'product_by_id', methods: ['get'])]
    public function getById(Product $product = null) : Response
    {
        if(!$product){
            return $this->json(
                [
                    'error' => 'Produit non trouvé'
                ],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json($product, 200, [], ['groups'=>'product:find_one']);
    }

    
}
