<?php

namespace App\Controller;

use App\Entity\Pick;
use App\Entity\Purchase;
use App\Repository\PersonRepository;
use App\Repository\PickRepository;
use App\Repository\ProductRepository;
use App\Repository\PurchaseRepository;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/api/picks', name: 'panier')]
class PickController extends AbstractController
{
    #[Route('/{id<\d+>}', name: 'GetPick', methods: ['GET'])]
    public function getById(PickRepository $pickRepository, int $id): JsonResponse
    {

        $pick = $pickRepository->findByIdPerson($id);

        return $this->json($pick, 200, [], ['groups' => 'pick:crud']);
    }
}
