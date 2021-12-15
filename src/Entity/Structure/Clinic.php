<?php

namespace App\Entity\Structure;

use App\Entity\Settings\Configuration;
use App\Interfaces\DateTime\EntityDateInterface;
use App\Interfaces\Priority\PriorityInterface;
use App\Interfaces\Socials\SocialInterface;
use App\Interfaces\Structure\AddressInterface;
use App\Interfaces\Structure\PhotoInterface;
use App\Interfaces\User\CreatedByInterface;
use App\Repository\Structure\ClinicRepository;
use App\Traits\DateTime\EntityDateTrait;
use App\Traits\Priority\PriorityTrait;
use App\Traits\Socials\SocialTrait;
use App\Traits\Structure\AddressTrait;
use App\Traits\Structure\PhotoTrait;
use App\Traits\User\CreatedByTrait;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=ClinicRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable()
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class Clinic implements EntityDateInterface, PriorityInterface, PhotoInterface, AddressInterface, SocialInterface
{
    use EntityDateTrait;
    use PriorityTrait;
    use PhotoTrait;
    use AddressTrait;
    use SocialTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected ?string $nameSlugiffied;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $fiscalDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $siren;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $siret;

    /**
     * @ORM\Column(type="enumStructureTypes", nullable=true)
     */
    private ?string $type;

    /**
     * @ORM\OneToMany(targetEntity=Configuration::class, mappedBy="clinic", orphanRemoval=true)
     */
    private $configurations;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $latitude;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $longitude;

    public function __construct()
    {
        $this->configurations = new ArrayCollection();
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

    public function getNameSlugiffied(): ?string
    {
        return $this->nameSlugiffied;
    }

    public function setNameSlugiffied(?string $nameSlugiffied): self
    {
        $this->nameSlugiffied = $nameSlugiffied;

        return $this;
    }

    public function getFiscalDate(): ?DateTimeInterface
    {
        return $this->fiscalDate;
    }

    public function setFiscalDate(?DateTimeInterface $fiscalDate): self
    {
        $this->fiscalDate = $fiscalDate;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSiren(): ?string
    {
        return $this->siren;
    }

    public function setSiren(?string $siren): self
    {
        $this->siren = $siren;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): self
    {
        $this->siret = $siret;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|Configuration[]
     */
    public function getConfigurations(): Collection
    {
        return $this->configurations;
    }

    public function addConfiguration(Configuration $configuration): self
    {
        if (!$this->configurations->contains($configuration)) {
            $this->configurations[] = $configuration;
            $configuration->setClinic($this);
        }

        return $this;
    }

    public function removeConfiguration(Configuration $configuration): self
    {
        if ($this->configurations->contains($configuration)) {
            $this->configurations->removeElement($configuration);
            // set the owning side to null (unless already changed)
            if ($configuration->getClinic() === $this) {
                $configuration->setClinic(null);
            }
        }

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }
}
