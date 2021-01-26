<?php

namespace App\Entity\Patients;

use App\Interfaces\DateTime\EntityDateInterface;
use App\Traits\DateTime\EntityDateTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Patients\RaceRepository")
 * @ORM\HasLifecycleCallbacks
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class Race implements EntityDateInterface
{
    use EntityDateTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Species::class, mappedBy="race", orphanRemoval=true)
     */
    private $species;

    public function __construct()
    {
        $this->species = new ArrayCollection();
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

    /**
     * @return Collection|Species[]
     */
    public function getSpecies(): Collection
    {
        return $this->species;
    }

    public function addSpecies(Species $species): self
    {
        if (!$this->species->contains($species)) {
            $this->species[] = $species;
            $species->setRace($this);
        }

        return $this;
    }

    public function removeSpecies(Species $species): self
    {
        if ($this->species->contains($species)) {
            $this->species->removeElement($species);
            // set the owning side to null (unless already changed)
            if ($species->getRace() === $this) {
                $species->setRace(null);
            }
        }

        return $this;
    }
}
