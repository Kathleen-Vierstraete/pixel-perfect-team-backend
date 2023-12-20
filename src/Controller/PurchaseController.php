<?php

namespace App\Controller;

use App\Entity\Purchase;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/purchases', name: 'purchase')]
class PurchaseController extends AbstractController
{
    #[Route('/{id<\d+>}/status', name: '_set_status', methods: ['GET'])]
    public function setPurchaseStatus(Purchase $purchase, StatusRepository $statusRepository, EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $status = $statusRepository->findOneBy(['name' => $data["name"]]);
        if (!$status) {
            return $this->json(
                [
                    'error' => 'Status non trouvé'
                ],
                Response::HTTP_NOT_FOUND,
            );
        }
        $purchase->setStatus($status);
        $entityManager->persist($purchase);
        $entityManager->flush();
        return $this->json($purchase, 200, [], ['groups' => 'purchase:crud']);
    }
}
