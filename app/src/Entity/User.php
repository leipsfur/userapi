<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    description: "An example user entity",
    collectionOperations: [
        "get" => ["normalization_context" => ["view_user"]],
        "post" => ["normalization_context" => ["create_user"]]
    ],
    itemOperations: [
        "get" => ["normalization_context" => ["view_user"]],
        "put" => ["normalization_context" => ["update_user"]],
        "delete"
    ],
    denormalizationContext: ["groups" => ["update_user", "create_user"]],
    normalizationContext: ["groups" => ["view_user"]]
)]
class User implements IdInterface {

    #[ApiProperty(
        description: "Unique user identifier",
        identifier: true
    )]
    #[Groups(["view_user"])]
    protected ?string $id = null;

    #[ApiProperty(description: "User login")]
    #[Groups(["view_user", "create_user"])]
    protected string $login = "";

    #[ApiProperty(description: "Users first name")]
    #[Groups(["view_user", "create_user", "update_user"])]
    protected ?string $firstName = null;

    #[ApiProperty(description: "Users last name")]
    #[Groups(["view_user", "create_user", "update_user"])]
    protected ?string $lastName = null;

    public function getId(): ?string {
        return $this->id;
    }

    public function setId($id): User {
        $this->id = $id;
        return $this;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): User
    {
        $this->login = $login;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): User
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): User
    {
        $this->lastName = $lastName;
        return $this;
    }
}