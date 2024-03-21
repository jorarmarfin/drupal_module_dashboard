<?php

namespace Drupal\drinux_dashboard\Batchprocess;

use Drupal\Core\Database\Database;
use Drupal\Core\Database\Log;
use Drupal\Core\TypedData\Exception\MissingDataException;
use Drupal\drinux_dashboard\Service\ActivityService;
use Drupal\drinux_dashboard\Service\EntityUtilityManager;

class DenormalizationBatchProcess
{
    /**
     * @throws MissingDataException
     */
    public static function processBatchActivity($nid, $sector, $grupo, &$context): void
    {
        $nidGroup = (new ActivityService())->getActividadesByGrupoNid($nid);
        foreach ($nidGroup as $nidActividad) {
            $actividad = (new EntityUtilityManager())->getFieldByNid($nidActividad, 'title');
            $participantes = (new ActivityService())->getParticipants($nidActividad);
            foreach ($participantes as $participante) {
                $sexo = (new EntityUtilityManager())->getFieldByNid($participante,'field_sexo');
                $nombre_participante = (new EntityUtilityManager())->getFieldByNid($participante,'title');
                $distrito = (new EntityUtilityManager())->getValueFromReferencedEntityField(intval($participante),'field_distrito','name');
                self::insertAttendance($sector,$grupo,$actividad,$nombre_participante,$sexo,$distrito);
            }
        }
    }

    /**
     * @throws \Exception
     */
    public static function finishedBatch($success, $results, $operations): void
    {
        if ($success) {
            $message = t('Se des normalizo la entidad ');
        } else {
            $message = t('Finished with an error.');
        }
        \Drupal::messenger()->addMessage($message);
    }


    public static function insertAttendance($sector,$grupo,$actividad,$participante,$sexo,$distrito ): void
    {
        $connection = Database::getConnection();
        $tabla = 'asistencia';

        // Si no existe, inserta un nuevo registro con el nombre y la cantidad dada.
        $connection->insert($tabla)
            ->fields([
                'sector' => $sector,
                'grupo' => $grupo,
                'actividad' => $actividad,
                'participante' => $participante,
                'sexo' => $sexo,
                'distrito' => $distrito,

            ])
            ->execute();

    }
}