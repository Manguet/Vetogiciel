<?php

namespace App\Traits\User;

use App\Entity\Structure\Employee;
use App\Entity\Structure\Veterinary;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
trait CreatedByTrait
{
    /**
     * @ORM\ManyToOne(targetEntity=Veterinary::class)
     */
    private ?Veterinary $createdByVeterinary;

    /**
     * @ORM\ManyToOne(targetEntity=Employee::class)
     */
    private ?Employee $createdByEmployee;

    public function getCreatedByVeterinary(): ?Veterinary
    {
        return $this->createdByVeterinary;
    }

    public function setCreatedByVeterinary(?Veterinary $createdByVeterinary): self
    {
        $this->createdByVeterinary = $createdByVeterinary;

        return $this;
    }

    public function getCreatedByEmployee(): ?Employee
    {
        return $this->createdByEmployee;
    }

    public function setCreatedByEmployee(?Employee $createdByEmployee): self
    {
        $this->createdByEmployee = $createdByEmployee;

        return $this;
    }

    public function getCreatedBy(): ?UserInterface
    {
        return $this->createdByVeterinary ?? $this->createdByEmployee;
    }

    public function setCreatedBy(UserInterface $user): self
    {
        if ($user instanceof Veterinary) {
            $this->createdByVeterinary = $user;
        }

        if ($user instanceof Employee) {
            $this->createdByEmployee = $user;
        }

        return $this;
    }
}