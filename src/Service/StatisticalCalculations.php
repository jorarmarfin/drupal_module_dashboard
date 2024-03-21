<?php

namespace Drupal\drinux_dashboard\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Database;

class StatisticalCalculations
{
    protected Connection $connection;

    public function __construct() {
        $this->connection = Database::getConnection();
    }
    function countActivityBySector($sector_id): array
    {
        $sector = (new EntityUtilityManager())->getTaxonomyTermById($sector_id);
        $query = $this->connection->select('asistencia', 'a');
        $query->condition('sector', $sector);
        $query->addField('a', 'grupo', 'actividad');
        $query->addExpression('COUNT(*)', 'cantidad');
        $query->groupBy('grupo');
        $query->orderBy('grupo', 'ASC');

        // Ejecuta la consulta y obtén los resultados.
        return $resultados = $query->execute()->fetchAll();
    }
    function countAttendanceByGender(): array
    {
        // Define la consulta con agrupación y ordenación.
        $query = $this->connection->select('asistencia', 'a');
        $query->addField('a', 'sexo', 'sexo');
        $query->addExpression('COUNT(*)', 'cantidad');
        $query->groupBy('sexo');
        $query->orderBy('sexo', 'DESC');

        // Ejecuta la consulta y obtén los resultados.
        $resultados = $query->execute()->fetchAll();

        // Prepara y devuelve los resultados.
        $datos = [];
        foreach ($resultados as $resultado) {
            $datos[] = [
                'sexo' => $resultado->sexo,
                'cantidad' => $resultado->cantidad,
            ];
        }

        return $datos;
    }
    function countGroupByGender():array
    {
        $query = $this->connection->select('asistencia', 'a');
        $query->addField('a', 'grupo', 'grupo');
        $query->addField('a', 'sexo', 'sexo');
        $query->addExpression('COUNT(*)', 'cantidad');
        $query->groupBy('grupo');
        $query->groupBy('sexo');
        $query->orderBy('grupo', 'ASC');
        $query->orderBy('sexo', 'ASC');

        // Ejecuta la consulta y obtén los resultados.
        return $resultados = $query->execute()->fetchAll();
    }

}