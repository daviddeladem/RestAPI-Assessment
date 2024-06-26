<?php
// tests/Controller/LoginControllerTest.php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase
{
    public function testLogin()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'login@example.com',
                'password' => 'password123',
            ])
        );

        $client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'login@example.com',
                'password' => 'password123',
            ])
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertStringContainsString('token', $client->getResponse()->getContent());
    }
}
