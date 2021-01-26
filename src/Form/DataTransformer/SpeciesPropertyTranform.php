<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class SpeciesPropertyTranform implements DataTransformerInterface
{

    /**
     * @param mixed $species
     *
     * @return mixed|string
     */
    public function transform($species)
    {
        if (null === $species) {
            return '';
        }

        $data[] = [
            'id'   => $species->getId(),
            'text' => $species->getName(),
        ];

        return $data;
    }


    public function reverseTransform($speciesData)
    {
        if (!$speciesData) {
            return;
        }

        return $speciesData;
    }
}