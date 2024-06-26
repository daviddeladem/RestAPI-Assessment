<?php

// src/Controller/ProductController.php
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
 * @Route("/api/products")
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
     * @Route("", name="product_list", methods={"GET"})
     */
    public function list()
    {
        $products = $this->productRepository->findAll();
        return new JsonResponse($products);
    }

    /**
     * @Route("", name="product_create", methods={"POST"})
     */
    public function create(Request $request, ValidatorInterface $validator)
    {
        $data = json_decode($request->getContent(), true);

        $product = new Product();
        $product->setName($data['name']);
        $product->setDescription($data['description']);
        $product->setPrice($data['price']);

        $errors = $validator->validate($product);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new JsonResponse(['errors' => $errorsString], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Product created!'], JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/{id}", name="product_show", methods={"GET"})
     */
    public function show($id)
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return new JsonResponse(['error' => 'Product not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse($product);
    }

    /**
     * @Route("/{id}", name="product_update", methods={"PUT"})
     */
    public function update(Request $request, $id, ValidatorInterface $validator)
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return new JsonResponse(['error' => 'Product not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $product->setName($data['name']);
        $product->setDescription($data['description']);
        $product->setPrice($data['price']);

        $errors = $validator->validate($product);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new JsonResponse(['errors' => $errorsString], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Product updated!'], JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="product_delete", methods={"DELETE"})
     */
    public function delete($id)
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return new JsonResponse(['error' => 'Product not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Product deleted!'], JsonResponse::HTTP_OK);
    }
}
