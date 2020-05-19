<?php

namespace Drupal\nlc_library\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class LibraryConfigForm extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'nlc_library.config';

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    return 'nlc_library_config';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);

    $intro_copy = $config->get('intro_copy');

    $form['intro_copy'] = [
      '#type' => 'text_format',
      '#label' => $this->t('Library introduction copy'),
      '#default_value' => $intro_copy['value'],
      '#format' => $intro_copy['format'],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration.
    $this->configFactory->getEditable(static::SETTINGS)
      ->set('intro_copy', $form_state->getValue('intro_copy'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
