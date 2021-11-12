<?php

namespace App\Service\Charts;

use App\Interfaces\Charts\ChartCreationInterface;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\AreaChart;

/**
 * @author Benjamin manguet <benjamin.manguet@gmail.com>
 */
class ChartCreationServices implements ChartCreationInterface
{
    /**
     * @param mixed $chartType
     * @param array $yLabels
     * @param array $xLabels
     * @param array $datas
     * @param array $sizes
     * @param string|null $title
     * @param string|null $color
     * @return mixed
     */
    public function createChart($chartType, array $yLabels, array $xLabels, array $datas, array $sizes,
                                ?string $title = null, ?string $color = '#1A5276')
    {
        $chart = $chartType;

        $chartDatas = $this->createChartDatas($yLabels, $xLabels, $datas);

        $chart->getData()->setArrayToDataTable($chartDatas);

        /**
         * @var $chart AreaChart
         */
        $chart->getOptions()->setTitle($title);
        $chart->getOptions()->setColors(['#1DB5FF', '#1A5276', '#000']);
        $chart->getOptions()->setHeight($sizes[0] ?? 360);
        $chart->getOptions()->setWidth($sizes[1] ?? 1108);
        $chart->getOptions()->getTitleTextStyle()->setBold(true);
        $chart->getOptions()->getTitleTextStyle()->setColor($color);
        $chart->getOptions()->getTitleTextStyle()->setItalic(true);
        $chart->getOptions()->getAnimation()->setEasing('linear');
        $chart->getOptions()->getAnimation()->setStartup(true);
        $chart->getOptions()->getAnimation()->setDuration(2000);

        if ($chartType instanceof AreaChart) {
            $chart->getOptions()->setPointSize(6);
        }

        return $chart;
    }

    /**
     * @param array $yLabels
     * @param array $xLabels
     * @param array $datas
     *
     * @return array
     */
    private function createChartDatas(array $yLabels, array $xLabels, array $datas): array
    {
        $formattedDatas = [$yLabels];

        foreach ($xLabels as $index => $xLabel) {

            if (isset($datas[$index])) {

                $formattedDatas[] = [$xLabel, $datas[$index]];
            }
        }

        return $formattedDatas ?? [];
    }
}