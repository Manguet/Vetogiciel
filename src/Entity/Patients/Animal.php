<?php

namespace App\Entity\Patients;

use App\Interfaces\DateTime\EntityDateInterface;
use App\Interfaces\User\CreatedByInterface;
use App\Traits\DateTime\EntityDateTrait;
use App\Traits\User\CreatedByTrait;
use Doctrine\ORM\Mapping as ORM;

use App\Repository\Patients\AnimalRepository;
use App\Entity\Structure\WaitingRoom;

/**
 * @ORM\Entity(repositoryClass=AnimalRepository::class)
 * @ORM\HasLifecycleCallbacks
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class Animal implements EntityDateInterface, CreatedByInterface
{
    use EntityDateTrait;
    use CreatedByTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $age;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    private ?string $color;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private ?string $transponder;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private ?string $tatoo;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $isLof;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $isInsured;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isAlive = true;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patients\Race", cascade={"persist"})
     */
    private ?Race $race;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patients\Species", cascade={"persist"})
     */
    private ?Species $species;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patients\Comment")
     */
    private ?Comment $comment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patients\Client", inversedBy="animals")
     */
    private ?Client $client;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Patients\Folder", mappedBy="animal", cascade={"persist", "remove"})
     */
    private ?Folder $folder;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Structure\WaitingRoom", inversedBy="animals")
     */
    private ?WaitingRoom $waitingRoom;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $birthdate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $idLocalization;

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

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getTransponder(): ?string
    {
        return $this->transponder;
    }

    public function setTransponder(?string $transponder): self
    {
        $this->transponder = $transponder;

        return $this;
    }

    public function getTatoo(): ?string
    {
        return $this->tatoo;
    }

    public function setTatoo(?string $tatoo): self
    {
        $this->tatoo = $tatoo;

        return $this;
    }

    public function getIsLof(): ?bool
    {
        return $this->isLof;
    }

    public function setIsLof(?bool $isLof): self
    {
        $this->isLof = $isLof;

        return $this;
    }

    public function getIsInsured(): ?bool
    {
        return $this->isInsured;
    }

    public function setIsInsured(?bool $isInsured): self
    {
        $this->isInsured = $isInsured;

        return $this;
    }

    public function getIsAlive(): ?bool
    {
        return $this->isAlive;
    }

    public function setIsAlive(bool $isAlive): self
    {
        $this->isAlive = $isAlive;

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

    public function getSpecies(): ?Species
    {
        return $this->species;
    }

    public function setSpecies(?Species $species): self
    {
        $this->species = $species;

        return $this;
    }

    public function getComment(): ?Comment
    {
        return $this->comment;
    }

    public function setComment(?Comment $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getFolder(): ?Folder
    {
        return $this->folder;
    }

    public function setFolder(?Folder $folder): self
    {
        $this->folder = $folder;

        if ($folder && $folder->getAnimal() !== $this) {
            $folder->setAnimal($this);
        }

        return $this;
    }

    public function getWaitingRoom(): ?WaitingRoom
    {
        return $this->waitingRoom;
    }

    public function setWaitingRoom(?WaitingRoom $waitingRoom): self
    {
        $this->waitingRoom = $waitingRoom;

        return $this;
    }

    public function getBirthdate()
    {
        return $this->birthdate;
    }

    public function setBirthdate($birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getIdLocalization(): ?string
    {
        return $this->idLocalization;
    }

    public function setIdLocalization(?string $idLocalization): self
    {
        $this->idLocalization = $idLocalization;

        return $this;
    }
}
