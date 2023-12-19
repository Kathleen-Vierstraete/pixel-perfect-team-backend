<?php

namespace App\Controller;

use App\Entity\Address;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/addresses', name: 'api_addresse')]
class AddressController extends AbstractController
{
    #[Route('/{id<\d+>}', name: '_update', methods: ['PUT'])]
    public function updateAddresses(Address $address, Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $serializer->deserialize($request->getContent(), Address::class, 'json', ['object_to_populate' => $address]);
        $entityManager->flush();
        
        return $this->json($address, 200, [], ['groups' => 'address:crud']);
    }

    #[Route('/{id<\d+>}', name: '_delete', methods: ['DELETE'])]
    public function deleteAddresses(Address $address, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($address);
        $entityManager->flush();

        return $this->json(['message' => 'Address deleted successfully'], 200);
    }
}
