<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigTest;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * Extension to check is entity is an instance of
 */
class instanceOfExtension extends AbstractExtension
{
    /**
     * @return TwigTest[]
     */
    public function getTests(): array
    {
        return [
            new TwigTest('instanceof', [$this, 'isInstanceof'])
        ];
    }

    /**
     * @param $var
     * @param $instance
     *
     * @return bool
     */
    public function isInstanceof($var, $instance): bool
    {
        return  $var instanceof $instance;
    }
}