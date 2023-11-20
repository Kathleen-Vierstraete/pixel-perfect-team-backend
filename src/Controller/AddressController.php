<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Address;
use App\Repository\AddressRepository;

<<<<<<< HEAD
class AddressController extends AbstractController
{
    #[Route('/address', name: 'app_address')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/AddressController.php',
        ]);
=======
#[Route('/api', name: 'api_')]
class AddressController extends AbstractController
{
    #[Route('/addresses', name: 'address_index', methods: ['get'])]
    public function index(AddressRepository $addressRepository): JsonResponse
    {
        $addresses = $addressRepository->findAll();

        return $this->json($addresses, 200, [], ['groups' => 'address:read']);
>>>>>>> 5327beda1ce5dd15e952a0b283a9aafd8be0f00e
    }

}
