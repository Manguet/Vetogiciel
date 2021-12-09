<?php

namespace App\Entity\Patients;

use App\Interfaces\DateTime\EntityDateInterface;
use App\Interfaces\User\CreatedByInterface;
use App\Traits\DateTime\EntityDateTrait;
use App\Traits\User\CreatedByTrait;
use Doctrine\ORM\Mapping as ORM;

use App\Repository\Patients\SpeciesRepository;

/**
 * @ORM\Entity(repositoryClass=SpeciesRepository::class)
 * @ORM\HasLifecycleCallbacks
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class Species implements EntityDateInterface, CreatedByInterface
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
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Race::class, inversedBy="species")
     * @ORM\JoinColumn(nullable=false)
     */
    private $race;

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

    public function getRace(): ?Race
    {
        return $this->race;
    }

    public function setRace(?Race $race): self
    {
        $this->race = $race;

        return $this;
    }
}
