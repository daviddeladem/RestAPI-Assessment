<?php

// src/Controller/LoginController.php
namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class LoginController
{
    /**
     * @Route("/api/login", name="login", methods={"POST"})
     */
    public function login(Request $request, JWTTokenManagerInterface $jwtManager)
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        // Validate credentials and authenticate user (pseudo-code)
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user || !password_verify($password, $user->getPassword())) {
            return new JsonResponse(['message' => 'Invalid credentials'], 401);
        }

        // Generate JWT token
        $token = $jwtManager->create($user);

        return new JsonResponse(['token' => $token]);
    }
}
