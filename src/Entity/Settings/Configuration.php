<?php

namespace App\Entity\Settings;

use App\Entity\Structure\Clinic;
use App\Repository\Settings\ConfigurationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ConfigurationRepository::class)
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class Configuration
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=80)
     */
    private string $name;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private ?array $datas = [];

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    private ?string $fieldType;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private ?array $settings = [];

    /**
     * @ORM\Column(type="enumConfigurationTypes")
     */
    private string $configurationType;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private string $onglet;

    /**
     * @ORM\Column(type="integer")
     */
    private int $position;

    /**
     * @ORM\ManyToOne(targetEntity=Clinic::class, inversedBy="configurations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $clinic;

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

    public function getDatas(): ?array
    {
        return $this->datas;
    }

    public function setDatas(?array $datas): self
    {
        $this->datas = $datas;

        return $this;
    }

    public function getFieldType(): ?string
    {
        return $this->fieldType;
    }

    public function setFieldType(?string $fieldType): self
    {
        $this->fieldType = $fieldType;

        return $this;
    }

    public function getSettings(): ?array
    {
        return $this->settings;
    }

    public function setSettings(?array $settings): self
    {
        $this->settings = $settings;

        return $this;
    }

    public function getConfigurationType()
    {
        return $this->configurationType;
    }

    public function setConfigurationType($configurationType): self
    {
        $this->configurationType = $configurationType;

        return $this;
    }

    public function getOnglet(): ?string
    {
        return $this->onglet;
    }

    public function setOnglet(string $onglet): self
    {
        $this->onglet = $onglet;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getClinic(): ?Clinic
    {
        return $this->clinic;
    }

    public function setClinic(?Clinic $clinic): self
    {
        $this->clinic = $clinic;

        return $this;
    }
}
