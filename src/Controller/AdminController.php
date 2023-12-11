<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\CreatorRepository;
use App\Repository\EditorRepository;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/api/administrators', name: 'api_admin_')]
class AdminController extends AbstractController
{
    #[Route('', name: 'index', methods: "GET")]
    public function index(CategoryRepository $categoryRepository,TagRepository $tagRepository,EditorRepository $editorRepository, CreatorRepository $creatorRepository ): JsonResponse
    {
        $categories = $categoryRepository->findAll();
        $tags = $tagRepository->findAll();
        $editors = $editorRepository->findAll();
        $creators = $creatorRepository->findAll();

        return $this->json(['category' => $categories, 'tag' => $tags,'editor'=> $editors, 'creator'=>$creators], 200, [], ['groups' => 'admin:crud']);
    }

}
