<?php
// tests/Controller/ProductControllerTest.php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProductControllerTest extends WebTestCase
{
    private $token;

    protected function setUp(): void
    {
        $client = static::createClient();

        // Get JWT token
        $client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'testuser1@example.com',
            'password' => 'testpassword',
        ]));

        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true);

        // Log the response for debugging
        echo 'Login response: ' . $response->getContent() . PHP_EOL;

        // Check if the response has the token key
        if (!isset($responseData['token'])) {
            echo 'Login failed. Response: ' . $response->getContent() . PHP_EOL;
            $this->fail('Login failed. Token not received.');
        }

        $this->token = $responseData['token'];
        $this->assertNotNull($this->token, 'JWT token should not be null');
    }

    public function testListProducts()
    {
        $client = static::createClient();

        $client->request('GET', '/api/products', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
        ]);

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testCreateProduct()
    {
        $client = static::createClient();

        $client->request('POST', '/api/products', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
        ]));

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testShowProduct()
    {
        $client = static::createClient();

        // Create a product first
        $client->request('POST', '/api/products', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
        ]));

        $response = $client->getResponse();
        $productData = json_decode($response->getContent(), true);

        // Check if productData has an 'id'
        $this->assertArrayHasKey('id', $productData, 'Response should contain an id');

        // Get the product
        $client->request('GET', '/api/products/' . $productData['id'], [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
        ]);

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testUpdateProduct()
    {
        $client = static::createClient();

        // Create a product first
        $client->request('POST', '/api/products', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
        ]));

        $response = $client->getResponse();
        $productData = json_decode($response->getContent(), true);

        // Check if productData has an 'id'
        $this->assertArrayHasKey('id', $productData, 'Response should contain an id');

        // Update the product
        $client->request('PUT', '/api/products/' . $productData['id'], [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'name' => 'Updated Product',
            'description' => 'Updated Description',
            'price' => 199.99,
        ]));

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testDeleteProduct()
    {
        $client = static::createClient();

        // Create a product first
        $client->request('POST', '/api/products', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
        ]));

        $response = $client->getResponse();
        $productData = json_decode($response->getContent(), true);

        // Check if productData has an 'id'
        $this->assertArrayHasKey('id', $productData, 'Response should contain an id');

        // Delete the product
        $client->request('DELETE', '/api/products/' . $productData['id'], [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
        ]);

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }
}
