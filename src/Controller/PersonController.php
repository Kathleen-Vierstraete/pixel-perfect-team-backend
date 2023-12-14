<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Credential;
use App\Entity\Person;
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
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/persons', name: '_person')]
class PersonController extends AbstractController
{
    #[Route('', name: 'Get_All_Person', methods: ['GET'])]
    public function getAllPerson(PersonRepository $personRepository): JsonResponse
    {
        $person = $personRepository->findAll();

        return $this->json($person, 200, [], ['groups' => 'person:crud']);
    }
    #[Route('/{id<\d+>}', name: 'person_by_id', methods: ['GET'])]
    public function getById(Person $person = null): JsonResponse
    {
        if (!$person) {
            return $this->json(
                [
                    'error' => 'Utilisateur non trouvé'
                ],
                JsonResponse::HTTP_NOT_FOUND,
            );
        }
        return $this->json($person, 200, [], ['groups' => 'person:crud']);
    }

    #[Route('/{id<\d+>}', name: 'patch_person', methods: ['patch'])]
    public function updatePerson(Person $person, Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $serializer->deserialize($request->getContent(), Person::class, 'json', [
            'object_to_populate' => $person,
            'ignore_attributes' => ['id'],
        ]);

        $credentialData = json_decode($request->getContent(), true);
        if (isset($credentialData['email'])) {
            $person->getcredential()->setEmail($credentialData['email']);
        }

        $entityManager->flush();

        return $this->json($person, 200, [], ['groups' => 'person:crud']);
    }

    #[Route('/{id<\d+>}/picks', name: '_add_picks', methods: 'POST')]
    public function addPick(int $id, Request $request, EntityManagerInterface $entityManager, PickRepository $pickRepository, PurchaseRepository $purchaseRepository, ProductRepository $productRepository, PersonRepository $personRepository, StatusRepository $statusRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $existingPurchases = $purchaseRepository->purchaseExists($id);
        $purchase = new Purchase();

        if (!$existingPurchases) {
            $person = $personRepository->find($id);
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

        foreach ($picks as $pick) {
            $entityManager->persist($pick);
        }

        $entityManager->flush();


        return $this->json($picks, context: ['groups' => "pick:crud"]);
    }

    #[Route('/{id<\d+>}/picks', name: '_delete_picks', methods: ['DELETE'])]
    public function deletePick(int $id, EntityManagerInterface $entityManager, PickRepository $pickRepository,): JsonResponse
    {
        $picks = $pickRepository->findByIdPerson($id);

        foreach ($picks as $pick) {
            $entityManager->remove($pick);
        }

        $entityManager->flush();

        return $this->json("{}", 200);
    }

    #[Route('/{id<\d+>}/purchases', name: '_get_purchase', methods: ['GET'])]
    public function purchases(Person $person): JsonResponse
    {
        return $this->json($person->getPurchases(), 200, [], ['groups' => 'purchase:crud']);
    }

    #[Route('/{id<\d+>}/addresses', name: '_add_addresse', methods: ['POST'])]
    public function addAddresses(Person $person, Request $request, EntityManagerInterface $entityManager,): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $addresse = new Address(intval($data["streetNumber"]), $data["streetName"], $data["city"], intval($data["zipcode"]));
        $addresse->setPerson($person);
        $entityManager->persist($addresse);
        $entityManager->flush();
        return $this->json($addresse, 200, [], ['groups' => 'purchase:crud']);
    }

    #[Route('/{id<\d+>}/comments', name: '_get_comments', methods: ['GET'])]
    public function getCommentsByPerson(Person $person): JsonResponse
    {

        return $this->json($person->getComments(), 200, [], ['groups' => 'comment:crud']);
    }
}
