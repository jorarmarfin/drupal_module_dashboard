<?php

namespace Drupal\drinux_dashboard\Batchprocess;

use Drupal\Core\Database\Database;
use Drupal\drinux_dashboard\Service\ActivityService;
use Drupal\drinux_dashboard\Service\EntityUtilityManager;

class DrinuxDashboardBatchProcess
{
    public static function processBatchActivity($actividad, &$context) {
        // Verifica si es la primera vez que la función se llama para este lote.
        if (!isset($context['sandbox']['initialized'])) {
            // Inicializa los contadores solo la primera vez.
            $context['sandbox']['m'] = 0;
            $context['sandbox']['f'] = 0;
            $context['sandbox']['sn'] = 0;
            $context['sandbox']['initialized'] = TRUE; // Marca el lote como inicializado.
        }
        $nid = (new EntityUtilityManager())->getNidByTitleAndType($actividad, 'grupo');
        $nidGroup = (new ActivityService())->getActividadesByGrupoNid($nid);


        foreach ($nidGroup as $nidActividad) {
            $resumen_por_sexo_por_actividad = (new ActivityService())->getParticipantsByGender($nidActividad);
            $context['sandbox']['m'] += $resumen_por_sexo_por_actividad['Masculino'];
            $context['sandbox']['f'] += $resumen_por_sexo_por_actividad['Femenino'];
            $context['sandbox']['sn'] += $resumen_por_sexo_por_actividad['sn'];
        }

        // Actualiza el mensaje de progreso.
        $context['message'] = t('Processing activity @actividad', ['@actividad' => $actividad]);
        self::updateOrInsertTotalByGender('Masculino', $context['sandbox']['m']);
        self::updateOrInsertTotalByGender('Femenino', $context['sandbox']['f']);
        self::updateOrInsertTotalByGender('Sin especificar', $context['sandbox']['sn']);
    }

    /**
     * @throws \Exception
     */
    public static function finishedBatch($success, $results, $operations): void
    {
        if ($success) {
            $contenidoTabla = self::getTotalByGenderAsArray();
            $message = t('Se ha ingresado las estadísticas. Masculino: @m, Femenino: @f, SN: @sn', [
                '@m' => $contenidoTabla['Masculino'],
                '@f' =>  $contenidoTabla['Femenino'],
                '@sn' => $contenidoTabla['Sin especificar'],
            ]);
        } else {
            $message = t('Finished with an error.');
        }
        \Drupal::messenger()->addMessage($message);
    }

    /**
     * Actualiza o inserta un registro en la tabla total_por_sexo.
     *
     * @param string $nombre
     *   El nombre del registro.
     * @param int $cantidadIncrementar
     *   La cantidad a incrementar.
     */
    public static function updateOrInsertTotalByGender($nombre, $cantidadIncrementar): void
    {
        $connection = Database::getConnection();
        $tabla = 'total_por_sexo';

        // Verifica si existe un registro con el nombre dado.
        $existe = $connection->select($tabla, 't')
            ->fields('t', ['id'])
            ->condition('nombre', $nombre)
            ->execute()
            ->fetchField();

        if ($existe) {
            // Si existe, actualiza el registro incrementando su valor.
            $connection->update($tabla)
                ->expression('cantidad', 'cantidad + :cantidadIncrementar', [':cantidadIncrementar' => $cantidadIncrementar])
                ->condition('nombre', $nombre)
                ->execute();
        } else {
            // Si no existe, inserta un nuevo registro con el nombre y la cantidad dada.
            $connection->insert($tabla)
                ->fields([
                    'nombre' => $nombre,
                    'cantidad' => $cantidadIncrementar,
                ])
                ->execute();
        }
    }
    public static function getTotalByGenderAsArray(): array
    {
        $connection = Database::getConnection();
        $tabla = 'total_por_sexo';

        // Selecciona todos los registros de la tabla.
        $result = $connection->select($tabla, 't')
            ->fields('t') // Selecciona todos los campos de cada registro.
            ->execute()
            ->fetchAll();

        // Convertir los resultados en un array asociativo [nombre => cantidad].
        $contenidos = [];
        foreach ($result as $fila) {
            $contenidos[$fila->nombre] = $fila->cantidad;
        }

        return $contenidos;
    }

}