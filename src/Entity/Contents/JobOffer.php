<?php

namespace App\Entity\Contents;

use App\Interfaces\DateTime\EntityDateInterface;
use App\Interfaces\Priority\PriorityInterface;
use App\Interfaces\Structure\ClinicInterface;
use App\Interfaces\Structure\PresentationInterface;
use App\Repository\Contents\JobOfferRepository;
use App\Traits\DateTime\EntityDateTrait;
use App\Traits\Priority\PriorityTrait;
use App\Traits\Structure\ClinicTrait;
use App\Traits\Structure\PresentationTrait;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=JobOfferRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable()
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class JobOffer implements EntityDateInterface, PriorityInterface, ClinicInterface, PresentationInterface
{
    use EntityDateTrait;
    use PriorityTrait;
    use ClinicTrait;
    use PresentationTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $titleUrl;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $isActivated;

    /**
     * @ORM\ManyToOne(targetEntity=JobOfferType::class, inversedBy="jobOffers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

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

    public function getTitleUrl(): ?string
    {
        return $this->titleUrl;
    }

    public function setTitleUrl(string $titleUrl): self
    {
        $this->titleUrl = $titleUrl;

        return $this;
    }

    public function getIsActivated(): ?bool
    {
        return $this->isActivated;
    }

    public function setIsActivated(?bool $isActivated): self
    {
        $this->isActivated = $isActivated;

        return $this;
    }

    public function getType(): ?JobOfferType
    {
        return $this->type;
    }

    public function setType(?JobOfferType $type): self
    {
        $this->type = $type;

        return $this;
    }
}
