<?php


namespace App\Data;


use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelInterface;

class DataProvider implements
    ContextAwareCollectionDataProviderInterface,
    ItemDataProviderInterface,
    RestrictedDataProviderInterface
{
    public function __construct(protected StorageAdapterInterface $storageAdapter) { }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        return $this->storageAdapter->getCollection($resourceClass, $operationName, $context);
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        return $this->storageAdapter->getItem($resourceClass, $id, $operationName, $context);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return true;
    }
}