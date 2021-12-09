<?php

namespace App\Entity\Structure;

use App\Entity\Contents\Article;
use App\Interfaces\DateTime\EntityDateInterface;
use App\Interfaces\Socials\SocialInterface;
use App\Interfaces\Structure\PhotoInterface;
use App\Interfaces\Structure\PresentationInterface;
use App\Interfaces\User\CreatedByInterface;
use App\Interfaces\User\UserEntityInterface;
use App\Traits\DateTime\EntityDateTrait;
use App\Traits\Socials\SocialTrait;
use App\Traits\Structure\PhotoTrait;
use App\Traits\Structure\PresentationTrait;
use App\Traits\User\CreatedByTrait;
use App\Traits\User\UserEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use App\Repository\Structure\EmployeeRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=EmployeeRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable()
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class Employee implements EntityDateInterface, UserInterface,
                          PresentationInterface, PhotoInterface, SocialInterface,
                          UserEntityInterface, CreatedByInterface
{
    use EntityDateTrait;
    use PhotoTrait;
    use PresentationTrait;
    use SocialTrait;
    use UserEntityTrait;
    use CreatedByTrait;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isManager;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Structure\Sector", inversedBy="employees")
     */
    private $sector;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isVerified = false;

    /**
     * @ORM\OneToMany(targetEntity=Article::class, mappedBy="createdByEmployee")
     */
    private $articles;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->sector   = new ArrayCollection();
        $this->articles = new ArrayCollection();
    }

    /**
     * @return bool|null
     */
    public function getIsManager(): ?bool
    {
        return $this->isManager;
    }

    /**
     * @param bool|null $isManager
     *
     * @return $this
     */
    public function setIsManager(?bool $isManager): self
    {
        $this->isManager = $isManager;

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
            $article->setCreatedByEmployee($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->contains($article)) {
            $this->articles->removeElement($article);
            // set the owning side to null (unless already changed)
            if ($article->getCreatedByEmployee() === $this) {
                $article->setCreatedByEmployee(null);
            }
        }

        return $this;
    }
}
