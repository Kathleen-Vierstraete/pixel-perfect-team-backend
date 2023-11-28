<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AddressRepository;

#[Route('/api', name: 'api_')]
class AddressController extends AbstractController
{
    #[Route('/addresses', name: 'address_index', methods: ['get'])]
    public function index(AddressRepository $addressRepository): JsonResponse
    {
        $addresses = $addressRepository->findAll();

        return $this->json($addresses, 200, [], ['groups' => 'address:read']);
    }
}
