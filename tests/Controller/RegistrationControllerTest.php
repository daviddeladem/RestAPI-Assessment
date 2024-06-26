<?php
// tests/Controller/RegistrationControllerTest.php
// tests/Controller/RegistrationControllerTest.php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegister()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'test@example.com',
                'password' => 'password123',
            ])
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertStringContainsString('User created!', $client->getResponse()->getContent());
    }
}
