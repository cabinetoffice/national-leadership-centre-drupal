<?php

namespace Drupal\nlc_register_interest\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;

/**
 * Provides a default form.
 */
class RegisterInterestForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'register_interest_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#prefix'] = '<div id="register-interest-wrapper" class="govuk-width-container">';
    $form['#suffix'] = '</div>';
    return array_merge($form, $this->getFormMarkup());
  }

  public function getFormMarkup() {
    $user = User::load(\Drupal::currentUser()->id());
    $reg = $user->get('field_register_interest')->value;

    $form = [
      'markup' => [
        '#theme' => $reg ? 'nlc_register_interest_registered' : 'nlc_register_interest_header',
      ]
    ];

    if ($reg == 0) {
      $form['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('I want to take part in the Programme'),
        '#prefix' => '<p>',
        '#suffix' => '</p>',
        '#ajax' => [
          'callback' => '::ajaxSubmit',
          'disable-refocus' => FALSE, // Or TRUE to prevent re-focusing on the triggering element.
          'wrapper' => 'register-interest-form',
          'progress' => [
            'type' => 'throbber',
            'message' => $this->t('Registering interest...'),
          ],
        ]
      ];
      $form['footer'] = [
        '#theme' => 'nlc_register_interest_footer',
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Register the current user.
    $user = User::load(\Drupal::currentUser()->id());
    $user->set('field_register_interest', TRUE);
    $user->save();
  }

  /**
   * AJAX submit handler. Rebuild the form if it's submitted by AJAX.
   */
  public function ajaxSubmit(array &$form, FormStateInterface $form_state) {
    return $this->getFormMarkup();
  }
}
