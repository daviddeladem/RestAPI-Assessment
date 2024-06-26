<?php

// src/Controller/RegistrationController.php
namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController
{
    private $entityManager;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @Route("/api/register", name="register", methods={"POST"})
     */
    public function register(Request $request, ValidatorInterface $validator)
    {
        // return $this->json(['message' => false], 400);
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new JsonResponse(['errors' => $errorsString], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'User created!'], JsonResponse::HTTP_CREATED);
    }
}
