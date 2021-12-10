<?php

namespace App\Entity\Admin;

use App\Repository\Admin\HeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HeaderRepository::class)
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class Header
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private ?string $title;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private ?string $path;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private ?string $icon;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $isActivated;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $isMainHeader;

    /**
     * @ORM\ManyToOne(targetEntity=Header::class, inversedBy="childHeaders")
     */
    private ?Header $parentHeader;

    /**
     * @ORM\OneToMany(targetEntity=Header::class, mappedBy="parentHeader")
     */
    private $childHeaders;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private ?array $authorizations;

    public function __construct()
    {
        $this->childHeaders = new ArrayCollection();
    }

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

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIsActivated(): ?bool
    {
        return $this->isActivated;
    }

    public function setIsActivated(?bool $isActivated): self
    {
        $this->isActivated = $isActivated;

        return $this;
    }

    public function getIsMainHeader(): ?bool
    {
        return $this->isMainHeader;
    }

    public function setIsMainHeader(?bool $isMainHeader): self
    {
        $this->isMainHeader = $isMainHeader;

        return $this;
    }

    public function getParentHeader(): ?self
    {
        return $this->parentHeader;
    }

    public function setParentHeader(?self $parentHeader): self
    {
        $this->parentHeader = $parentHeader;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getChildHeaders(): Collection
    {
        return $this->childHeaders;
    }

    public function addChildHeader(self $childHeader): self
    {
        if (!$this->childHeaders->contains($childHeader)) {
            $this->childHeaders[] = $childHeader;
            $childHeader->setParentHeader($this);
        }

        return $this;
    }

    public function removeChildHeader(self $childHeader): self
    {
        if ($this->childHeaders->contains($childHeader)) {
            $this->childHeaders->removeElement($childHeader);

            if ($childHeader->getParentHeader() === $this) {
                $childHeader->setParentHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return null|array
     */
    public function getAuthorizations(): ?array
    {
        return $this->authorizations;
    }

    /**
     * @param mixed $authorizations
     */
    public function setAuthorizations($authorizations): self
    {
        $this->authorizations = $authorizations;

        return $this;
    }
}
