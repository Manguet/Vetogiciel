<?php

namespace App\Interfaces\Slugger;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
interface SluggerInterface
{
    /**
     * @param string $title
     * @param string $class
     *
     * @return string
     */
    public function generateSlugUrl(string $title, string $class): string;
}