<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;

#[Route('/api/categories', name: 'api_category_')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoryRepository $categoryRepository ): JsonResponse
    {
        $categories= $categoryRepository->findAll();

        return $this->json($categories, 200, [], ['groups' => 'category:crud']);
    }
}
