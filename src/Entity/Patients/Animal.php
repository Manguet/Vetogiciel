<?php

namespace App\Entity\Patients;

use App\Interfaces\DateTime\EntityDateInterface;
use App\Traits\DateTime\EntityDateTrait;
use Doctrine\ORM\Mapping as ORM;

use App\Repository\Patients\AnimalRepository;
use App\Entity\Structure\WaitingRoom;

/**
 * @ORM\Entity(repositoryClass=AnimalRepository::class)
 * @ORM\HasLifecycleCallbacks
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class Animal implements EntityDateInterface
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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $age;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    private $color;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $transponder;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $tatoo;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isLof;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isInsured;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAlive;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patients\Race")
     */
    private $race;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patients\Species")
     */
    private $species;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patients\Comment")
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patients\Client", inversedBy="animals")
     */
    private $client;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Patients\Folder", mappedBy="animal", cascade={"persist", "remove"})
     */
    private $folder;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Structure\WaitingRoom", inversedBy="animals")
     */
    private $waitingRoom;

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

    public function setFolder(Folder $folder): self
    {
        $this->folder = $folder;

        // set the owning side of the relation if necessary
        if ($folder->getAnimal() !== $this) {
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
}
