<?php

namespace App\Entity\Settings;

use App\Interfaces\DateTime\EntityDateInterface;
use App\Repository\Settings\RoleRepository;
use App\Traits\DateTime\EntityDateTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * @ORM\Entity(repositoryClass=RoleRepository::class)
 *
 * @ORM\HasLifecycleCallbacks
 */
class Role implements EntityDateInterface
{
    use EntityDateTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Role::class, inversedBy="childRoles")
     */
    private $parentRole;

    /**
     * @ORM\OneToMany(targetEntity=Role::class, mappedBy="parentRole")
     */
    private $childRoles;

    /**
     * @ORM\ManyToMany(targetEntity=Authorization::class)
     */
    private $authorizations;

    public function __construct()
    {
        $this->childRoles = new ArrayCollection();
        $this->authorizations = new ArrayCollection();
    }

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

    public function getParentRole(): ?self
    {
        return $this->parentRole;
    }

    public function setParentRole(?self $parentRole): self
    {
        $this->parentRole = $parentRole;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getChildRoles(): Collection
    {
        return $this->childRoles;
    }

    public function addChildRole(self $childRole): self
    {
        if (!$this->childRoles->contains($childRole)) {
            $this->childRoles[] = $childRole;
            $childRole->setParentRole($this);
        }

        return $this;
    }

    public function removeChildRole(self $childRole): self
    {
        if ($this->childRoles->contains($childRole)) {
            $this->childRoles->removeElement($childRole);
            // set the owning side to null (unless already changed)
            if ($childRole->getParentRole() === $this) {
                $childRole->setParentRole(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Authorization[]
     */
    public function getAuthorizations(): Collection
    {
        return $this->authorizations;
    }

    public function addAuthorization(Authorization $authorization): self
    {
        if (!$this->authorizations->contains($authorization)) {
            $this->authorizations[] = $authorization;
        }

        return $this;
    }

    public function removeAuthorization(Authorization $authorization): self
    {
        if ($this->authorizations->contains($authorization)) {
            $this->authorizations->removeElement($authorization);
        }

        return $this;
    }
}
