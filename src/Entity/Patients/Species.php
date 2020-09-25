<?php

namespace App\Entity\Patients;

use Doctrine\ORM\Mapping as ORM;

use App\Repository\Patients\SpeciesRepository;

/**
 * @ORM\Entity(repositoryClass=SpeciesRepository::class)
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class Species
{
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
}
