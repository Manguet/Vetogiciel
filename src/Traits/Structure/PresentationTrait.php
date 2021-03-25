<?php

namespace App\Traits\Structure;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * Trait to implement logic in entity to create presentation.
 * Need to implement EntityDateTrait too for dateUpdate
 * Take care to serialization in entity implement UserInterface
 */
trait PresentationTrait
{
    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string|null
     */
    private $presentation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string|null
     */
    private $cv;

    /**
     * @Vich\UploadableField(mapping="cvs", fileNameProperty="cv")
     * @Assert\File(
     *      maxSize="2048k",
     *      maxSizeMessage="Le fichier excède 1000Ko.",
     *      mimeTypes={"image/png", "image/jpeg", "image/jpg", "application/pdf"},
     *      mimeTypesMessage= "formats autorisés: png, jpeg, jpg, pdf"
     * )
     * @var null|File
     */
    private $cvFile;

    /**
     * @return string|null
     */
    public function getPresentation(): ?string
    {
        return $this->presentation;
    }

    /**
     * @param string|null $presentation
     */
    public function setPresentation(?string $presentation): void
    {
        $this->presentation = $presentation;
    }

    public function setCVFile(File $cv = null): void
    {
        $this->cvFile = $cv;

        if ($cv) {
            $this->dateUpdate = new DateTime('now');
        }
    }

    public function getCVFile(): ?File
    {
        return $this->cvFile;
    }

    public function setCV($cv): void
    {
        $this->cv = $cv;
    }

    public function getCV(): ?string
    {
        return $this->cv;
    }
}