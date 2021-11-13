<?php

namespace App\Entity\Structure;

use App\Entity\Contents\Article;
use App\Interfaces\DateTime\EntityDateInterface;
use App\Interfaces\Socials\SocialInterface;
use App\Interfaces\Structure\ClinicInterface;
use App\Interfaces\Structure\PhotoInterface;
use App\Interfaces\Structure\PresentationInterface;
use App\Interfaces\User\UserEntityInterface;
use App\Traits\DateTime\EntityDateTrait;
use App\Traits\Socials\SocialTrait;
use App\Traits\Structure\ClinicTrait;
use App\Traits\Structure\PhotoTrait;
use App\Traits\Structure\PresentationTrait;
use App\Traits\User\UserEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

use App\Repository\Structure\VeterinaryRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=VeterinaryRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable()
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class Veterinary implements EntityDateInterface, UserEntityInterface, UserInterface,
                            ClinicInterface, PhotoInterface, PresentationInterface,
                            SocialInterface
{
    use EntityDateTrait;
    use UserEntityTrait;
    use ClinicTrait;
    use PhotoTrait;
    use PresentationTrait;
    use SocialTrait;

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
     * @ORM\OneToMany(targetEntity=Article::class, mappedBy="createdBy")
     */
    private $articles;

    /**
     * @ORM\Column(type="enumVeterinaryTypes", nullable=true)
     */
    private $type;

    /**
     * Veterinary constructor.
     */
    public function __construct()
    {
        $this->sector   = new ArrayCollection();
        $this->articles = new ArrayCollection();
    }

    /**
     * @return array
     *
     * Serialization use for photoFile
     */
    public function __serialize(): array
    {
        return [
            'id'        => $this->id,
            'email'     => $this->email,
            'photoFile' => base64_encode($this->photoFile),
            'photo'     => $this->photo,
            'password'  => $this->password,
        ];
    }

    /**
     * @param array $serialized
     *
     * Déserialize for photoFile
     */
    public function __unserialize(array $serialized): void
    {
        $this->id        = $serialized['id'];
        $this->email     = $serialized['email'];
        $this->photoFile = base64_decode($serialized['photoFile']);
        $this->photo     = $serialized['photo'];
        $this->password  = $serialized['password'];
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

    /**
     * @return Collection|Article[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setCreatedBy($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->contains($article)) {
            $this->articles->removeElement($article);
            // set the owning side to null (unless already changed)
            if ($article->getCreatedBy() === $this) {
                $article->setCreatedBy(null);
            }
        }

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
