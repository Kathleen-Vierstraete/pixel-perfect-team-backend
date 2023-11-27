<?php

namespace App\Controller;

use App\Repository\PickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/pick', name: 'panier')]
class PickController extends AbstractController
{
    #[Route('/{id<\d+>}', name: 'GetPick', methods: ['GET'])]
    public function getById(PickRepository $pickRepository, int $id): JsonResponse
    {
        
    $pick = $pickRepository->findByIdPerson($id);

    return $this->json($pick, 200, [], ['groups' => 'pick:crud']);
    }
}
