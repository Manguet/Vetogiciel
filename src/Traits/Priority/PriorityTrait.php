<?php

namespace App\Traits\Priority;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Benjamin Manguet
 */
trait PriorityTrait
{
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $priority;

    /**
     * @return int|null
     */
    public function getPriority(): ?int
    {
        return $this->priority;
    }

    /**
     * @param int|null $priority
     *
     * @return $this
     */
    public function setPriority(?int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }
}