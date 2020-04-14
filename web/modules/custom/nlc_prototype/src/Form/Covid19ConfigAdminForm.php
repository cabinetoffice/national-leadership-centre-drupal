<?php


namespace Drupal\nlc_prototype\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class Covid19ConfigAdminForm extends ConfigFormBase {

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    return 'connect_covid19_admin_form';
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Form constructor.
    $form = parent::buildForm($form, $form_state);
    // Default settings.
    $config = $this->config('nlc_prototype.config_covid19.settings');

    $form['covid19_survey_url'] = [
      '#type' => 'url',
      '#title' => $this->t('COVID-19 survey URL'),
      '#size' => 64,
      '#default_value' => $config->get('covid19.survey_url'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('nlc_prototype.config_covid19.settings');
    $config->set('covid19.survey_url', $form_state->getValue('covid19_survey_url'));
    $config->save();
    return parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'nlc_prototype.config_covid19.settings',
    ];
  }

}
