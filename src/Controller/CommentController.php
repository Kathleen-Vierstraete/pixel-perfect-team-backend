<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Person;
use App\Entity\Product;
use App\Repository\CommentRepository;
use App\Repository\ProductRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function Symfony\Component\Clock\now;

#[Route('/api/comment', name: 'api_comment_')]
class CommentController extends AbstractController
{
    #[Route('/{id<\d+>}', name: 'get_by_product', methods: ['get'])]
    public function getByProduct(ProductRepository $productRepository, int $id): Response
    {
        // Getting the product 
        $product = $productRepository->find($id);

        // Getting all its comments
        $comments = $product->getComments();

        // Return them
        return $this->json($comments, 200, [], ['groups' => 'comment:crud']);
    }

    #[Route('/add', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {

        // Creating a Product Entity
        $comment = new Comment();

        // Getting the content
        $commentData = json_decode($request->getContent(), true);

        // Setting body, title and rate
        $properties = ['body', 'title', 'rate', 'vote'];

        foreach ($properties as $property) {
            if (isset($commentData[$property])) {
                $setterMethod = 'set' . ucfirst($property);
                $comment->$setterMethod($commentData[$property]);
            }
        }

        // Setting date
        $comment->setDate(new DateTime('now'));

        // Setting person
        $personId = $commentData['person_id'];
        $person = $entityManager->getRepository(Person::class)->find($personId);
        $comment->setPerson($person);

        // Setting product
        $productId = $commentData['product_id'];
        $product = $entityManager->getRepository(Product::class)->find($productId);
        $comment->setProduct($product);

        // Saving the entity
        $entityManager->persist($comment);
        $entityManager->flush();

        // Returning the entity comment in JSON & connecting it through groups (204 = HTTP_CREATED)
        return $this->json($comment, 204, [], ['groups' => 'comment:crud']);
    }

    #[Route('/update/{id<\d+>}', name: 'update', methods: ['PATCH'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager, CommentRepository $commentRepository): JsonResponse
    {

        // Getting the Product Entity to update
        $comment = $commentRepository->find($id);

        // Decode the the content
        $commentData = json_decode($request->getContent(), true);

        // Setting body, title, rate & vote
        $properties = ['body', 'title', 'rate', 'vote'];

        foreach ($properties as $property) {
            if (isset($commentData[$property])) {
                $setterMethod = 'set' . ucfirst($property);
                $comment->$setterMethod($commentData[$property]);
            }
        }

        // Setting person
        $personId = $comment->getPerson();
        $person = $entityManager->getRepository(Person::class)->find($personId);
        $comment->setPerson($person);

        // Setting product
        $productId = $comment->getProduct();
        $product = $entityManager->getRepository(Product::class)->find($productId);
        $comment->setProduct($product);

        // Saving the entity
        $entityManager->persist($comment);
        $entityManager->flush();

        // Returning the entity comment in JSON & connecting it through groups (204 = HTTP_CREATED)
        return $this->json($comment, 204, [], ['groups' => 'comment:crud']);
    }

    #[Route('/delete/{id<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager, CommentRepository $commentRepository): JsonResponse
    {
        $comment = $commentRepository->find($id);
        $entityManager->remove($comment);
        $entityManager->flush();

        return $this->json($comment, 200, [], ['groups' => 'comment:crud']);
    }
}