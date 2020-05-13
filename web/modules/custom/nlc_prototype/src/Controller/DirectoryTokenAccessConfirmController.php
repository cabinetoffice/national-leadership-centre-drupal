<?php

namespace Drupal\nlc_prototype\Controller;

use Drupal\Component\Utility\Crypt;
use Drupal\Component\Utility\UserAgent;
use Drupal\Console\Bootstrap\Drupal;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Config\Config;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\TempStore\PrivateTempStore;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Drupal\Core\Url;
use Drupal\user\UserDataInterface;
use Drupal\user\UserInterface;
use Drupal\user\UserStorageInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class DirectoryTokenAccessConfirmController extends ControllerBase {

  /**
   * @var string
   */
  private $routeName = 'view.senior_leaders_directory_es.page';

  /**
   * @var string
   */
  private $routeNameAccessForm = 'nlc_prototype.directory.token_access';

  /**
   * @var PrivateTempStoreFactory
   */
  private $privateTempStoreFactory;

  /**
   * @var PrivateTempStore
   */
  protected $store;

  /**
   * The user storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * The user data service.
   *
   * @var \Drupal\user\UserDataInterface
   */
  protected $userData;

  /**
   * A logger instance.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * @var \Drupal\Core\Extension\ModuleHandler
   */
  protected $moduleHandler;

  /**
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * @var int
   */
  protected $maxSessionCount = 1;

  /**
   * Array of simple HTTP user agents known to do link previewing that may cause issues with single-use login links.
   *
   * @var array
   */
  private $linkPreviewUserAgents = [
    'BingPreview',
    'Slackbot',
  ];

  /**
   * @var string
   */
  private $linkPreviewAgent;

  /**
   * Constructs a UserController object.
   *
   * @param \Drupal\Core\TempStore\PrivateTempStoreFactory $privateTempStoreFactory
   *   A private temporary storage factory.
   * @param \Drupal\user\UserStorageInterface $userStorage
   *   The user storage.
   * @param \Drupal\user\UserDataInterface $userData
   *   The user data service.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Session\AccountProxy $currentUser
   *   The current user.
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Core\Extension\ModuleHandler $moduleHandler
   *   Module handler.
   * @param DateFormatter $dateFormatter
   *   Date formatter.
   */
  public function __construct(PrivateTempStoreFactory $privateTempStoreFactory, UserStorageInterface $userStorage, UserDataInterface $userData, LoggerInterface $logger, AccountProxy $currentUser, Connection $database, ModuleHandler $moduleHandler, DateFormatter $dateFormatter) {
    $this->privateTempStoreFactory = $privateTempStoreFactory;
    $this->store = $this->privateTempStoreFactory->get('directory_token_access_data');
    $this->userStorage = $userStorage;
    $this->userData = $userData;
    $this->logger = $logger;
    $this->currentUser = $currentUser;
    $this->database = $database;
    $this->moduleHandler = $moduleHandler;
    $this->dateFormatter = $dateFormatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('tempstore.private'),
      $container->get('entity.manager')->getStorage('user'),
      $container->get('user.data'),
      $container->get('logger.factory')->get('nlc_prototype'),
      $container->get('current_user'),
      $container->get('database'),
      $container->get('module_handler'),
      $container->get('date.formatter')
    );
  }

  /**
   * Get the current user.
   *
   * @return \Drupal\Core\Session\AccountProxyInterface
   */
  protected function getCurrentUser() {
    return $this->currentUser;
  }

  /**
   * Controller render array, with link back to access request form.
   *
   * @return array
   */
  public function build() {

    $build = [];
    $email = $this->store->get('email');
    $url = Url::fromRoute('nlc_prototype.directory.token_access');
    $build['intro'] = [
      '#type' => 'inline_template',
      '#context' => [
        'email' => $email,
        'url' => $url,
      ],
      '#template' => '<p>{% trans %} We have sent a secure link to {{email}} to log you in. Check your email. {% endtrans %}</p><p>{% trans %}Haven’t received an email?{% endtrans %} <a href="{{url}}">{% trans %}Request access again.{% endtrans %}</a></p>',
    ];

    $this->deleteStore();
    return $build;
  }

  /**
   * Access check for the access form confirm page.
   *
   * @return \Drupal\Core\Access\AccessResult
   */
  public function access() {
    return AccessResult::allowedIf($this->store->get('email'));
  }

  /**
   * Confirm that the user want to use single-use access URL.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   * @param int $uid
   *   User ID of the user requesting reset.
   * @param int $timestamp
   *   The current timestamp.
   * @param string $hash
   *   Login link hash.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse|array
   *   The form render array, or redirect response.
   */
  public function login(Request $request, $uid, $timestamp, $hash) {
    $request = \Drupal::request();

    // The current user is not logged in, so check the parameters.
    $current = \Drupal::time()->getRequestTime();

    $account = $this->getCurrentUser();

    if ($account->isAuthenticated()) {
      $active_sessions = $this->getUserActiveSessionCount($this->getCurrentUser());
      if ($active_sessions > 0) {
        $this->logger
          ->notice(
            'User %name re-used single-use login link at time %timestamp. (User agent: %user_agent; user agent language: %user_agent_language)',
            $this->getOneTimeLoginLogMessageContext($request, $account, $current));
        $this->messenger()->addStatus($this->t('You have just re-used your single-use directory access link.'));
        return $this->redirect($this->routeName);
      }
    }

    $url_options = [
      'absolute' => TRUE,
    ];
    if ($login_destination = $request->query->get('login_destination')) {
      $url_options['query'] = ['destination' => $login_destination];
    }

      // Get the URL to the check route.
    $url = Url::fromRoute('nlc_prototype.directory.token_access.check', [
      'uid' => $uid,
      'timestamp' => $timestamp,
      'hash' => $hash,
    ], $url_options)->toString();

    return [
      '#theme' => 'nlc_prototype_login_page',
      '#check_url' => $url,
    ];
  }

  /**
   * Check that a single-use access URL is for a valid.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   * @param int $uid
   *   User ID of the user requesting reset.
   * @param int $timestamp
   *   The current timestamp.
   * @param string $hash
   *   Login link hash.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   The redirect response.
   * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
   */
  public function check(Request $request, $uid, $timestamp, $hash) {
    // Check if this is a link preview user agent.
    if ($this->checkRequestUserAgentLinkPreview($request)) {
      $this->logger->warning('Access request denied for user (uid: @uid) using %user_agent', ['@uid' => $uid, '%user_agent' => $this->linkPreviewAgent]);
      // Deny access for the BingPreview bot, used by Outlook on links in e-mails.
      throw new AccessDeniedHttpException();
    }

    // The current user is not logged in, so check the parameters.
    $current = \Drupal::time()->getRequestTime();

    $account = $this->getCurrentUser();

    if ($account->isAuthenticated()) {
      $active_sessions = $this->getUserActiveSessionCount($this->getCurrentUser());
      if ($active_sessions > 0) {
        $this->logger
          ->notice(
            'User %name re-used single-use login link at time %timestamp. (User agent: %user_agent; user agent language: %user_agent_language)',
            $this->getOneTimeLoginLogMessageContext($request, $account, $current));
        $this->messenger()->addStatus($this->t('You have just re-used your single-use directory access link.'));
        return $this->redirect($this->routeName);
      }
    }

    /** @var \Drupal\user\UserInterface $user */
    $user = $this->userStorage->load($uid);

    // Verify that the user exists and is active.
    if ($user === NULL || !$user->isActive()) {
      // Blocked or invalid user ID, so deny access. The parameters will be in
      // the watchdog's URL for the administrator to check.
      throw new AccessDeniedHttpException();
    }

    // Time out, in seconds, until login URL expires. 1 day =  86400 seconds.
    $timeout = 86400;
    // No time out for first time login.
    if ($user->getLastLoginTime() && $current - $timestamp > $timeout) {
      $context = array_merge(['%request_timestamp' => $this->dateFormatter->format($timestamp, 'custom', 'r')] , $this->getOneTimeLoginLogMessageContext($request, $user, $current));
      $this->logger
        ->warning(
          'User %name used expired single-use login link at time %timestamp. Link requested at %request_timestamp. (User agent: %user_agent; user agent language: %user_agent_language)',
          $context);
      $this->messenger()->addError($this->t('The secure link you have tried to use is no longer valid. This can happen if you have already used it or if it has expired. Please request a new link by entering your email below.'));
      return $this->redirect($this->routeNameAccessForm);
    }
    elseif ($user->isAuthenticated() && ($timestamp >= $user->getLastLoginTime()) && ($timestamp <= $current) && Crypt::hashEquals($hash, user_pass_rehash($user, $timestamp))) {
      user_login_finalize($user);
      $this->logger
        ->notice(
          'User %name used single-use login link at time %timestamp. (User agent: %user_agent; user agent language: %user_agent_language)',
          $this->getOneTimeLoginLogMessageContext($request, $user, $current));
      $statusMessage = [
        '#type' => 'inline_template',
        '#context' => [
          'first' => $this->t('Welcome. You have used your secure link. This will give you access to the Connect Directory for one month.'),
          'second' => $this->t('You can use this service to find others within the National Leadership Centre Network.'),
        ],
        '#template' => '<p>{{ first }}</p><p>{{ second }}</p>'
      ];
      $this->messenger()->addStatus(render($statusMessage));
      // Let the user's password be changed without the current password
      // check.
      $token = Crypt::randomBytesBase64(55);
      $_SESSION['pass_reset_' . $user->id()] = $token;
      return $this->redirect($this->routeName);
    }

    $this->logger
      ->warning(
        'User %name used expired single-use login link at time %timestamp. (User agent: %user_agent; user agent language: %user_agent_language)',
        $this->getOneTimeLoginLogMessageContext($request, $account, $current));
    $this->messenger()->addError($this->t('The secure link you have tried to use is no longer valid. This can happen if you have already used it or if it has expired. Please request a new link by entering your email below.'));
    return $this->redirect($this->routeNameAccessForm);
  }

  /**
   * Get the number of active sessions for a user.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user to check on.
   *
   * @return int
   *   The total number of active sessions for the given user
   */
  public function getUserActiveSessionCount(AccountInterface $account) {
    $query = $this->database->select('sessions', 's')
      // Use distinct so that HTTP and HTTPS sessions
      // are considered a single sessionId.
      ->distinct()
      ->fields('s', ['sid'])
      ->condition('s.uid', $account->id());

    if ($this->getMasqueradeIgnore()) {
      // Masquerading sessions do not count.
      $like = '%' . $this->database->escapeLike('masquerading') . '%';
      $query->condition('s.session', $like, 'NOT LIKE');
    }

    /** @var \Drupal\Core\Database\Query\Select $query */
    return $query->countQuery()->execute()->fetchField();
  }

  /**
   * @return bool
   *   Should we ignore masqueraded sessions?
   */
  public function getMasqueradeIgnore() {
    $masqueradeModuleExists = $this->moduleHandler->moduleExists('masquerade');
    return $masqueradeModuleExists ? TRUE : FALSE;
  }

  /**
   * Helper method that removes all the keys from the store collection.
   */
  protected function deleteStore() {
    $keys = ['email'];
    foreach ($keys as $key) {
      $this->store->delete($key);
    }
  }

  /**
   * The context array for a log message.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param \Drupal\user\UserInterface|\Drupal\Core\Session\AccountProxyInterface $account
   * @param string|null $timestamp
   *
   * @return array
   */
  protected function getOneTimeLoginLogMessageContext(Request $request, $account, $timestamp = null) {
    $timestamp = isset($timestamp) ? $timestamp : \Drupal::time()->getRequestTime();
    return [
      '%name' => $account->getDisplayName(),
      '%timestamp' => $this->dateFormatter->format($timestamp, 'custom', 'r'),
      '%user_agent' => $this->getRequestUserAgent($request),
      '%user_agent_language' => $this->getRequestLangCode($request),
    ];
  }

  /**
   * Identifying language from the browser Accept-language HTTP header.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return string
   */
  protected function getRequestLangCode(Request $request) {
    $http_accept_language = $request->server->get('HTTP_ACCEPT_LANGUAGE');
    $langCodes = array_keys($this->languageManager()->getLanguages());
    $mappings = \Drupal::config('language.mappings')->get('map');
    $langCode = UserAgent::getBestMatchingLangcode($http_accept_language, $langCodes, $mappings);
    return $langCode;
  }

  /**
   * Identify the user agent from the User-Agent HTTP header.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return mixed
   */
  protected function getRequestUserAgent(Request $request) {
    $http_user_agent = $request->server->get('HTTP_USER_AGENT');
    return $http_user_agent;
  }

  /**
   * Check if the user agent of the request is a from a link preview headless browser.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return bool
   */
  protected function checkRequestUserAgentLinkPreview(Request $request) {
    $linkPreview = false;
    $user_agent = $request->server->get('HTTP_USER_AGENT');;
    foreach ($this->linkPreviewUserAgents as $linkPreviewUserAgent) {
      if (strpos($user_agent,$linkPreviewUserAgent)) {
        $linkPreview = true;
        $this->linkPreviewAgent = $user_agent;
        break;
      }
    }
    return $linkPreview;
  }
}
