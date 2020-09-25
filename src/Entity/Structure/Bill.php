<?php

namespace App\Entity\Structure;

use App\Interfaces\DateTime\EntityDateInterface;
use App\Traits\DateTime\EntityDateTrait;
use Doctrine\ORM\Mapping as ORM;

use App\Repository\Structure\BillRepository;

/**
 * @ORM\Entity(repositoryClass=BillRepository::class)
 * @ORM\HasLifecycleCallbacks
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class Bill implements EntityDateInterface
{
    use EntityDateTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=80)
     */
    private $number;

    /**
     * @ORM\Column(type="float")
     */
    private $priceHT;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $priceTTC;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getPriceHT(): ?float
    {
        return $this->priceHT;
    }

    public function setPriceHT(float $priceHT): self
    {
        $this->priceHT = $priceHT;

        return $this;
    }

    public function getPriceTTC(): ?float
    {
        return $this->priceTTC;
    }

    public function setPriceTTC(?float $priceTTC): self
    {
        $this->priceTTC = $priceTTC;

        return $this;
    }
}
