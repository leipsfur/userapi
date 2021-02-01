<?php

namespace App\Data;

use App\Entity\IdInterface;
use App\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Uid\Uuid;

class XmlFileStorageAdapter implements StorageAdapterInterface {

    protected Serializer $serializer;

    public function __construct(protected KernelInterface $appKernel, protected string $path) {
        $normalizers = [
            new ArrayDenormalizer(),
            new ObjectNormalizer(null, null, null, new ReflectionExtractor())
        ];
        $encoders = [new XmlEncoder()];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        return $this->loadData($resourceClass);
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $data = $this->loadData($resourceClass);
        foreach($data as $item) {
            if($item->getId() === $id) {
                return $item;
            }
        }
        return null;
    }

    /**
     * @param IdInterface $data
     * @param array $context
     * @return object|void
     */
    public function persist($data, array $context = [])
    {
        $users = $this->loadData($context['resource_class']);
        if($data->getId() === null) {
            $uid = Uuid::v6();
            $data->setId($uid->toRfc4122());
            $users[] = $data;
        } else {
            foreach($users as $i => $user) {
                if($user->getId() === $data->getId()) {
                    $users[$i] = $data;
                    break;
                }
            }
        }
        $this->saveData($context['resource_class'], $users);
    }

    public function remove($data, array $context = [])
    {
        $users = $this->loadData($context['resource_class']);
        foreach($users as $i => $user) {
            if($user->getId() === $data->getId()) {
                unset($users[$i]);
                break;
            }
        }
        $this->saveData($context['resource_class'], $users);
    }

    /**
     * @param $resourceClass
     * @return array
     */
    protected function loadData($resourceClass) : array {
        $xmlFile = "/users.xml"; // TODO determine xml file name from resource class
        $fileName = $this->appKernel->getProjectDir() . '/' . $this->path . $xmlFile;
        if(!file_exists($fileName)) {
            return [];
        }
        $xml = file_get_contents($fileName);
        return $this->serializer->deserialize($xml, User::class . '[]', 'xml');
    }

    /**
     * @param $resourceClass
     */
    protected function saveData($resourceClass, array $data) : void {
        $xmlFile = "/users.xml"; // TODO determine xml file name from resource class
        $fileName = $this->appKernel->getProjectDir() . '/' . $this->path . $xmlFile;
        file_put_contents($fileName, $this->serializer->serialize($data, "xml"));
    }
}