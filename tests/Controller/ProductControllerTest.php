<?php

// tests/Controller/ProductControllerTest.php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
    private $token;

    public function setUp(): void
    {
        $client = static::createClient();

        // Register a new user
        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'product@example.com',
                'password' => 'password123',
            ])
        );

        // Login to get JWT token
        $client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'product@example.com',
                'password' => 'password123',
            ])
        );

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->token = $response['token'];
    }

    public function testListProducts()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/products',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->token]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCreateProduct()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/products',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'name' => 'Product 1',
                'description' => 'Description 1',
                'price' => '100.00',
            ])
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertStringContainsString('Product created!', $client->getResponse()->getContent());
    }

    public function testShowProduct()
    {
        $client = static::createClient();

        // Create a product first
        $client->request(
            'POST',
            '/api/products',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'name' => 'Product 1',
                'description' => 'Description 1',
                'price' => '100.00',
            ])
        );

        $response = json_decode($client->getResponse()->getContent(), true);
        $productId = $response['id'];

        $client->request(
            'GET',
            '/api/products/' . $productId,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->token]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testUpdateProduct()
    {
        $client = static::createClient();

        // Create a product first
        $client->request(
            'POST',
            '/api/products',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'name' => 'Product 1',
                'description' => 'Description 1',
                'price' => '100.00',
            ])
        );

        $response = json_decode($client->getResponse()->getContent(), true);
        $productId = $response['id'];

        $client->request(
            'PUT',
            '/api/products/' . $productId,
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'name' => 'Updated Product 1',
                'description' => 'Updated Description 1',
                'price' => '150.00',
            ])
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertStringContainsString('Product updated!', $client->getResponse()->getContent());
    }

    public function testDeleteProduct()
    {
        $client = static::createClient();

        // Create a product first
        $client->request(
            'POST',
            '/api/products',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'name' => 'Product 1',
                'description' => 'Description 1',
                'price' => '100.00',
            ])
        );

        $response = json_decode($client->getResponse()->getContent(), true);
        $productId = $response['id'];

        $client->request(
            'DELETE',
            '/api/products/' . $productId,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->token]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertStringContainsString('Product deleted!', $client->getResponse()->getContent());
    }
}
