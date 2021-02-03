<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Data\DatabaseStorageAdapter;
use App\Data\XmlFileStorageAdapter;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Functional test for user api endpoints 
 */
class DatabaseAdapterUserApiTest extends ApiTestCase {

    protected UserRepository $userRepository;
    protected EntityManager $entityManager;

    public function setUp() : void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    protected function getClient() {
        $client = self::createClient();
        $normalContainer = $client->getContainer();
        $specialContainer = $normalContainer->get('test.service_container');

        $specialContainer->set('app.dataStorageAdapter',
            new DatabaseStorageAdapter($client->getKernel(),
            $normalContainer->get('doctrine.orm.entity_manager')));
        return $client;
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
        $this->assertSame(5, $this->userRepository->count([]));
    }

    /** @test */
    public function patchUser()
    {
        $this->assertNotNull($this->userRepository->findOneBy(['login' => 'henry.dunant']));
        $this->assertNull($this->userRepository->findOneBy(['login' => 'test.login']));
        $this->getClient()->request('PUT', '/users/1', [
            'json' => [
                'login' => 'test.login',
                'firstName' => 'Test',
                'lastName' => 'Login',
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertNull($this->userRepository->findOneBy(['login' => 'henry.dunant']));
        $this->assertNotNull($this->userRepository->findOneBy(['login' => 'test.login']));
    }

    /** @test */
    public function deleteUser()
    {
        $this->assertNotNull($this->userRepository->findOneBy(['login' => 'henry.dunant']));
        $this->getClient()->request('DELETE', '/users/1');
        $this->assertResponseIsSuccessful();
        $this->assertNull($this->userRepository->findOneBy(['login' => 'henry.dunant']));
        $this->assertSame(3, $this->userRepository->count([]));
    }
}