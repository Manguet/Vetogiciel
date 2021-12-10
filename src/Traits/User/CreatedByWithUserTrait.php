<?php

namespace App\Traits\User;

use App\Entity\Patients\Client;
use App\Entity\Structure\Employee;
use App\Entity\Structure\Veterinary;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
trait CreatedByWithUserTrait
{
    use CreatedByTrait;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class)
     */
    private ?Client $createdByClient;

    public function getCreatedByClient(): ?Client
    {
        return $this->createdByClient;
    }

    public function setCreatedByClient(?Client $createdByClient): self
    {
        $this->createdByClient = $createdByClient;

        return $this;
    }

    public function getCreatedBy(): ?UserInterface
    {
        return $this->createdByVeterinary ?? $this->createdByEmployee ?? $this->createdByClient;
    }

    public function setCreatedBy(UserInterface $user): self
    {
        if ($user instanceof Veterinary) {
            $this->createdByVeterinary = $user;
        }

        if ($user instanceof Employee) {
            $this->createdByEmployee = $user;
        }

        if ($user instanceof Client) {
            $this->createdByClient = $user;
        }

        return $this;
    }
}