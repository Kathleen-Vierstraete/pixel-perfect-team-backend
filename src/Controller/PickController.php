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
    /**
     * Getting one pick by it's id
     * 
     * @param $id, the id of the Pick object
     * @param $pickRepository, the repository to make request from the table "pick" in DB
     * @return JsonResponse
     */
    #[Route('/{id<\d+><}', name: 'GetPick', methods: ['GET'])]
    public function getById(PickRepository $pickRepository, int $id): JsonResponse
    {

        $pick = $pickRepository->findByIdPerson($id);

        return $this->json($pick, 200, [], ['groups' => 'pick:crud']);
    }
}
