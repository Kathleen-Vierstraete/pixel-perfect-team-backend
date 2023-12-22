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
    /**
     * Displaying the backoffice
     * 
     * @param $categoryRepository, the repository to make request from the table "category" in DB
     * @param $tagRepository, the repository to make request from the table "tag" in DB
     * @param $editorRepository, the repository to make request from the table "editor" in DB
     * @param $creatorRepository, the repository to make request from the table "creator" in DB
     * @return JsonResponse
     */
    #[Route('', name: 'index', methods: "GET")]
    public function index(CategoryRepository $categoryRepository,TagRepository $tagRepository,EditorRepository $editorRepository, CreatorRepository $creatorRepository ): JsonResponse
    {
        // Getting all the categories
        $categories = $categoryRepository->findAll();

        // Getting all the tags
        $tags = $tagRepository->findAll();

        // Getting all the editors
        $editors = $editorRepository->findAll();
        
        // Getting all the creators
        $creators = $creatorRepository->findAll();

        // Returning all the entities in a JSON Object (200 = HTTP_OK)
        return $this->json(['category' => $categories, 'tag' => $tags,'editor'=> $editors, 'creator'=>$creators], 200, [], ['groups' => 'admin:crud']);
    }

}
