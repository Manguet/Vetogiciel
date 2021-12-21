<?php

namespace App\Entity\Structure;

use App\Entity\Patients\Consultation;
use App\Interfaces\DateTime\EntityDateInterface;
use App\Interfaces\Structure\ClinicInterface;
use App\Interfaces\User\CreatedByInterface;
use App\Traits\DateTime\EntityDateTrait;
use App\Traits\Structure\ClinicTrait;
use App\Traits\User\CreatedByTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use App\Repository\Structure\PrestationRepository;

/**
 * @ORM\Entity(repositoryClass=PrestationRepository::class)
 * @ORM\HasLifecycleCallbacks
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class Prestation implements EntityDateInterface, CreatedByInterface, ClinicInterface
{
    use EntityDateTrait;
    use CreatedByTrait;
    use ClinicTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=120)
     */
    private ?string $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    private ?string $code;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $quantity;

    /**
     * @ORM\Column(type="float")
     */
    private ?float $PriceHT;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $PriceTTC;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $reduction;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Patients\Consultation", inversedBy="prestations")
     */
    private $consultation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Structure\Vat")
     */
    private ?Vat $vat;

    /**
     * @ORM\Column(type="enumPrestationTypes", nullable=true)
     */
    private $type;

    public function __construct()
    {
        $this->consultation = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPriceHT(): ?float
    {
        return $this->PriceHT;
    }

    public function setPriceHT(float $PriceHT): self
    {
        $this->PriceHT = $PriceHT;

        return $this;
    }

    public function getPriceTTC(): ?float
    {
        return $this->PriceTTC;
    }

    public function setPriceTTC(?float $PriceTTC): self
    {
        $this->PriceTTC = $PriceTTC;

        return $this;
    }

    public function getReduction(): ?float
    {
        return $this->reduction;
    }

    public function setReduction(?float $reduction): self
    {
        $this->reduction = $reduction;

        return $this;
    }

    /**
     * @return Collection|Consultation[]
     */
    public function getConsultation(): Collection
    {
        return $this->consultation;
    }

    public function addConsultation(Consultation $consultation): self
    {
        if (!$this->consultation->contains($consultation)) {
            $this->consultation[] = $consultation;
        }

        return $this;
    }

    public function removeConsultation(Consultation $consultation): self
    {
        if ($this->consultation->contains($consultation)) {
            $this->consultation->removeElement($consultation);
        }

        return $this;
    }

    public function getVat(): ?Vat
    {
        return $this->vat;
    }

    public function setVat(?Vat $vat): self
    {
        $this->vat = $vat;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type): self
    {
        $this->type = $type;

        return $this;
    }
}
