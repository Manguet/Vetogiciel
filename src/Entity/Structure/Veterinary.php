<?php

namespace App\Entity\Structure;

use App\Interfaces\DateTime\EntityDateInterface;
use App\Interfaces\Structure\ClinicInterface;
use App\Interfaces\User\UserEntityInterface;
use App\Traits\DateTime\EntityDateTrait;
use App\Traits\Structure\ClinicTrait;
use App\Traits\User\UserEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use App\Repository\Structure\VeterinaryRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=VeterinaryRepository::class)
 * @ORM\HasLifecycleCallbacks
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class Veterinary implements EntityDateInterface, UserEntityInterface, UserInterface, ClinicInterface
{
    use EntityDateTrait;
    use UserEntityTrait;
    use ClinicTrait;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $color;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $number;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Structure\Sector", inversedBy="veterinaries")
     */
    private $sector;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isVerified = false;

    /**
     * Veterinary constructor.
     */
    public function __construct()
    {
        $this->sector = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * @param string|null $color
     *
     * @return $this
     */
    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNumber(): ?string
    {
        return $this->number;
    }

    /**
     * @param string|null $number
     *
     * @return $this
     */
    public function setNumber(?string $number): self
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return Collection|Sector[]
     */
    public function getSector(): Collection
    {
        return $this->sector;
    }

    /**
     * @param Sector $sector
     *
     * @return $this
     */
    public function addSector(Sector $sector): self
    {
        if (!$this->sector->contains($sector)) {
            $this->sector[] = $sector;
        }

        return $this;
    }

    /**
     * @param Sector $sector
     *
     * @return $this
     */
    public function removeSector(Sector $sector): self
    {
        if ($this->sector->contains($sector)) {
            $this->sector->removeElement($sector);
        }

        return $this;
    }

    /**
     * @return null|bool
     */
    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    /**
     * @param null|bool $isVerified
     *
     * @return $this
     */
    public function setIsVerified(?bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
