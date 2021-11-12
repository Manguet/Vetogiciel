<?php

namespace App\Interfaces\Charts;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
interface ChartCreationInterface
{
    /**
     * @param mixed $chartType
     * @param array $yLabels
     * @param array $xLabels
     * @param array $datas
     * @param array $sizes
     * @param null|string $title
     * @param string|null $color
     *
     * @return mixed
     */
    public function createChart($chartType,
                                array $yLabels,
                                array $xLabels,
                                array $datas,
                                array $sizes,
                                ?string $title = '',
                                ?string $color = '#1A5276');
}