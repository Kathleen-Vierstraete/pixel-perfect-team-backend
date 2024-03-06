<?php

namespace App\Controller;

use App\Entity\Address;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/addresses', name: 'api_addresse')]
class AddressController extends AbstractController
{
    /**
     * Updating an address
     * 
     * @param $address, the entity Address
     * @param $request, the request to the DB
     * @param $entityManager, the EntityManagerInterface to make the relation with the DB
     * @param $serializer, SerializerInterface used to deserialize the request
     * @return JsonResponse
     */
    #[Route('/{id<\d+>}', name: '_update', methods: ['PUT'])]
    public function updateAddresses(Address $address, Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        // Deserializing the JSON Object to an Address Object
        $serializer->deserialize($request->getContent(), Address::class, 'json', ['object_to_populate' => $address]);
        // Saving it in the DB
        $entityManager->persist($address);
        $entityManager->flush();
        
        // Returning the entity Address in JSON (200 = HTTP_OK)
        return $this->json($address, 200, [], ['groups' => 'address:crud']);
    }

    /**
     * Deleting an address
     * 
     * @param $address, the entity Address
     * @param $entityManager, the EntityManagerInterface to make the relation with the DB
     * @return JsonResponse
     */
    #[Route('/{id<\d+>}', name: '_delete', methods: ['DELETE'])]
    public function deleteAddresses(Address $address, EntityManagerInterface $entityManager): JsonResponse
    {
        // Removing the Address Object from the DB
        $entityManager->remove($address);
        // Saving it in the DB
        $entityManager->flush();

        // Returning a message in a JSON Object (200 = HTTP_OK)
        return $this->json(['message' => 'Address deleted successfully'], 200);
    }

    // Test 6 mars 2024
}