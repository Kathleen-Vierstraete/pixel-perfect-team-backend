<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Creator;
use App\Entity\Editor;
use App\Entity\Product;
use App\Entity\Tag;
use App\Repository\ProductRepository;
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

        return $this->json($products, 200, [], ['groups' => 'product:read']);
    }

    /**
     * Getting all non-archived products
     *
     * @param $productRepository, the repository to make request from the table Products
     */
    #[Route('/', name: 'index', methods: ['get'])]
    public function index(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findBy(['isArchived' => 0]);

        return $this->json($products, 200, [], ['groups' => 'product:read']);
    }

    #[Route('/similar/{id<\d+>}', name:'similar', methods: ['get'])]
    public function similarProducts(Product $product,ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findByTags($product->getTags());

        return $this->json($products, 200, [], ['groups' => 'product:read']);
    }

    /** 
     * Getting a product by its ID
     * 
     * @param $product, a Product entity
     *  */
    #[Route('/{id<\d+>}', name: 'by_id', methods: ['get'])]
    public function getById(Product $product = null): Response
    {
        if (!$product) {
            return $this->json(
                [
                    'error' => 'Produit non trouvé'
                ],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json($product, 200, [], ['groups' => 'product:read']);
    }

    /** 
     * Creating a new product
     * 
     * @param $request, a Request entity to call the database
     * @param $entityManager, the manager to persist the data
     *  */

    #[Route('/add', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {

        // Creating a Product Entity
        $product = new Product();

        // Getting the content
        $productData = json_decode($request->getContent(), true);

        // Setting properties
        $properties = ['name', 'reference', 'price', 'description', 'stock', 'length', 'height', 'width', 'weight', 'creationDate', 'isArchived', 'isCollector'];

        foreach ($properties as $property) {
            if (isset($productData[$property])) {
                $setterMethod = 'set' . ucfirst($property);
                $product->$setterMethod($productData[$property]);
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


        // Saving the product entity
        $entityManager->persist($product);
        $entityManager->flush();

        // Return the response
        return $this->json($product, Response::HTTP_CREATED, [], ["groups" => "product:create"]);
    }


    /** 
     * Getting a product by its ID
     * 
     * @param $id, the product id to update
     * @param $request, a Request entity to call the database
     * @param $entityManager, the manager to persist the data
     * @param $productRepository, the repository to make request from the table Products
     *  */

    #[Route('/update/{id<\d+>}', name: 'update', methods: ['PATCH'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager, ProductRepository $productRepository): JsonResponse
    {
        // Getting the product to update
        $product = $productRepository->find($id);
        $productData = json_decode($request->getContent(), true);

        $product->setName($productData['name']);
        $product->setReference($productData['reference']);
        $product->setPrice($productData['price']);
        $product->setDescription($productData['description']);
        $product->setStock($productData['stock']);
        $product->setLength($productData['length']);
        $product->setHeight($productData['height']);
        $product->setWidth($productData['width']);
        $product->setWeight($productData['weight']);
        $product->setCreationDate($productData['creationDate']);
        $product->setIsArchived($productData['isArchived']);
        $product->setIsCollector($productData['isCollector']);

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
        return $this->json($product, 204, [], ['groups' => 'product:update']);
    }
}
