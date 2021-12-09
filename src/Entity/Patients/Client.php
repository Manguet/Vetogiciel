<?php

namespace App\Entity\Patients;

use App\Entity\Structure\Clinic;
use App\Interfaces\DateTime\EntityDateInterface;
use App\Interfaces\User\CreatedByInterface;
use App\Interfaces\User\CreatedByWithUserInterface;
use App\Interfaces\User\UserEntityInterface;
use App\Traits\DateTime\EntityDateTrait;
use App\Traits\User\CreatedByWithUserTrait;
use App\Traits\User\UserEntityTrait;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use App\Repository\Patients\ClientRepository;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 * @ORM\HasLifecycleCallbacks
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class Client implements EntityDateInterface, UserInterface, UserEntityInterface, CreatedByWithUserInterface
{
    use EntityDateTrait;
    use UserEntityTrait;
    use CreatedByWithUserTrait;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address2;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $postalCode;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $phoneNumber2;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patients\Comment")
     */
    private $comment;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isInDebt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Patients\Animal", mappedBy="client")
     */
    private $animals;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastVisit;

    /**
     * @ORM\ManyToMany(targetEntity=Clinic::class)
     */
    private $clinic;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->animals  = new ArrayCollection();
        $this->clinic   = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     *
     * @return $this
     */
    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    /**
     * @param string|null $address2
     *
     * @return $this
     */
    public function setAddress2(?string $address2): self
    {
        $this->address2 = $address2;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    /**
     * @param string|null $postalCode
     *
     * @return $this
     */
    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     *
     * @return $this
     */
    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @param string|null $phoneNumber
     *
     * @return $this
     */
    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber2(): ?string
    {
        return $this->phoneNumber2;
    }

    /**
     * @param string|null $phoneNumber2
     *
     * @return $this
     */
    public function setPhoneNumber2(?string $phoneNumber2): self
    {
        $this->phoneNumber2 = $phoneNumber2;

        return $this;
    }

    /**
     * @return Comment|null
     */
    public function getComment(): ?Comment
    {
        return $this->comment;
    }

    /**
     * @param Comment|null $comment
     *
     * @return $this
     */
    public function setComment(?Comment $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsInDebt(): ?bool
    {
        return $this->isInDebt;
    }

    /**
     * @param bool|null $isInDebt
     *
     * @return $this
     */
    public function setIsInDebt(?bool $isInDebt): self
    {
        $this->isInDebt = $isInDebt;

        return $this;
    }

    /**
     * @return Collection|Animal[]
     */
    public function getAnimals(): Collection
    {
        return $this->animals;
    }

    /**
     * @param Animal $animal
     *
     * @return $this
     */
    public function addAnimal(Animal $animal): self
    {
        if (!$this->animals->contains($animal)) {
            $this->animals[] = $animal;
            $animal->setClient($this);
        }

        return $this;
    }

    /**
     * @param Animal $animal
     *
     * @return $this
     */
    public function removeAnimal(Animal $animal): self
    {
        if ($this->animals->contains($animal)) {
            $this->animals->removeElement($animal);

            if ($animal->getClient() === $this) {
                $animal->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getLastVisit(): ?DateTimeInterface
    {
        return $this->lastVisit;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreFlush()
     *
     * @return Client
     */
    public function setLastVisit(): self
    {
        $this->lastVisit = new DateTime('now');

        return $this;
    }

    /**
     * @return Collection|Clinic[]
     */
    public function getClinic(): Collection
    {
        return $this->clinic;
    }

    /**
     * @param Clinic $clinic
     *
     * @return $this
     */
    public function addClinic(Clinic $clinic): self
    {
        if (!$this->clinic->contains($clinic)) {
            $this->clinic[] = $clinic;
        }

        return $this;
    }

    /**
     * @param Clinic $clinic
     *
     * @return $this
     */
    public function removeClinic(Clinic $clinic): self
    {
        if ($this->clinic->contains($clinic)) {
            $this->clinic->removeElement($clinic);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    /**
     * @param bool $isVerified
     *
     * @return $this
     */
    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
