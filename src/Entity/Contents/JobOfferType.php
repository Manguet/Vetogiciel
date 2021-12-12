<?php

namespace App\Entity\Contents;

use App\Interfaces\DateTime\EntityDateInterface;
use App\Repository\Contents\JobOfferTypeRepository;
use App\Traits\DateTime\EntityDateTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=JobOfferTypeRepository::class)
 * @ORM\HasLifecycleCallbacks
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class JobOfferType implements EntityDateInterface
{
    use EntityDateTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $titleUrl;

    /**
     * @ORM\OneToMany(targetEntity=JobOffer::class, mappedBy="type", orphanRemoval=true)
     */
    private $jobOffers;

    public function __construct()
    {
        $this->jobOffers = new ArrayCollection();
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

    /**
     * @return Collection|JobOffer[]
     */
    public function getJobOffers(): Collection
    {
        return $this->jobOffers;
    }

    public function addJobOffer(JobOffer $jobOffer): self
    {
        if (!$this->jobOffers->contains($jobOffer)) {
            $this->jobOffers[] = $jobOffer;
            $jobOffer->setType($this);
        }

        return $this;
    }

    public function removeJobOffer(JobOffer $jobOffer): self
    {
        if ($this->jobOffers->contains($jobOffer)) {
            $this->jobOffers->removeElement($jobOffer);
            // set the owning side to null (unless already changed)
            if ($jobOffer->getType() === $this) {
                $jobOffer->setType(null);
            }
        }

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
}
