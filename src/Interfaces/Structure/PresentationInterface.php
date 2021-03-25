<?php

namespace App\Interfaces\Structure;

use Symfony\Component\HttpFoundation\File\File;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
interface PresentationInterface
{
    public function getPresentation(): ?string;

    public function setPresentation(?string $presentation): void;

    public function setCVFile(File $cv = null): void;

    public function getCVFile(): ?File;

    public function setCV($cv): void;

    public function getCV(): ?string;
}