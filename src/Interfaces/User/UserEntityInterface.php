<?php

namespace App\Interfaces\User;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
interface UserEntityInterface
{
    public function getId(): ?int;

    public function getEmail(): ?string;

    public function setEmail(string $email): self;

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string;

    /**
     * @see UserInterface
     */
    public function getRoles(): array;

    public function setRoles(array $roles): self;

    /**
     * @see UserInterface
     */
    public function getPassword(): string;

    public function setPassword(string $password): self;

    /**
     * @see UserInterface
     */
    public function getSalt();

    /**
     * @see UserInterface
     */
    public function eraseCredentials();

    public function getFirstname(): ?string;

    public function setFirstname(?string $firstname): self;

    public function getLastname(): ?string;

    public function setLastname(string $lastname): self;

    public function getFullNameSlugiffied(): ?string;

    public function setFullNameSlugiffied(?string $fullNameSlugiffied): self;
}