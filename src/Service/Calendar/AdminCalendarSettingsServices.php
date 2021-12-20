<?php

namespace App\Service\Calendar;

use App\Entity\Settings\Configuration;
use App\Entity\Structure\Veterinary;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
class AdminCalendarSettingsServices
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Veterinary $veterinary
     *
     * @return null|array
     */
    public function getCalendarSettings(Veterinary $veterinary): ?array
    {
        $clinicSettings = $this->entityManager->getRepository(Configuration::class)
            ->getClinicCalendarSettings($veterinary->getClinic());

        foreach ($clinicSettings as $settingName => $clinicSetting) {

            if(str_starts_with($settingName, 'days_working_hours')
                && isset($clinicSetting->getDatas()['values'])
                && !empty($clinicSetting->getDatas()['values'])
            ) {

                $hours = explode(' - ', $clinicSetting->getDatas()['values']);
                $settings['days'][] = [
                    'daysOfWeek' =>  [1, 2, 3, 4, 5],
                    'startTime'  =>  '\'' . $hours[0] . '\'',
                    'endTime'    => '\'' . $hours[1] . '\'',
                ];

                $settings['minHour'] = $hours[0];
                $settings['maxHour'] = $hours[1];
            }

            if(str_starts_with($settingName, 'saturday_working_hours')
                && isset($clinicSetting->getDatas()['values'])
                && !empty($clinicSetting->getDatas()['values'])
            ) {

                $hours = explode(' - ', $clinicSetting->getDatas()['values']);

                $settings['days'][] = [
                    'daysOfWeek' =>  [6],
                    'startTime'  =>  '\'' . $hours[0] . '\'',
                    'endTime'    => '\'' . $hours[1] . '\'',
                ];

                $settings['minHour'] = $this->checkAndModifyHour($settings, 'minHour', $hours[0]);
                $settings['maxHour'] = $this->checkAndModifyHour($settings, 'maxHour', $hours[1]);
            }

            if(str_starts_with($settingName, 'sunday_working_hours')
                && isset($clinicSetting->getDatas()['values'])
                && !empty($clinicSetting->getDatas()['values'])
            ) {

                $hours = explode(' - ', $clinicSetting->getDatas()['values']);
                $settings['days'][] = [
                    'daysOfWeek' =>  [0],
                    'startTime'  =>  '\'' . $hours[0] . '\'',
                    'endTime'    => '\'' . $hours[1] . '\'',
                ];

                $settings['minHour'] = $this->checkAndModifyHour($settings, 'minHour', $hours[0]);
                $settings['maxHour'] = $this->checkAndModifyHour($settings, 'maxHour', $hours[1]);
            }
        }

        if (!isset($settings['days'])) {
            $settings['days'] = [
                'daysOfWeek' =>  [ 1, 2, 3 , 4 , 5],
                'startTime'  =>  '\'08:00\'',
                'endTime'    => '\'20:00\'',
            ];
        }

        return $settings ?? null;
    }

    /**
     * @param array|null $settings
     * @param string $typeHour
     * @param $hour
     *
     * @return mixed
     */
    private function checkAndModifyHour(?array $settings, string $typeHour, $hour)
    {
        if (!isset($settings[$typeHour])) {
            return $hour;
        }

        if ($typeHour === 'minHour' && isset($settings[$typeHour]) && $settings[$typeHour] > $hour) {
            return $hour;
        }

        if ($typeHour === 'maxHour' && isset($settings[$typeHour]) && $settings[$typeHour] < $hour) {
            return $hour;
        }

        return $settings[$typeHour];
    }
}