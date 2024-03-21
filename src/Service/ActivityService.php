<?php

namespace Drupal\drinux_dashboard\Service;

use Drupal\node\Entity\Node;

class ActivityService
{

    function getActividadesByGrupoNid(int $grupoNid): array
    {
        $query = \Drupal::entityTypeManager()->getStorage('node')->getQuery();
        $nids = $query->condition('type', 'actividad')
            ->condition('field_grupo', $grupoNid)
            ->accessCheck(TRUE) // Asegúrate de aplicar controles de acceso.
            ->execute();
        // Esto devolverá un array de NIDs de todas las actividades asociadas al grupo especificado.
        return array_values($nids);
    }
    /**
     * Obtiene los participantes de una actividad agrupados por sexo.
     *
     * @param int $activityNid El ID del nodo de la actividad.
     * @return array Un arreglo de participantes agrupados por sexo.
     */
    public function getParticipantsByGender(int $activityNid): array
    {
        $actividad = Node::load($activityNid);
        // Verificar si el nodo tiene participantes y si el campo participante existe.
        if ($actividad && $actividad->hasField('field_participante') && !$actividad->get('field_participante')->isEmpty()) {
            $participantesIds = $actividad->get('field_participante')->getValue();
            $m = 0; $f = 0; $sn = 0;
            foreach ($participantesIds as $participanteId) {
                $participante = Node::load($participanteId['target_id']);
                if ($participante && $participante->hasField('field_sexo')) {
                    $sexo = $participante->get('field_sexo')->value;
                    // Asegúrate de que el sexo obtenido sea uno de los esperados antes de añadirlo.
                    switch ($sexo) {
                        case 'Masculino':
                            $m++;
                            break;
                        case 'Femenino':
                            $f++;
                            break;
                        default:
                            $sn++;
                            break;
                    }
                }
            }
            return [
                'Masculino' => $m,
                'Femenino' => $f,
                'sn' => $sn,
            ];
        }
        // Devolver un arreglo vacío o manejar el caso de error como prefieras.
        return [];
    }
    public function getParticipants(int $activityNid): array {
        $actividad = Node::load($activityNid);
        $participantIds = [];
        // Verificar si el nodo tiene participantes y si el campo participante existe.
        if ($actividad && $actividad->hasField('field_participante') && !$actividad->get('field_participante')->isEmpty()) {
            $participants = $actividad->get('field_participante')->getValue();
            foreach ($participants as $participant) {
                $participantIds[] = $participant['target_id'];
            }
        }
        return $participantIds;
    }



}