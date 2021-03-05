<?php

namespace App\Traits\Structure;

use App\Entity\Structure\Clinic;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait to implement clinic logique into each entities
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
trait ClinicTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Structure\Clinic")
     */
    protected $clinic;

    /**
     * @return Clinic|null
     */
    public function getClinic(): ?Clinic
    {
        return $this->clinic;
    }

    /**
     * @param $clinic
     */
    public function setClinic($clinic): void
    {
        $this->clinic = $clinic;
    }
}