<?php


namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class StructurePropertyToTransform implements DataTransformerInterface
{
    /**
     * @param mixed $structure
     *
     * @return mixed|string
     */
    public function transform($structure)
    {
        if (null === $structure) {
            return '';
        }

        $data[] = [
            'id'   => $structure->getId(),
            'text' => $structure->getName(),
        ];

        return $data;
    }

    /**
     * @param mixed $structureData
     *
     * @return mixed|void
     */
    public function reverseTransform($structureData)
    {
        if (!$structureData) {
            return;
        }

        return $structureData;
    }
}