<?php

namespace App\Data;

interface StorageAdapterInterface {
    public function getCollection(string $resourceClass, string $operationName = null, array $context = []);

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []);

    public function persist($data, array $context = []);

    public function remove($data, array $context = []);
}