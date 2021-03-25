<?php

namespace App\Traits\Structure;

use DateTime;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait to implement logic in entity to upload file.
 * Need to implement EntityDateTrait too for dateUpdate
 * Take care to serialization in entity implement UserInterface
 *
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
trait PhotoTrait
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string|null
     */
    private $photo;

    /**
     * @Vich\UploadableField(mapping="photos", fileNameProperty="photo")
     * @Assert\File(
     *      maxSize="1000k",
     *      maxSizeMessage="Le fichier excède 1000Ko.",
     *      mimeTypes={"image/png", "image/jpeg", "image/jpg", "image/gif"},
     *      mimeTypesMessage= "formats autorisés: png, jpeg, jpg, gif"
     * )
     * @var null|File
     */
    private $photoFile;

    public function setPhotoFile(File $photo = null): void
    {
        $this->photoFile = $photo;

        if ($photo) {
            $this->dateUpdate = new DateTime('now');
        }
    }

    public function getPhotoFile(): ?File
    {
        return $this->photoFile;
    }

    public function setPhoto($photo): void
    {
        $this->photo = $photo;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }
}