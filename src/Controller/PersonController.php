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
use Symfony\Component\Uid\Factory\UuidFactory;

#[Route('/api/persons', name: '_person')]
class PersonController extends AbstractController
{
    /**
     * Getting all the users
     * 
     * @param $personRepository, the repository to make request from the table "person" in DB
     * @return JsonResponse
     */
    #[Route('', name: 'Get_All_Person', methods: ['GET'])]
    public function getAllPerson(PersonRepository $personRepository): JsonResponse
    {
        $person = $personRepository->findAll();

        return $this->json($person, 200, [], ['groups' => 'person:crud']);
    }

    /**
     * Getting one person by their id
     * 
     * @param $person, the id of the Person object
     * @return JsonResponse
     */
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

    /**
     * Change one person's data in the database
     * 
     * @param $person, the id of the Person object
     * @param $request, the request to the DB
     * @param $entityManager, the EntityManagerInterface to make the relation with the DB
     * @param $serializer, SerializerInterface used to deserialize the request
     * @return JsonResponse
     */
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

    /**
     * Create a new Pick to a person in the database
     * 
     * @param $id, the id of the Person object
     * @param $request, the request to the DB
     * @param $entityManager, the EntityManagerInterface to make the relation with the DB
     * @param $pickRepository, the repository to make request from the table "pick" in DB
     * @param $purchaseRepository, the repository to make request from the table "purchase" in DB
     * @param $productRepository, the repository to make request from the table "product" in DB
     * @param $personRepository, the repository to make request from the table "person" in DB
     * @param $statusRepository, the repository to make request from the table "status" in DB
     * @param $uuidFactory, UUID Factory called to generate a new UUID
     * @return JsonResponse
     */
    #[Route('/{id<\d+>}/picks', name: '_add_picks', methods: 'POST')]
    public function addPick(int $id, Request $request, EntityManagerInterface $entityManager, PickRepository $pickRepository, PurchaseRepository $purchaseRepository, ProductRepository $productRepository, PersonRepository $personRepository, StatusRepository $statusRepository, UuidFactory $uuidFactory): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $existingPurchases = $purchaseRepository->purchaseExists($id);
        $purchase = new Purchase();

        if (!$existingPurchases) {
            $person = $personRepository->find($id);
            $status = $statusRepository->findOneBy(['name' => 'en commande']);
            $purchase->setPerson($person)
                ->setStatus($status)
                ->setReference($uuidFactory->create());
            $entityManager->persist($purchase);
        } else {
            $purchase = $existingPurchases[0];
        }

        $picks = [];
        foreach ($data['product'] as $productData) {
            $product = $productRepository->find($productData['id']);
            $pick = $pickRepository->findOneBy(['product' => $product->getId(), 'purchase' => $purchase->getId()]);
            if ($pick) {
                array_push($picks, $pick->setQuantity($productData['quantity']));
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

    /**
     * Getting all the Picks associated to one id to delete them
     * 
     * @param $id, the id of the Person object
     * @param $entityManager, the EntityManagerInterface to make the relation with the DB
     * @param $pickRepository, the repository to make request from the table "pick" in DB
     * @return JsonResponse
     */
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

    /**
     * Getting all the purchases of one person by their id
     * 
     * @param $person, the id of the Person object
     * @return JsonResponse
     */
    #[Route('/{id<\d+>}/purchases', name: '_get_purchase', methods: ['GET'])]
    public function purchases(Person $person): JsonResponse
    {
        return $this->json($person->getPurchases(), 200, [], ['groups' => 'purchase:crud']);
    }

    /**
     * Create a new address and link it to a user
     * 
     * @param $person, the id of the Person object
     * @param $request, the request to the DB
     * @param $entityManager, the EntityManagerInterface to make the relation with the DB
     * @return JsonResponse
     */
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

    /**
     * Getting all the addresses related to one user by it's id
     * 
     * @param $person, the id of the Person object
     * @return JsonResponse
     */
    #[Route('/{id<\d+>}/addresses', name: '_get_addresse', methods: ['GET'])]
    public function getAddresses(Person $person): JsonResponse
    {
        return $this->json($person->getAddresses(), 200, [], ['groups' => 'address:read']);
    }
}
