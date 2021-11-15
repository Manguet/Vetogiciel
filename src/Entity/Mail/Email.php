<?php

namespace App\Entity\Mail;

use App\Interfaces\DateTime\EntityDateInterface;
use App\Repository\Mail\EmailRepository;
use App\Traits\DateTime\EntityDateTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EmailRepository::class)
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * @ORM\HasLifecycleCallbacks
 */
class Email implements EntityDateInterface
{
    use EntityDateTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=120, unique=true)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="text")
     */
    private $template;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isExpeditorCurrentUser;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $expeditor;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDestinatorCurrentUser;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $destinators = [];

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(string $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function getIsExpeditorCurrentUser(): ?bool
    {
        return $this->isExpeditorCurrentUser;
    }

    public function setIsExpeditorCurrentUser(?bool $isExpeditorCurrentUser): self
    {
        $this->isExpeditorCurrentUser = $isExpeditorCurrentUser;

        return $this;
    }

    public function getExpeditor(): ?string
    {
        return $this->expeditor;
    }

    public function setExpeditor(?string $expeditor): self
    {
        $this->expeditor = $expeditor;

        return $this;
    }

    public function getIsDestinatorCurrentUser(): ?bool
    {
        return $this->isDestinatorCurrentUser;
    }

    public function setIsDestinatorCurrentUser(?bool $isDestinatorCurrentUser): self
    {
        $this->isDestinatorCurrentUser = $isDestinatorCurrentUser;

        return $this;
    }

    public function getDestinators(): ?array
    {
        return $this->destinators;
    }

    public function setDestinators(?array $destinators): self
    {
        $this->destinators = $destinators;

        return $this;
    }
}
