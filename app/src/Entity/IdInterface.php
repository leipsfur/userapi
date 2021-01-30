<?php


namespace App\Entity;

interface IdInterface {
    public function getId(): ?string;
    public function setId(string $id): self;
}