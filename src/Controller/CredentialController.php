<?php

namespace App\Controller;

use App\Entity\Credential;
use App\Entity\Person;
use App\Repository\CredentialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Factory\UuidFactory;

#[Route('/api/users', name: '_user')]
class CredentialController extends AbstractController
{
    /**
     * Create a new user in the database
     * 
     * @param $request, the request to the DB
     * @param $entityManager, the EntityManagerInterface to make the relation with the DB
     * @param $passwordHasher, the password hasher object to hash the password
     * @param $uuidFactory, the UUID factory object to generate a UUID
     * @return JsonResponse
     */
    #[Route('', name: '_add', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, UuidFactory $uuidFactory): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $credential = new Credential($data['email'], $uuidFactory->create());
        $credential->setPassword($passwordHasher->hashPassword($credential, $data['password']));

        $person = new Person($data['firstName'], $data['lastName'], $data['phone']);
        $person->setcredential($credential);
        $credential->setperson($person);
        $existingUser = $entityManager->getRepository(Credential::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return $this->json(['message' => 'User with this email already exists'], 400);
        }
        $entityManager->persist($credential);
        $entityManager->persist($person);
        $entityManager->flush();

        return $this->json(['message' => 'person added'], 201);
    }

    /**
     * Verify one user's e-mail by their reset token
     * 
     * @param $resetToken, the reset token used for the verification
     * @param $credentialRepository, the repository to make request from the table "credential" in DB
     * @return JsonResponse
     */
    #[Route('/verify/{resetToken}', name: '_verify')]
    public function verify(string $resetToken, CredentialRepository $credentialRepository): JsonResponse
    {
        $userIsExist = $credentialRepository->findOneBy(['resetToken' => $resetToken]);

        $credentialRepository->add($userIsExist->setIsVerified(true),true);

        return $this->json(['message' => 'user is verified']);
    }
    /**
     * Getting one user by their id
     * 
     * @param $credential, the id of the Credential object
     * @return JsonResponse
     */
    #[Route('/{id<\d+>}', name: '_getbyid',methods: ['GET'])]
    public function getById(Credential $credential): JsonResponse
    {
        
        return $this->json($credential->getperson(), 200, [], ['groups' => 'person:crud']);
    }
}
