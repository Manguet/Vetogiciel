<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class SectorPropertyToTransform implements DataTransformerInterface
{
    /**
     * @param mixed $sector
     *
     * @return mixed|string
     */
    public function transform($sector)
    {
        if ($sector->isEmpty()) {
            return '';
        }

        $data[] = [
            'id'   => $sector->getId(),
            'text' => $sector->getName(),
        ];

        return $data;
    }

    /**
     * @param mixed $sectorData
     *
     * @return mixed|void
     */
    public function reverseTransform($sectorData)
    {
        if (!$sectorData) {
            return [];
        }

        return $sectorData;
    }
}