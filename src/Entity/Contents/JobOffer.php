<?php

namespace App\Entity\Contents;

use App\Interfaces\DateTime\EntityDateInterface;
use App\Interfaces\Priority\PriorityInterface;
use App\Interfaces\Structure\ClinicInterface;
use App\Interfaces\User\CreatedByInterface;
use App\Repository\Contents\JobOfferRepository;
use App\Traits\DateTime\EntityDateTrait;
use App\Traits\Priority\PriorityTrait;
use App\Traits\Structure\ClinicTrait;
use App\Traits\User\CreatedByTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=JobOfferRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable()
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class JobOffer implements EntityDateInterface, PriorityInterface, ClinicInterface, CreatedByInterface
{
    use EntityDateTrait;
    use PriorityTrait;
    use ClinicTrait;
    use CreatedByTrait;

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
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $isActivated;

    /**
     * @ORM\ManyToOne(targetEntity=JobOfferType::class, inversedBy="jobOffers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity=Candidate::class, mappedBy="joboffer")
     */
    private $candidates;

    public function __construct()
    {
        $this->candidates = new ArrayCollection();
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

    public function getTitleUrl(): ?string
    {
        return $this->titleUrl;
    }

    public function setTitleUrl(string $titleUrl): self
    {
        $this->titleUrl = $titleUrl;

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

    /**
     * @return string|string[]
     *
     * use to decode in front without twig extension and raw ect
     */
    public function getDescriptionDecode()
    {
        $withoutHtml = strip_tags($this->description);

        $baseHtml = ['&agrave;', '&nbsp;', '&#39;', '&eacute;', '&quot;', '&rsquo;', '&egrave;', '&ecirc;', '&acirc;', '&ccedil;', '&hellip;', '&ndash;', '&oelig;', '&Eacute;'];

        $result = ['à', ' ', '\'', 'é', '"', '\'', 'è', 'ê', 'â', 'ç', '...', '-', 'oe', 'E'];

        return str_replace($baseHtml, $result, $withoutHtml);
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

    /**
     * @return Collection|Candidate[]
     */
    public function getCandidates(): Collection
    {
        return $this->candidates;
    }

    public function addCandidate(Candidate $candidate): self
    {
        if (!$this->candidates->contains($candidate)) {
            $this->candidates[] = $candidate;
            $candidate->setJoboffer($this);
        }

        return $this;
    }

    public function removeCandidate(Candidate $candidate): self
    {
        if ($this->candidates->contains($candidate)) {
            $this->candidates->removeElement($candidate);
            // set the owning side to null (unless already changed)
            if ($candidate->getJoboffer() === $this) {
                $candidate->setJoboffer(null);
            }
        }

        return $this;
    }
}
