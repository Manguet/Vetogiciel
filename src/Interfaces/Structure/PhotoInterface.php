<?php

namespace App\Interfaces\Structure;

use Symfony\Component\HttpFoundation\File\File;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
interface PhotoInterface
{
    public function setPhotoFile(?File $photo = null);

    public function getPhotoFile();

    public function setPhoto($photo);

    public function getPhoto(): ?string;
}