<?php

namespace App\Entity\Patients;

use App\Interfaces\DateTime\EntityDateInterface;
use App\Interfaces\User\CreatedByInterface;
use App\Traits\DateTime\EntityDateTrait;
use App\Traits\User\CreatedByTrait;
use Doctrine\ORM\Mapping as ORM;

use App\Repository\Patients\DocumentRepository;

/**
 * @ORM\Entity(repositoryClass=DocumentRepository::class)
 * @ORM\HasLifecycleCallbacks
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class Document implements EntityDateInterface, CreatedByInterface
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patients\Folder", inversedBy="documents")
     */
    private $folder;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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

    public function getFolder(): ?Folder
    {
        return $this->folder;
    }

    public function setFolder(?Folder $folder): self
    {
        $this->folder = $folder;

        return $this;
    }
}
