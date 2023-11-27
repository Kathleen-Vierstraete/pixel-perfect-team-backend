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
    public function addPick(int $userId, Request $request, EntityManagerInterface $entityManager, PurchaseRepository $purchaseRepository, ProductRepository $productRepository, PersonRepository $personRepository, StatusRepository $statusRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $existingPurchases = $purchaseRepository->purchaseExists($userId);
        $purchase = new Purchase();

        if (!$existingPurchases) {
            $person = $personRepository->find($userId);
            $status = $statusRepository->findOneBy(['name'=>'en commande']);
            $purchase->setPerson($person);
            $purchase->setStatus($status);
            $purchase->setDatePurchase(new DateTimeImmutable('0000-00-00 00:00:00'));
            $entityManager->persist($purchase);
        }else{
            $purchase = $existingPurchases[0];
        }

        foreach ($data['product'] as $productData) {
            $product = $productRepository->find($productData['id']);
            $pick = new Pick($product, $productData['quantity'], $purchase);
            $entityManager->persist($pick);
        }

        $entityManager->flush();

        return $this->json($data);
    }
}



// #[Route('/add', name: '_add', methods: ['POST'])]
//     public function create(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, UuidFactory $uuidFactory): JsonResponse
//     {
//         $data = json_decode($request->getContent(), true);

//         $credential = new Credential($data['email'], $uuidFactory->create());
//         $credential->setPassword($passwordHasher->hashPassword($credential, $data['password']));

//         $person = new Person($data['firstName'], $data['lastName'], $data['phone']);
//         $person->setcredential($credential);
//         $credential->setperson($person);

//         $entityManager->persist($credential);
//         $entityManager->persist($person);
//         $entityManager->flush();

//         return new JsonResponse($request, 201, [], true);
//     }
