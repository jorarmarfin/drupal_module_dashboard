<?php

namespace Drupal\drinux_dashboard\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\drinux_dashboard\Service\EntityUtilityManager;
use Drupal\drinux_dashboard\Service\StatisticalCalculations;
use Symfony\Component\HttpFoundation\JsonResponse;


class DashboardController extends ControllerBase
{
    public function content(): array
    {
        return [
            '#theme' => 'dashboard',
            '#attached' => [
                'library' => [
                    'drinux_dashboard/apexcharts',
                ],
            ], '#cache' => ['max-age' => 0],
        ];
    }
    public function jsonContentSector($sector_id): JsonResponse
    {
        return new JsonResponse((new StatisticalCalculations())->countActivityBySector($sector_id));
    }
    public function jsonStatistics($id): JsonResponse
    {
        return match ($id) {
            '1' => new JsonResponse((new StatisticalCalculations())->countAttendanceByGender()),
            '2' => new JsonResponse((new StatisticalCalculations())->countGroupByGender()),
        };
    }

}
