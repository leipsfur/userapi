<?php

namespace App\Data;

use App\Entity\IdInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Uid\Uuid;

class DatabaseStorageAdapter implements StorageAdapterInterface {

    public function __construct(protected KernelInterface $appKernel, protected EntityManagerInterface $entityManager) {}

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        return $this->entityManager->getRepository($resourceClass)->findAll();
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        return $this->entityManager->getRepository($resourceClass)->find($id);
    }

    /**
     * @param IdInterface $data
     * @param array $context
     * @return object|void
     */
    public function persist($data, array $context = [])
    {
        if($data->getId() === null) {
            $uid = Uuid::v6();
            $data->setId($uid->toRfc4122());
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();
        return $data;
    }

    public function remove($data, array $context = [])
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}