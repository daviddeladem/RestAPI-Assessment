<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * @Route("/api/products", name="api_products_")
 */
class ProductController extends AbstractController
{
    private $entityManager;
    private $productRepository;

    public function __construct(EntityManagerInterface $entityManager, ProductRepository $productRepository)
    {
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("", name="index", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        $products = $this->productRepository->findAll();
        return $this->json($products);
    }

    /**
     * @Route("", name="create", methods={"POST"})
     */
    public function create(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $product = new Product();
        $product->setName($data['name']);
        $product->setDescription($data['description']);
        $product->setPrice($data['price']);

        $errors = $validator->validate($product);
        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $this->json($product, 201);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        return $this->json($product);
    }

    /**
     * @Route("/{id}", name="update", methods={"PUT"})
     */
    public function update(Request $request, int $id, ValidatorInterface $validator): JsonResponse
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        $data = json_decode($request->getContent(), true);
        $product->setName($data['name']);
        $product->setDescription($data['description']);
        $product->setPrice($data['price']);

        $errors = $validator->validate($product);
        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $this->entityManager->flush();

        return $this->json($product);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return $this->json(null, 204);
    }
}