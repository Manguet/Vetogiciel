<?php

namespace App\Interfaces\Structure;

/**
 * @author Benjamin Manguet <benjamin.mangeut@gmail.com>
 */
interface AddressInterface
{
    public function getAddress(): ?string;

    public function setAddress(?string $address): self;

    public function getAddress2(): ?string;

    public function setAddress2(?string $address2): self;

    public function getPostalCode(): ?int;

    public function setPostalCode(?int $postalCode): self;

    public function getCity(): ?string;

    public function setCity(?string $city): self;

    public function getPhone(): ?string;

    public function setPhone(?string $phone): self;

    public function getPhone2(): ?string;

    public function setPhone2(?string $phone2): self;
}