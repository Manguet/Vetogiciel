<?php

namespace App\Interfaces\User;

use App\Entity\Structure\Employee;
use App\Entity\Structure\Veterinary;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
interface CreatedByInterface
{
    public function getCreatedByVeterinary(): ?Veterinary;

    public function setCreatedByVeterinary(?Veterinary $createdByVeterinary): self;

    public function getCreatedByEmployee(): ?Employee;

    public function setCreatedByEmployee(?Employee $createdByEmployee): self;

    public function getCreatedBy(): ?UserInterface;

    public function setCreatedBy(UserInterface $user): self;
}