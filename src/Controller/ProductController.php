<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Creator;
use App\Entity\Editor;
use App\Entity\Person;
use App\Entity\Picture;
use App\Entity\Product;
use App\Entity\Tag;
use App\Repository\PictureRepository;
use App\Repository\ProductRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/products', name: 'api_products_')]
class ProductController extends AbstractController
{
    /**
     * Getting all products
     *
     * @param $productRepository, the repository to make request from the table Products
     */
    #[Route('/backoffice', name: 'index_backoffice', methods: ['get'])]
    public function indexBackoffice(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findAll();

        return $this->json($products, 200, [], ['groups' => 'product:crud']);
    }

    /**
     * Getting all non-archived products
     *
     * @param $productRepository, the repository to make request from the table Products
     */
    #[Route('', name: 'index', methods: ['get'])]
    public function index(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findBy(['isArchived' => 0]);

        return $this->json($products, 200, [], ['groups' => 'product:crud']);
    }
    /** 
     * Getting a product by its ID & the similar products by the tag's ID
     * 
     * @param $product, a Product entity
     * @param $productRepository, the repository to make request from the table Products
     *  */
    #[Route('/{id<\d+>}', name: 'by_id', methods: ['get'])]
    public function getById(Product $product = null, ProductRepository $productRepository): JsonResponse
    {
        if (!$product) {
            return $this->json(
                [
                    'error' => 'Produit non trouvé'
                ],
                Response::HTTP_NOT_FOUND,
            );
        }

        $products = $productRepository->findByTags($product->getTags(), $product->getId());

        return $this->json(['product' => $product, 'similarProduct' => $products], 200, [], ['groups' => 'product:crud']);
    }

    /** 
     * Getting a product by its ID & the similar products by the tag's ID
     * 
     * @param $product, a Product entity
     * @param $productRepository, the repository to make request from the table Products
     *  */
    #[Route('/tags', name: 'by_tags', methods: ['POST'])]
    public function getByTags(Request $request,ProductRepository $productRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $products = $productRepository->findByTagsExcludeProducts($data["product_ids"]);
        return $this->json($products);
    }

    /** 
     * Creating a new product
     * 
     * @param $request, a Request entity to call the database
     * @param $entityManager, the manager to persist the data
     *  */

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {

        // Creating a Product Entity
        $product = new Product();

        // Getting the content
        $productData = json_decode($request->getContent(), true);

        // Setting properties
        $properties = ['name', 'reference', 'price', 'description', 'stock', 'length', 'height', 'width', 'weight', 'creationDate', 'isArchived', 'isCollector'];

        if (isset($productData['reference'])) {
            $existingProduct = $entityManager->getRepository(Product::class)->findOneBy(['reference' => $productData['reference']]);
            if ($existingProduct !== null) {
                return $this->json(['error' => 'Duplicate reference. Reference must be unique.'], 409);
            }
        }
        foreach ($properties as $property) {
            if (isset($productData[$property])) {
                $setterMethod = 'set' . ucfirst($property);
                if (in_array($property, ['price', 'stock', 'length', 'height', 'width', 'weight', 'creationDate']) && $productData[$property] < 0) {
                    return $this->json(['error' => 'Negative values are not allowed for ' . $property], 400);
                }
                $product->$setterMethod($productData[$property]);
            }
        }
        $requiredFields = ['name', 'reference','description'];
        foreach ($requiredFields as $field) {
            if (empty($productData[$field])) {
                return $this->json(['error' => $field . ' cannot be empty'], 400);
            }
        }

        // Add creators
        $creatorIds = $productData['creator_ids'];
        foreach ($creatorIds as $creatorId) {
            $creator = $entityManager->getRepository(Creator::class)->find($creatorId);
            $product->addCreator($creator);
        }

        // Add tags
        $tagsIds = $productData['tag_ids'];
        foreach ($tagsIds as $tagId) {
            $tag = $entityManager->getRepository(Tag::class)->find($tagId);
            $product->addTag($tag);
        }

        // Set category
        $categoryId = $productData['category_id'];
        $category = $entityManager->getRepository(Category::class)->find($categoryId);
        $product->setCategory($category);

        // Set editor
        $editorId = $productData['editor_id'];
        $editor = $entityManager->getRepository(Editor::class)->find($editorId);
        $product->setEditor($editor);

        $pictureData = $productData['pictures'][0] ?? null;
        $picture = new Picture();
        if ($pictureData !== null) {
            $pictureProperties = ['name', 'url', 'alt'];
            
            foreach ($pictureProperties as $property) {
                if (isset($pictureData[$property])) {
                    $setterMethod = 'set' . ucfirst($property);
                    $picture->$setterMethod($pictureData[$property]);
                }
            }
        }
        // Set the picture for the product
        $product->addPicture($picture);
        $picture->setProduct($product);
        // Saving the product entity
        $entityManager->persist($product);
        $entityManager->persist($picture);
        
        $entityManager->flush();

        // Return the response
        return $this->json($product, 201, [], ["groups" => "product:crud"]);
    }


    /** 
     * Getting a product by its ID
     * 
     * @param $id, the product id to update
     * @param $request, a Request entity to call the database
     * @param $entityManager, the manager to persist the data
     * @param $productRepository, the repository to make request from the table Products
     *  */

    #[Route('/{id<\d+>}', name: 'update', methods: ['PATCH'])]
    public function update(Product $product, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $productData = json_decode($request->getContent(), true);

        $properties = ['name', 'reference', 'price', 'description', 'stock', 'length', 'height', 'width', 'weight', 'creationDate', 'isArchived', 'isCollector'];
        foreach ($properties as $property) {
            if (isset($productData[$property])) {
                $setterMethod = 'set' . ucfirst($property);
                if (in_array($property, ['price', 'stock', 'length', 'height', 'width', 'weight','creationDate']) && $productData[$property] < 0) {
                    return $this->json(['error' => 'Negative values are not allowed for ' . $property], 400);
                }
                $product->$setterMethod($productData[$property]);
            }
        }

        // Add creators
        $creatorIds = $product->getCreators();
        foreach ($creatorIds as $creatorId) {
            $creator = $entityManager->getRepository(Creator::class)->find($creatorId);
            $product->addCreator($creator);
        }

        // Add tags
        $tagsIds = $product->getTags();
        foreach ($tagsIds as $tagId) {
            $tag = $entityManager->getRepository(Tag::class)->find($tagId);
            $product->addTag($tag);
        }

        // Set category
        $categoryId = $product->getCategory();
        $category = $entityManager->getRepository(Category::class)->find($categoryId);
        $product->setCategory($category);

        // Set editor
        $editorId = $product->getEditor();
        $editor = $entityManager->getRepository(Editor::class)->find($editorId);
        $product->setEditor($editor);

        // Saving the entity
        $entityManager->persist($product);
        $entityManager->flush();

        // returning the answer
        return $this->json(['message'=>'product is updated'],200,['groups' => 'product:crud']);
    }

    /** 
     * Getting a comment by the id of the associated product
     * 
     * @param $ProductRepository, the repository to make request from the table Products
     * @param $id, the id of the associated product
     *  */
    #[Route('/{id<\d+>}/comments', name: 'get_by_product', methods: ['GET'])]
    public function getCommentsByProduct(Product $product): Response
    {
        // Getting all its comments
        $comments = $product->getComments();

        // Returning the entity comment in JSON (200 = HTTP_OK)
        return $this->json($comments, 200, [], ['groups' => 'comment:crud']);
    }

    /** 
     * Creating a new comment
     * 
     * @param $request, a Request entity to call the database
     * @param $entityManager, the manager to persist the data
     *  */
    #[Route('/{id<\d+>}/comments', name: 'create_comment', methods: ['POST'])]
    public function createComment(Product $product, Request $request, EntityManagerInterface $entityManager): JsonResponse
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

        // Setting person
        $personId = $commentData['person_id'];
        $person = $entityManager->getRepository(Person::class)->find($personId);
        $comment->setPerson($person);

        // Setting product
        $comment->setProduct($product);

        // Saving the entity
        $entityManager->persist($comment);
        $entityManager->flush();

        // Returning the new entity comment in JSON (201 = HTTP_CREATED)
        return $this->json($comment, 201, [], ['groups' => 'comment:crud']);
    }
    
}
