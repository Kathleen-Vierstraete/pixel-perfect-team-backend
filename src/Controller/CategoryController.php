<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;

#[Route('/api/categories', name: 'api_category_')]
class CategoryController extends AbstractController
{
     /**
     * Getting all the categories
     * 
     * @param $categoryRepository, the repository to make a request from the table "category" in DB
     * @return JsonResponse
     */
    #[Route('', name: 'index', methods: "GET")]
    public function index(CategoryRepository $categoryRepository): JsonResponse
    {
        $categories = $categoryRepository->findAll();

        return $this->json($categories, 200, [], ['groups' => 'category:crud']);
    }

     /**
     * Getting products by category
     * 
     * @param $category, the entity Category
     * @param $productRepository, the repository to make a request from the table "product" in DB
     * @return JsonResponse
     */
    #[Route('/{id<\d+>}/products', name: 'product_by_category', methods: "GET")]
    public function getProductByCategorie(Category $category, ProductRepository $productRepository): JsonResponse
    {
        $categories = $productRepository->findBy(["category" => $category]);

        return $this->json($categories, 200, [], ['groups' => 'product:crud']);
    }
}
