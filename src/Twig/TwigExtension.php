<?php

namespace App\Twig;

use ReflectionClass;
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
            new TwigFilter('getClass', [$this, 'getClass'])
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
     * @param null|string $phone
     *
     * @return string
     */
    public function formatPhone(?string $phone): string
    {
        if (null === $phone) {
            return '';
        }

        $phone = str_replace(['+33', ' ', '-', ',', '.'], ['0', '', '', '', ''], $phone);

        $chunks = str_split($phone, 2);

        return implode('.', $chunks);
    }

    /**
     * @param $object
     *
     * @return string
     */
    public function getClass($object): string
    {
        return (new ReflectionClass($object))->getShortName();
    }
}