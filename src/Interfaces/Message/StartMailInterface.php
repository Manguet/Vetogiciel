<?php

namespace App\Interfaces\Message;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
interface StartMailInterface
{
    /**
     * @return string
     */
    public function getMailTitle(): string;

    /**
     * @return int|null
     */
    public function getUserId(): ?int;
}