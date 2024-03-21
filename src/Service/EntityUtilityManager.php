<?php

namespace Drupal\drinux_dashboard\Service;

use Drupal\Core\TypedData\Exception\MissingDataException;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

class EntityUtilityManager
{
    function getNidByTitleAndType($title, $type) {
        $query = \Drupal::entityTypeManager()->getStorage('node')->getQuery();
        $nids = $query->condition('type', $type)
            ->condition('title', $title)
            ->accessCheck(TRUE)
            ->execute();
        // Esto devolverá un array de NIDs. Si solo esperas uno, puedes tomar el primero.
        $nid = !empty($nids) ? reset($nids) : NULL;
        return $nid;
    }
    function getTitlesBySector($sectorId): array
    {
        $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties([
            'type' => 'grupo',
            'field_sector' => $sectorId,
        ]);

        $titles = [];
        foreach ($nodes as $key=> $node) {
            $titles[$key] = $node->getTitle();
        }

        return $titles;
    }
    function getCountByField($nid,$field): int
    {
        $node = Node::load($nid);
        return $node->get($field)->count();
    }
    function getFieldByNid($nid,$field)
    {
        $node = Node::load($nid);
        return ($field=='title')? $node->getTitle() :$node->get($field)->getValue()[0]['value'];
    }
    function getTaxonomyTermById($termId)
    {
        $term = Term::load($termId);
        return ($term)?$term->getName():NULL;
    }
    function getListTaxonomy($taxonomy): array
    {
        $listado = [];

        // Cargar los términos de la taxonomía 'sector'.
        $terminos = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($taxonomy, 0, NULL, TRUE);

        // Iterar sobre cada término y añadirlo al listado.
        foreach ($terminos as $termino) {
            if ($termino instanceof Term) {
                $listado[$termino->id()] = $termino->getName();
            }
        }

        return $listado;
    }

    /**
     * Obtiene el valor de un campo de una entidad referenciada por un nodo.
     *
     * @param int $nid NID del nodo desde el cual se obtiene la referencia.
     * @param String $referenceField campo de referencia
     * @param String $field campo de la entidad referenciada
     * @return mixed El valor del campo de la entidad referenciada, o NULL si no se encuentra.
     * @throws MissingDataException
     */
    function getValueFromReferencedEntityField(int $nid, String $referenceField,String $field): mixed
    {
        // Cargar el nodo.
        $node = Node::load($nid);
        if (!$node) {
            return NULL; // Nodo no encontrado.
        }

        // Obtener el valor del campo de referencia para cargar la entidad referenciada.
        $referencia = $node->get($referenceField)->first();
        if (!$referencia) {
            return NULL; // Referencia no encontrada.
        }

        // Cargar la entidad referenciada.
        $entidadReferenciada = $referencia->get('entity')->getTarget()->getValue();
        if (!$entidadReferenciada) {
            return NULL; // Entidad referenciada no encontrada.
        }

        // Obtener el valor del campo especificado de la entidad referenciada.
        return $entidadReferenciada->get($field)->value;
    }
}