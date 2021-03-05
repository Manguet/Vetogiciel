<?php


namespace App\Interfaces\Structure;


use App\Entity\Structure\Clinic;

interface ClinicInterface
{
    /**
     * @return Clinic|null
     */
    public function getClinic(): ?Clinic;

    /**
     * @param $clinic Clinic
     *
     * @return void
     */
    public function setClinic(Clinic $clinic): void;
}