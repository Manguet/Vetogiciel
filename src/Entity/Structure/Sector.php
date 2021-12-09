<?php

namespace App\Entity\Structure;

use App\Interfaces\DateTime\EntityDateInterface;
use App\Interfaces\User\CreatedByInterface;
use App\Traits\DateTime\EntityDateTrait;
use App\Traits\User\CreatedByTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Structure\SectorRepository")
 * @ORM\HasLifecycleCallbacks
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class Sector implements EntityDateInterface, CreatedByInterface
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
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Structure\Veterinary", mappedBy="sector")
     */
    private $veterinaries;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Structure\Employee", mappedBy="sector")
     */
    private $employees;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private $icon;

    public function __construct()
    {
        $this->veterinaries = new ArrayCollection();
        $this->employees    = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Veterinary[]
     */
    public function getVeterinaries(): Collection
    {
        return $this->veterinaries;
    }

    public function addVeterinary(?Veterinary $veterinary): self
    {
        if ($veterinary && !$this->veterinaries->contains($veterinary)) {
            $this->veterinaries[] = $veterinary;
            $veterinary->addSector($this);
        }

        return $this;
    }

    public function removeVeterinary(Veterinary $veterinary): self
    {
        if ($this->veterinaries->contains($veterinary)) {
            $this->veterinaries->removeElement($veterinary);
            $veterinary->removeSector($this);
        }

        return $this;
    }

    /**
     * @return Collection|Employee[]
     */
    public function getEmployees(): Collection
    {
        return $this->employees;
    }

    public function addEmployee(?Employee $employee): self
    {
        if ($employee && !$this->employees->contains($employee)) {
            $this->employees[] = $employee;
            $employee->addSector($this);
        }

        return $this;
    }

    public function removeEmployee(Employee $employee): self
    {
        if ($this->employees->contains($employee)) {
            $this->employees->removeElement($employee);
            $employee->removeSector($this);
        }

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
}
