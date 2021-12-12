<?php


namespace App\Traits\DateTime;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait for fields dateCreation and dateUpdate in entities
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
trait EntityDateTrait
{
    /**
     * @ORM\Column(type="datetime_immutable", nullable=false)
     */
    protected $dateCreation;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $dateUpdate;

    /**
     * @return DateTimeImmutable
     */
    public function getDateCreation(): DateTimeImmutable
    {
        return $this->dateCreation;
    }

    /**
     * @return string date already convert
     */
    public function getDateUpdate(): string
    {
        if (!$this->dateUpdate) {
            return 'Aucune date de mise à jour';
        }

        $dateUpdate = date_format($this->dateUpdate, 'd/m/Y H:i:s');

        if (!$dateUpdate) {
            return '';
        }

        return $dateUpdate;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     *
     * @return void
     */
    public function prePersist(): void
    {
        if (null === $this->dateCreation) {
            $this->dateCreation = new DateTimeImmutable();
        }

        $this->dateUpdate = new DateTime();
    }
}