<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Purchase;
use App\Repository\StatusRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/purchases', name: 'purchase')]
class PurchaseController extends AbstractController
{
    /**
     * Get the status of a purchase and sets a purchase and delivery date for current purchases
     * 
     * @param $purchase, id of the Purchase object to change
     * @param $statusRepository, the repository to make request from the table "status" in DB
     * @param $entityManager, the EntityManagerInterface to make the relation with the DB
     * @param $request, the request to the DB
     * @return JsonResponse
     */
    #[Route('/{id<\d+>}/status', name: '_set_status', methods: ['PUT'])]
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

        if ($status->getName() == "en cours") {
            $purchase->setDatePurchase(new DateTime());
            $purchase->setDateExpectedDelivery(new DateTime('now + 5 day'));
        }

        $purchase->setStatus($status);
        $entityManager->persist($purchase);
        $entityManager->flush();
        return $this->json($purchase, 200, [], ['groups' => 'purchase:crud']);
    }

    /**
     * Puts an address to a purchase
     * 
     * @param $purchase, id of the Purchase object to change
     * @param $address, id of the Address object to change
     * @param $entityManager, the EntityManagerInterface to make the relation with the DB
     * @param $request, the request to the DB
     * @return JsonResponse
     */
    #[Route('/{purchase<\d+>}/addresses/{address<\d+>}', name: '_set_address', methods: ['PUT'])]
    public function setPurchaseAddress(Purchase $purchase, Address $address, EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $purchase->setAddresses($address);
        $entityManager->persist($purchase);
        $entityManager->flush();
        return $this->json($purchase, 200, [], ['groups' => 'purchase:crud']);
    }
}
