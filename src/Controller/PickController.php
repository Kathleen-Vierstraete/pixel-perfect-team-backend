<?php

namespace App\Controller;

use App\Entity\Person;
use App\Entity\Pick;
use App\Entity\Product;
use App\Entity\Purchase;
use App\Repository\PersonRepository;
use App\Repository\PickRepository;
use App\Repository\ProductRepository;
use App\Repository\PurchaseRepository;
use App\Repository\StatusRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/add/{userId<\d+>}', name: '_add', methods: 'POST')]
    public function addPick(int $userId, Request $request, EntityManagerInterface $entityManager, PickRepository $pickRepository, PurchaseRepository $purchaseRepository, ProductRepository $productRepository, PersonRepository $personRepository, StatusRepository $statusRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $existingPurchases = $purchaseRepository->purchaseExists($userId);
        $purchase = new Purchase();

        if (!$existingPurchases) {
            $person = $personRepository->find($userId);
            $status = $statusRepository->findOneBy(['name' => 'en commande']);
            $purchase->setPerson($person)
                ->setStatus($status);
            $entityManager->persist($purchase);
        } else {
            $purchase = $existingPurchases[0];
        }

        $picks = [];
        foreach ($data['product'] as $productData) {
            $product = $productRepository->find($productData['id']);
            $pick = $pickRepository->findOneBy(['product' => $product->getId(), 'purchase' => $purchase->getId()]);
            if ($pick) {
                array_push($picks, $pick->setQuantity($pick->getQuantity() + $productData['quantity']));
                continue;
            }

            array_push($picks, new Pick($purchase, $productData['quantity'], $product));
        }

        foreach ($picks as $pick){
            $entityManager->persist($pick);
        }

        $entityManager->flush();


        return $this->json($picks, context: ['groups' => "pick:crud"]);
    }

    #[Route('/{userId<\d+>}', name: '_delete', methods: ['DELETE'])]
    public function delete(int $userId, EntityManagerInterface $entityManager,PickRepository $pickRepository,): JsonResponse
    {
        $picks = $pickRepository->findByIdPerson($userId);

        foreach ($picks as $pick) {
            $entityManager->remove($pick);
        }

        $entityManager->flush();

        return $this->json("{}",204);
    }
}
