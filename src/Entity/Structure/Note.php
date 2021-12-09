<?php

namespace App\Entity\Structure;

use App\Interfaces\DateTime\EntityDateInterface;
use App\Interfaces\User\CreatedByInterface;
use App\Traits\DateTime\EntityDateTrait;
use App\Traits\User\CreatedByTrait;
use Doctrine\ORM\Mapping as ORM;

use App\Repository\Structure\NoteRepository;

/**
 * @ORM\Entity(repositoryClass=NoteRepository::class)
 * @ORM\HasLifecycleCallbacks
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class Note implements EntityDateInterface, CreatedByInterface
{
    use EntityDateTrait;
    use CreatedByTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=120)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

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
}
