<?php
namespace Drupal\drinux_dashboard\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\drinux_dashboard\Service\EntityUtilityManager;

class GenderCalculationForm extends FormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'calculo_sexo_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Realizar Cálculos'),
            '#button_type' => 'primary',
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        \Drupal::database()->truncate('total_por_sexo')->execute();
        $actividades = (new EntityUtilityManager())->getTitlesBySector(641);
        $operations = [];

        foreach ($actividades as $actividad) {
            $operations[] = ['\Drupal\drinux_dashboard\Batchprocess\DrinuxDashboardBatchProcess::processBatchActivity', [$actividad]];
        }
        $batch = [
            'title' => $this->t('Procesando Estadística por Sexo...'),
            'operations' => $operations,
            'finished' => '\Drupal\drinux_dashboard\Batchprocess\DrinuxDashboardBatchProcess::finishedBatch',
        ];

        batch_set($batch);
    }

}
