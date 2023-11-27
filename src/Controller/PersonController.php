<?php

namespace App\Controller;

use App\Entity\Person;
use App\Repository\PersonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/person', name: '_person')]
class PersonController extends AbstractController
{
    #[Route('/', name: 'Get_All_Person', methods: ['GET'])]
    public function getAllPerson(PersonRepository $personRepository): JsonResponse
    {
        $person = $personRepository->findAll();

        return $this->json($person, 200, [], ['groups'=>'person:crud']);
    }
    #[Route('/{id<\d+>}', name: 'person_by_id', methods: ['GET'])]
    public function getById(Person $person = null) : JsonResponse
    {
        if(!$person){
            return $this->json(
                [
                    'error' => 'Utilisateur non trouvé'
                ],
                JsonResponse::HTTP_NOT_FOUND,
            );
        }
        return $this->json($person, 200, [], ['groups'=>'person:crud']);
    }
}
