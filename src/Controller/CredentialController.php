<?php

namespace App\Controller;

use App\Entity\Credential;
use App\Entity\Person;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Factory\UuidFactory;

#[Route('/api/user', name: '_user')]
class CredentialController extends AbstractController
{
    #[Route('/', name: '_get')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/CredentialController.php',
        ]);
    }

    #[Route('/add', name: '_add', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, UuidFactory $uuidFactory): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $credential = new Credential($data['email'], $uuidFactory->create());
        $credential->setPassword($passwordHasher->hashPassword($credential, $data['password']));

        $person = new Person($data['firstName'], $data['lastName'], $data['phone']);
        $person->setcredential($credential);
        $credential->setperson($person);

        $entityManager->persist($credential);
        $entityManager->persist($person);
        $entityManager->flush();

        return $this->json(['message'=>'person added'], 201);
    }
}
