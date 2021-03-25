<?php

namespace App\Entity\Structure;

use App\Entity\Contents\Article;
use App\Interfaces\DateTime\EntityDateInterface;
use App\Interfaces\User\UserEntityInterface;
use App\Traits\DateTime\EntityDateTrait;
use App\Traits\User\UserEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use App\Repository\Structure\EmployeeRepository;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=EmployeeRepository::class)
 * @ORM\HasLifecycleCallbacks
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class Employee implements EntityDateInterface, UserEntityInterface, UserInterface
{
    use EntityDateTrait;
    use UserEntityTrait;

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
     * @return void
     */
    public function __construct()
    {
        $this->sector   = new ArrayCollection();
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
}
