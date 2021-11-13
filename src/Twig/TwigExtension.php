<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigTest;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * Extension to check is entity is an instance of
 */
class TwigExtension extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('phone', [$this, 'formatPhone']),
        ];
    }

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
        return $var instanceof $instance;
    }

    /**
     * @param string $phone
     *
     * @return string
     */
    public function formatPhone(string $phone): string
    {
        $phone = str_replace(['+33', ' ', '-', ',', '.'], ['0', '', '', '', ''], $phone);

        $chunks = str_split($phone, 2);

        return implode('.', $chunks);
    }
}