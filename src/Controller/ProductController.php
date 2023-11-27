<?php

namespace App\Controller;

use App\Entity\Creator;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/products', name: 'api_products_')]
class ProductController extends AbstractController
{
    /** Getting all products
     *  */ 
    #[Route('/', name: 'index', methods: ['get'])]
    public function index(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findAll();

        return $this->json($products, 200, [], ['groups' => 'product:read']);
    }

    /** Getting a product by its ID
     *  */ 
    #[Route('/{id<\d+>}', name: 'by_id', methods: ['get'])]
    public function getById(Product $product = null): Response
    {
        if (!$product) {
            return $this->json(
                [
                    'error' => 'Produit non trouvé'
                ],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json($product, 200, [], ['groups' => 'product:read']);
    }

    #[Route('/add', name: 'creation', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializerInterface, ValidatorInterface $validatorInterface, ManagerRegistry $managerRegistry): JsonResponse
    {
        $creator = new Creator;
        
        
        // Getting the JSON content
        $json = $request->getContent();

        // Managing error
        try {
            // Deserializing the JSON content in the class Product
            $product = $serializerInterface->deserialize($json, Product::class, 'json');

        } catch (NotEncodableValueException $error) {
            return $this->json(
                ["error" => "JSON invalide"],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        // Validating the product entity
        $errors = $validatorInterface->validate($product);

        if (count($errors) > 0) {
            return $this->json(
                $errors,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        // Saving the product entity
        $entityManager = $managerRegistry->getManager();
        $entityManager->persist($product);
        $entityManager->flush();

        // Return the response
        return $this->json($product, Response::HTTP_CREATED, [], ["groups" => "product:create"]);
    }
}
