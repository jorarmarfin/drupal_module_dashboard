<?php

namespace Drupal\drinux_dashboard\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\drinux_dashboard\Form\DenormalizationForm;

class DashboardAdminController extends ControllerBase
{
    public function content() {
        $form = \Drupal::formBuilder()->getForm(DenormalizationForm::class);
        return $form;
    }

}