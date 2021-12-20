<?php

namespace App\Entity\Calendar;

use App\Entity\Patients\Animal;
use App\Entity\Patients\Client;
use App\Entity\Structure\Veterinary;
use App\Interfaces\DateTime\EntityDateInterface;
use App\Interfaces\User\CreatedByWithUserInterface;
use App\Traits\DateTime\EntityDateTrait;
use App\Traits\User\CreatedByWithUserTrait;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Calendar\BookingRepository;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 * @ORM\HasLifecycleCallbacks
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class Booking implements EntityDateInterface, CreatedByWithUserInterface
{
    use EntityDateTrait;
    use CreatedByWithUserTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThan("today")
     */
    private $beginAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\GreaterThan("today")
     */
    private $endAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $title;

    /**
     * @ORM\ManyToOne(targetEntity=Veterinary::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $veterinary;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="bookings")
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity=Animal::class)
     */
    private $animal;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $color;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBeginAt(): ?DateTimeInterface
    {
        return $this->beginAt;
    }

    public function setBeginAt(DateTimeInterface $beginAt): self
    {
        $this->beginAt = $beginAt;

        return $this;
    }

    public function getEndAt(): ?DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(?DateTimeInterface $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getVeterinary(): ?Veterinary
    {
        return $this->veterinary;
    }

    public function setVeterinary(?Veterinary $veterinary): self
    {
        $this->veterinary = $veterinary;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getAnimal(): ?Animal
    {
        return $this->animal;
    }

    public function setAnimal(?Animal $animal): self
    {
        $this->animal = $animal;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }
}
