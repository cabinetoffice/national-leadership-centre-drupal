<?php

namespace Drupal\nlc_prototype\Controller;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\TempStore\PrivateTempStore;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Drupal\Core\Url;
use Drupal\user\UserDataInterface;
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
  private $routeName = 'view.directory.page_1';

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
   */
  public function __construct(PrivateTempStoreFactory $privateTempStoreFactory, UserStorageInterface $userStorage, UserDataInterface $userData, LoggerInterface $logger) {
    $this->privateTempStoreFactory = $privateTempStoreFactory;
    $this->store = $this->privateTempStoreFactory->get('directory_token_access_data');
    $this->userStorage = $userStorage;
    $this->userData = $userData;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('tempstore.private'),
      $container->get('entity.manager')->getStorage('user'),
      $container->get('user.data'),
      $container->get('logger.factory')->get('user')
    );
  }

  /**
   * Controller render array, with link back to access request form.
   *
   * @return array
   */
  public function build() {

    $build = [];
    $format = filter_default_format();
    $build['intro'] = [
      '#type' => 'processed_text',
      '#text' => $this->t('You will receive a secure link to your email address.'),
      '#format' => $format,
    ];

    $url = Url::fromRoute('nlc_prototype.directory.token_access');
    $build['form_link'] = [
      '#type' => 'link',
      '#title' => $this->t('Go to form'),
      '#url' => $url,
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
   * Check that a one-time access URL is for a valid.
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
   */
  public function check(Request $request, $uid, $timestamp, $hash) {
    // The current user is not logged in, so check the parameters.
    $current = REQUEST_TIME;
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
      $this->messenger()->addError($this->t('You have tried to use a one-time login link that has expired. Please request a new one using the form below.'));
      return $this->redirect($this->routeNameAccessForm);
    }
    elseif ($user->isAuthenticated() && ($timestamp >= $user->getLastLoginTime()) && ($timestamp <= $current) && Crypt::hashEquals($hash, user_pass_rehash($user, $timestamp))) {
      user_login_finalize($user);
      $this->logger->notice('User %name used one-time login link at time %timestamp.', ['%name' => $user->getDisplayName(), '%timestamp' => $timestamp]);
      $this->messenger()->addStatus($this->t('You have just used your one-time directory access link.'));
      // Let the user's password be changed without the current password
      // check.
      $token = Crypt::randomBytesBase64(55);
      $_SESSION['pass_reset_' . $user->id()] = $token;
      return $this->redirect($this->routeName);
    }

    $this->messenger()->addError($this->t('You have tried to use a one-time directory access link that has either been used or is no longer valid. Please request a new one using the form below.'));
    return $this->redirect($this->routeNameAccessForm);
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
}
