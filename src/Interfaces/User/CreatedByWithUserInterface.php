<?php

namespace App\Interfaces\User;

use App\Entity\Patients\Client;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
interface CreatedByWithUserInterface
{
    public function getCreatedByClient(): ?Client;

    public function setCreatedByClient(?Client $createdByClient): self;

    public function getCreatedBy(): ?UserInterface;

    public function setCreatedBy(UserInterface $user): self;
}