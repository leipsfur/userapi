<?php


namespace App\Data;


use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;

class DataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(protected StorageAdapterInterface $storageAdapter) { }


    public function supports($data, array $context = []): bool
    {
        return true;
    }

    public function persist($data, array $context = [])
    {
        return $this->storageAdapter->persist($data, $context);
    }

    public function remove($data, array $context = [])
    {
        return $this->storageAdapter->remove($data, $context);
    }
}