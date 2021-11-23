<?php

namespace App\Entity\Settings;

use App\Repository\Settings\AuthorizationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * @ORM\Entity(repositoryClass=AuthorizationRepository::class)
 */
class Authorization
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=120)
     */
    private $relatedEntity;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    private $canAccess;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private $canAdd;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private $canShow;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private $canEdit;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private $canDelete;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRelatedEntity(): ?string
    {
        return $this->relatedEntity;
    }

    public function setRelatedEntity(string $relatedEntity): self
    {
        $this->relatedEntity = $relatedEntity;

        return $this;
    }

    public function getCanAccess(): ?string
    {
        return $this->canAccess;
    }

    public function setCanAccess(?string $canAccess): self
    {
        $this->canAccess = $canAccess;

        return $this;
    }

    public function getCanAdd(): ?string
    {
        return $this->canAdd;
    }

    public function setCanAdd(?string $canAdd): self
    {
        $this->canAdd = $canAdd;

        return $this;
    }

    public function getCanShow(): ?string
    {
        return $this->canShow;
    }

    public function setCanShow(?string $canShow): self
    {
        $this->canShow = $canShow;

        return $this;
    }

    public function getCanEdit(): ?string
    {
        return $this->canEdit;
    }

    public function setCanEdit(?string $canEdit): self
    {
        $this->canEdit = $canEdit;

        return $this;
    }

    public function getCanDelete(): ?string
    {
        return $this->canDelete;
    }

    public function setCanDelete(?string $canDelete): self
    {
        $this->canDelete = $canDelete;

        return $this;
    }
}
