<?php

namespace App\Entity\Calendar;

use App\Interfaces\DateTime\EntityDateInterface;
use App\Interfaces\User\CreatedByWithUserInterface;
use App\Traits\DateTime\EntityDateTrait;
use App\Traits\User\CreatedByWithUserTrait;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Calendar\BookingRepository;

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
     */
    private ?DateTimeInterface $beginAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $endAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $title;

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
}
