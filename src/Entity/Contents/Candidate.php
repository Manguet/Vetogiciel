<?php

namespace App\Entity\Contents;

use App\Interfaces\DateTime\EntityDateInterface;
use App\Interfaces\Structure\PresentationInterface;
use App\Repository\Contents\CandidateRepository;
use App\Traits\DateTime\EntityDateTrait;
use App\Traits\Structure\PresentationTrait;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=CandidateRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable()
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class Candidate implements EntityDateInterface, PresentationInterface
{
    use EntityDateTrait;
    use PresentationTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="enumGender")
     */
    private $gender;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity=JobOffer::class, inversedBy="candidates")
     */
    private $joboffer;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isResponseSend;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGender()
    {
        return $this->gender;
    }

    public function setGender($gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getJoboffer(): ?JobOffer
    {
        return $this->joboffer;
    }

    public function setJoboffer(?JobOffer $joboffer): self
    {
        $this->joboffer = $joboffer;

        return $this;
    }

    public function getIsResponseSend(): ?bool
    {
        return $this->isResponseSend;
    }

    public function setIsResponseSend(?bool $isResponseSend): self
    {
        $this->isResponseSend = $isResponseSend;

        return $this;
    }
}
