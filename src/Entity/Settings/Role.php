<?php

namespace App\Entity\Settings;

use App\Interfaces\DateTime\EntityDateInterface;
use App\Interfaces\User\CreatedByInterface;
use App\Repository\Settings\RoleRepository;
use App\Traits\DateTime\EntityDateTrait;
use App\Traits\User\CreatedByTrait;
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
class Role implements EntityDateInterface, CreatedByInterface
{
    use EntityDateTrait;
    use CreatedByTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\ManyToOne(targetEntity=Role::class, inversedBy="childRoles")
     */
    private ?Role $parentRole;

    /**
     * @ORM\OneToMany(targetEntity=Role::class, mappedBy="parentRole")
     */
    private $childRoles;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private ?array $authorizations = [];

    /**
     * @ORM\Column(type="enumRoleLevel", nullable=true)
     */
    private ?string $permissionLevel;

    /**
     * @ORM\Column(type="enumRoleType")
     */
    private ?string $type;

    public function __construct()
    {
        $this->childRoles = new ArrayCollection();
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

    public function getAuthorizations(): ?array
    {
        return $this->authorizations;
    }

    public function addAuthorization(string $authorization): self
    {
        if (null === $this->authorizations) {
            $this->authorizations = [];
        }

        if (!in_array($authorization, $this->authorizations, true)) {
            $this->authorizations[] = $authorization;
        }

        return $this;
    }

    public function removeAuthorization(string $authorization): self
    {
        if (($key = array_search($authorization, $this->authorizations, true)) !== false) {
            unset($this->authorizations[$key]);
        }

        return $this;
    }

    public function setAuthorizations(?array $authorizations): self
    {
        $this->authorizations = $authorizations;

        return $this;
    }

    public function getPermissionLevel(): string
    {
        return $this->permissionLevel ?? 'user';
    }

    public function setPermissionLevel($permissionLevel): self
    {
        $this->permissionLevel = $permissionLevel;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType($type): self
    {
        $this->type = $type;

        return $this;
    }
}
