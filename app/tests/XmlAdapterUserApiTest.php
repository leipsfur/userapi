<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Data\XmlFileStorageAdapter;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Functional test for user api endpoints 
 */
class XmlAdapterUserApiTest extends ApiTestCase {

    protected function getClient() {
        $client = self::createClient();
        $normalContainer = $client->getContainer();
        $specialContainer = $normalContainer->get('test.service_container');

        $specialContainer->set('app.dataStorageAdapter', new XmlFileStorageAdapter($client->getKernel(), 'xml/test'));
        return $client;
    }

    protected function setUp(): void
    {
        $filesystem = new Filesystem();
        $filesystem->copy("xml/test/test_users.xml", "xml/test/users.xml", true);
    }

    /** @test */
    public function getAllUsers()
    {
        $response = $this->getClient()->request('GET', '/users');
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/contexts/User',
            '@id' => '/users',
            '@type' => 'hydra:Collection'
        ]);
        $this->assertCount(4, $response->toArray()['hydra:member']);
    }

    /** @test */
    public function getSpecificUser()
    {
        $this->getClient()->request('GET', '/users/1');
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/contexts/User',
            '@id' => '/users/1',
            '@type' => 'User',
            'id' => '1',
            'login' => "henry.dunant",
            'firstName' => "Henry",
            'lastName' => "Dunant",
        ]);
    }

    /** @test */
    public function postUser()
    {
        $this->getClient()->request('POST', '/users', [
            'json' => [
                'login' => 'test.login',
                'firstName' => 'Test',
                'lastName' => 'Login',
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $xmlBefore = file_get_contents('xml/test/test_users.xml');
        $xmlAfter = file_get_contents('xml/test/users.xml');
        $this->assertStringNotContainsString("test.login", $xmlBefore);
        $this->assertStringContainsString("test.login", $xmlAfter);
    }

    /** @test */
    public function patchUser()
    {
        $this->getClient()->request('PUT', '/users/1', [
            'json' => [
                'login' => 'test.login',
                'firstName' => 'Test',
                'lastName' => 'Login',
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $xmlBefore = file_get_contents('xml/test/test_users.xml');
        $xmlAfter = file_get_contents('xml/test/users.xml');
        $this->assertStringNotContainsString("test.login", $xmlBefore);
        $this->assertStringContainsString("henry.dunant", $xmlBefore);
        $this->assertStringNotContainsString("henry.dunant", $xmlAfter);
        $this->assertStringContainsString("test.login", $xmlAfter);
    }

    /** @test */
    public function deleteUser()
    {
        $this->getClient()->request('DELETE', '/users/1');
        $this->assertResponseIsSuccessful();
        $xmlBefore = file_get_contents('xml/test/test_users.xml');
        $xmlAfter = file_get_contents('xml/test/users.xml');
        $this->assertStringContainsString("henry.dunant", $xmlBefore);
        $this->assertStringNotContainsString("henry.dunant", $xmlAfter);
    }
}