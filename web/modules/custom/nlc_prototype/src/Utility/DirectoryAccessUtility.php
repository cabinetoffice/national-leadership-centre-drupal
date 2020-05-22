<?php


namespace Drupal\nlc_prototype\Utility;

use Drupal\Core\Url;
use Drupal\user\UserInterface;

class DirectoryAccessUtility {

  /**
   * @var string
   */
  const ROUTE_NAME = 'nlc_prototype.directory.token_access.login';

  /**
   * Create a directory URL with one-time access hash parameters.
   *
   * @param \Drupal\user\UserInterface $account
   * @param string $routeName
   * @param array $params
   *
   * @return \Drupal\Core\GeneratedUrl|string
   */
  public static function directoryUrl(UserInterface $account, $routeName = null, $params = array()) {
    $routeName = $routeName ?? self::ROUTE_NAME;
    $timestamp = \Drupal::time()->getRequestTime();
    $langCode = isset($options['langcode']) ? $options['langcode'] : $account
      ->getPreferredLangcode();
    $options = [
      'absolute' => TRUE,
      'language' => \Drupal::languageManager()
        ->getLanguage($langCode),
    ];
    if (!empty($params['login_destination'])) {
      $options['query'] = ['login_destination' => $params['login_destination']];
    }

    return Url::fromRoute($routeName, [
      'uid' => $account
        ->id(),
      'timestamp' => $timestamp,
      'hash' => user_pass_rehash($account, $timestamp),
    ],
      $options)
      ->toString();
  }

}
