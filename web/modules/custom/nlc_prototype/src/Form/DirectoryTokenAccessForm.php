<?php
/**
 * @file DirectoryTokenAccessForm.php
 *
 * Contains \Drupal\nlc_prototype\Form\DirectoryTokenAccessController
 */

namespace Drupal\nlc_prototype\Form;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Render\Markup;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Drupal\Core\Url;
use Drupal\menu_test\Access\AccessCheck;
use Drupal\user\Entity\User;
use Drupal\Core\TempStore\PrivateTempStore;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form class to request access to the members directory via a one-time login token.
 *
 * @package Drupal\nlc_prototype\Form
 */
class DirectoryTokenAccessForm extends FormBase {

  /**
   * @var string
   */
  private $routeName = 'nlc_prototype.directory.token_access.login';

  /**
   * @var PrivateTempStoreFactory
   */
  private $privateTempStoreFactory;

  /** @var  PrivateTempStore */
  protected $store;

  public function __construct(PrivateTempStoreFactory $privateTempStoreFactory) {
    $this->privateTempStoreFactory = $privateTempStoreFactory;
    $this->store = $this->privateTempStoreFactory->get('directory_token_access_data');
  }


  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('tempstore.private')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    return 'directory_access_token';
  }

  /**
   * Access check for form.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   */
  public function access() {
    $account = $this->currentUser();
    if ($account->isAuthenticated()) {
      return AccessResult::forbidden();
    }
    return AccessResult::allowed();
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['intro'] = [
      '#weight' => 0,
      '#type' => 'inline_template',
      '#template' => '<p>{% trans %} {{paragraph_one}} {% endtrans %}</p> <p>{{paragraph_two}}</p>',
      '#context' => [
        'paragraph_one' => 'To access the Senior Leaders\' Network Directory, we will send a secure link to your email address.',
        'paragraph_two' => 'Please use the same email address that is registered with the NLC.',
      ],
    ];

    $form['email'] = array(
      '#weight' => 1,
      '#type' => 'email',
      '#title' => t('Email'),
      '#required' => TRUE,
    );

    $form['actions']['#type'] = 'actions';
    $form['actions']['#weight'] = 2;
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Continue'),
      '#button_type' => 'primary',
    );

    $form['outro'] = [
      '#weight' => 3,
      '#type' => 'inline_template',
      '#template' => '<p class="govuk-!-margin-top-4">{% trans %} {{paragraph}} {% endtrans %}</p>',
      '#context' => [
        'paragraph' => 'The secure link keeps you logged in for a month and once sent it will be valid for 24 hours. After this you will have to request a new link.',
      ],
    ];

    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\user\Entity\User $account */
    $account = user_load_by_mail($form_state->getValue('email'));
    if (empty($account)) {
      $form_state->setErrorByName('email', $this->t('Check your email address.'));
      $email = 'nlc@cabinetoffice.gov.uk';
      $url = Url::fromUri('mailto:' . $email);
      $link = Link::fromTextAndUrl($email, $url);
      $form_state->setError($form, $this->t('Your email address does not currently have access to the directory. Please check your email address is correct. If it is, please contact @email for more information.', ['@email' => $link->toString()]));
    }
  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\user\Entity\User $account */
    $account = user_load_by_mail($form_state->getValue('email'));
    $this->store->set('email', $account->getEmail());
    /** @var \Drupal\Core\Mail\MailManager $mailManager */
    $mailManager = \Drupal::service('plugin.manager.mail');
    $module = 'nlc_prototype';
    $key = 'directory_access_token';
    $to = $account->getEmail();
    $params = [
      'message' => [
        $this->t('Thank you for requesting a link to your senior leadership directory, provided by the National Leadership Centre.'),
        $this->t('Your single-use secure link is: @url', ['@url' => $this->directoryUrl($account)]),
        $this->t('This link will expire after one day.'),
        $this->t('If you have any difficulties, please contact NLC@CabinetOffice.gov.uk for help.'),
      ],
    ];
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $send = true;

    $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
    if ($result['result'] !== true) {
      $this->messenger()->addError($this->t('There was a problem sending your message and it was not sent.'));
    }
//
//    $rendered_message = Markup::create('You will receive a secure link to your email address.');
//
//    $this->messenger()->addStatus($rendered_message);
    $form_state->setRedirect('nlc_prototype.directory.token_access.confirm');

  }

  /**
   * Create a directory URL with one-time access hash parameters.
   *
   * @param \Drupal\user\Entity\User $account
   * @param array $options
   *
   * @return \Drupal\Core\GeneratedUrl|string
   */
  private function directoryUrl(User $account, $options = array()) {
    $timestamp = \Drupal::time()->getRequestTime();
    $langCode = isset($options['langcode']) ? $options['langcode'] : $account
      ->getPreferredLangcode();

    return Url::fromRoute($this->routeName, [
      'uid' => $account
        ->id(),
      'timestamp' => $timestamp,
      'hash' => user_pass_rehash($account, $timestamp),
    ], [
      'absolute' => TRUE,
      'language' => \Drupal::languageManager()
        ->getLanguage($langCode),
    ])
      ->toString();
  }

}
