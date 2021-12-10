<?php

namespace App\Entity\External;

use App\Interfaces\DateTime\EntityDateInterface;
use App\Interfaces\Structure\ClinicInterface;
use App\Interfaces\User\CreatedByInterface;
use App\Traits\DateTime\EntityDateTrait;
use App\Traits\Structure\ClinicTrait;
use App\Traits\User\CreatedByTrait;
use Doctrine\ORM\Mapping as ORM;

use App\Repository\External\LaboratoryRepository;

/**
 * @ORM\Entity(repositoryClass=LaboratoryRepository::class)
 * @ORM\HasLifecycleCallbacks
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class Laboratory implements EntityDateInterface, CreatedByInterface, ClinicInterface
{
    use EntityDateTrait;
    use CreatedByTrait;
    use ClinicTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private ?string $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $address2;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $postalCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $city;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private ?string $phone;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private ?string $phone2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $email;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    public function setAddress2(?string $address2): self
    {
        $this->address2 = $address2;

        return $this;
    }

    public function getPostalCode(): ?int
    {
        return $this->postalCode;
    }

    public function setPostalCode(?int $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPhone2(): ?string
    {
        return $this->phone2;
    }

    public function setPhone2(?string $phone2): self
    {
        $this->phone2 = $phone2;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }
}
