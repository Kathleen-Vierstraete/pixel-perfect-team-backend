<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Person;
use App\Entity\Product;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/comments', name: 'api_comment_')]
class CommentController extends AbstractController
{
    /** 
     * Creating a new comment
     * 
     * @param $request, a Request entity to call the database
     * @param $entityManager, the manager to persist the data
     *  */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Creating a Product Entity
        $comment = new Comment();

        // Getting the content
        $commentData = json_decode($request->getContent(), true);

        // Setting body, title, rate & vote
        $properties = ['body', 'title', 'rate'];

        foreach ($properties as $property) {
            if (isset($commentData[$property])) {
                $setterMethod = 'set' . ucfirst($property);
                $comment->$setterMethod($commentData[$property]);
            }
        }

        // Setting date
        $comment->setDate(new DateTime('now'));

        // Setting vote to 0
        $comment->setVote(0);

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

        // Returning the new entity comment in JSON (201 = HTTP_CREATED)
        return $this->json($comment, 201, [], ['groups' => 'comment:crud']);
    }

    /** 
     * Updating a comment by getting the associated comment's id
     * 
     * @param $id, the id of the associated comment
     * @param $request, a Request entity to call the database
     * @param $entityManager, the manager to persist the data
     * @param $CommentRepository, the repository to make request from the table Comment
     *  */
    #[Route('/{id<\d+>}', name: 'update', methods: ['PATCH'])]
    public function update(Comment $comment, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Decode the the content
        $commentData = json_decode($request->getContent(), true);

        // Setting body, title & rate
        $properties = ['body', 'title', 'rate'];

        foreach ($properties as $property) {
            if (isset($commentData[$property])) {
                $setterMethod = 'set' . ucfirst($property);
                $comment->$setterMethod($commentData[$property]);
            }
        }

        // Saving the entity
        $entityManager->persist($comment);
        $entityManager->flush();
        
        // Returning the updated entity comment in JSON (200 = HTTP_OK)
        return $this->json($comment, 200, [], ['groups' => 'comment:crud']);
    }

    /** 
     * Updating a comment's votes by getting the associated person's id
     * 
     * @param $id, the id of the associated comment
     * @param $request, a Request entity to call the database
     * @param $entityManager, the manager to persist the data
     * @param $CommentRepository, the repository to make request from the table Comment
     *  */
    #[Route('/{id<\d+>}/vote', name: 'vote', methods: ['PATCH'])]
    public function vote(Comment $comment, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Decode the the content
        $commentData = json_decode($request->getContent(), true);

        // Setting the vote count
        $comment->setVote($commentData['vote']);

        // Saving the entity
        $entityManager->persist($comment);
        $entityManager->flush();
        
        // Returning the updated entity comment in JSON (200 = HTTP_OK)
        return $this->json($comment, 200, [], ['groups' => 'comment:crud']);
    }

    /** 
     * Deleting a comment by getting its id
     * 
     * @param $id, the id of the comment entity
     * @param $entityManager, the manager to persist the data
     * @param $CommentRepository, the repository to make request from the table Comment
     *  */
    #[Route('/{id<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(Comment $comment, EntityManagerInterface $entityManager): JsonResponse
    {
        // Deleting the comment
        $entityManager->remove($comment);
        $entityManager->flush();

        // Returning a message 'Comment delete' (200 = HTTP_OK)
        return $this->json(['message' => 'Comment deleted'], 200, [], ['groups' => 'comment:crud']);
    }
}