<?php
namespace Drupal\drinux_dashboard\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\drinux_dashboard\Service\ActivityService;
use Drupal\drinux_dashboard\Service\EntityUtilityManager;

class DenormalizationForm extends FormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'denormalization_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Des normalizar'),
            '#button_type' => 'primary',
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        \Drupal::database()->truncate('asistencia')->execute();
        $terms = (new EntityUtilityManager())->getListTaxonomy('g');
        $operations = [];
        foreach ($terms as $key1=> $term) {
            $grupos = (new EntityUtilityManager())->getTitlesBySector($key1);
            foreach ($grupos as $key2=> $grupo) {
                $nidGroup = (new ActivityService())->getActividadesByGrupoNid($key2);
                $operations[] = ['\Drupal\drinux_dashboard\Batchprocess\DenormalizationBatchProcess::processBatchActivity', [$key2,$term,$grupo]];
            }
        }
        $batch = [
            'title' => $this->t('Desnormalizando...'),
            'operations' => $operations,
            'finished' => '\Drupal\drinux_dashboard\Batchprocess\DenormalizationBatchProcess::finishedBatch',
        ];

        batch_set($batch);
    }

}
