<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class RacePropertyToTransform implements DataTransformerInterface
{
    /**
     * @param mixed $race
     *
     * @return mixed|string
     */
    public function transform($race)
    {
        if (null === $race) {
            return '';
        }

        $data[] = [
            'id'   => $race->getId(),
            'text' => $race->getName(),
        ];

        return $data;
    }

    /**
     * @param mixed $raceData
     *
     * @return mixed|void
     */
    public function reverseTransform($raceData)
    {
        if (!$raceData) {
            return;
        }

        return $raceData;
    }
}