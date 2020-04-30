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
      '#template' => '<p>{{paragraph_one}}</p>',
      '#context' => [
        'paragraph_one' => $this->t('We will send a secure access link to your work email account.'),
      ],
    ];

    $form['email'] = array(
      '#weight' => 1,
      '#type' => 'email',
      '#title' => t('Email'),
      '#attributes' => array(
        'autocomplete' => 'email',
        'class' => array('govuk-input--width-30')
      ),
      '#required' => TRUE,
    );

    $form['outro'] = [
      '#weight' => 2,
      '#type' => 'inline_template',
      '#template' => '<p class="govuk-!-margin-top-4">{{paragraph}}</p>',
      '#context' => [
        'paragraph' => $this->t('This link will only be valid for 24 hours. If you don’t use the link in that time, you can come back to this page and enter your email address again to request a new access link.'),
      ],
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['#weight'] = 3;
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Continue'),
      '#button_type' => 'primary',
    );

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
      $email = 'NLC@CabinetOffice.gov.uk';
      $mailUrl = Url::fromUri('mailto:' . $email);
      $mailLink = Link::fromTextAndUrl($email, $mailUrl);
      $networkUrl = Url::fromUri('https://www.nationalleadership.gov.uk/the-network/');
      $networkLink = Link::fromTextAndUrl($this->t('visit our Network page'), $networkUrl);
      $networkLink = $networkLink->toRenderable();
      $networkLink['#attributes'] = [
        'target' => '_blank',
      ];
      $formError = [
        '#type' => 'inline_template',
        '#context' => [
          'first' => $this->t('Please check that you have used the correct email address.'),
          'second' => $this->t('In a few cases we may not have you on our records yet, please email us at @email with your name, role and organisation and we will be happy to register you.', ['@email' => $mailLink->toString()]),
          'third' => $this->t('To check if you are eligible to join our Network, please @network_page.', ['@network_page' => render($networkLink)]),
        ],
        '#template' => '<p>{{ first }}</p><p>{{ second }}</p><p>{{ third }}</p>',
      ];
      $form_state->setError($form, render($formError));
      $message = $this->t('Login failed: Unknown address @email', ['@email' => $form_state->getValue('email')]);
      \Drupal::logger('nlc_prototype')->error($message);
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
    $email = 'NLC@CabinetOffice.gov.uk';
    $mailUrl = Url::fromUri('mailto:' . $email);
    $mailLink = Link::fromTextAndUrl($email, $mailUrl);
    $message = [
      '#theme' => 'nlc_connect_access_email_body',
      '#pre' => [
        '#type' => 'inline_template',
        '#context' => [
          'first' => $this->t('Thank you for requesting a secure link to the Network of Senior Leaders: the Connect Directory, provided by the National Leadership Centre.')
        ],
        '#template' => '<p>{{ first }}</p>',
      ],
      '#link' => [
        '#type' => 'link',
        '#title' => $this->t('Log into the Connect Directory'),
        '#url' => Url::fromUri($this->directoryUrl($account)),
        '#attributes' => [
          'class' => ['button'],
        ],
      ],
      '#post' => [
        '#type' => 'inline_template',
        '#context' => [
          'first' => $this->t('The secure link will expire after 24 hours.'),
          'second' => $this->t('You can use this directory to connect and work with people across the public sector.'),
          'third' => $this->t('If you have any difficulties, please contact @email for help.', ['@email' => $mailLink->toString()]),
        ],
        '#template' => '<p>{{ first }}</p><p>{{ second }}</p><p>{{ third }}</p>',
      ],
    ];
    $params['message'] = render($message);
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $send = true;

    $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
    if ($result['result'] !== true) {
      $email = 'NLC@cabinetoffice.gov.uk';
      $mailUrl = Url::fromUri('mailto:' . $email);
      $mailLink = Link::fromTextAndUrl($email, $mailUrl);
      $message = [
        '#type' => 'inline_template',
        '#context' => [
          'first' => $this->t('There was an unexpected error sending your secure link. Please request a new link by entering your work email below.'),
          'second' => $this->t('If this continues, please contact @email for more information.', ['@email' => $mailLink->toString()]),
        ],
        '#template' => '<p>{{ first }}</p><p>{{ second }}</p>'
      ];
      // Remove the default error message set by core mail manager.
      $this->messenger()->deleteByType('error');
      // Add our own mail sending error message.
      $this->messenger()->addError(render($message));
    }
    else {
      $message = $this->t('Login link requested and email sent to @email', ['@email' => $form_state->getValue('email')]);
      \Drupal::logger('nlc_prototype')->notice($message);
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
