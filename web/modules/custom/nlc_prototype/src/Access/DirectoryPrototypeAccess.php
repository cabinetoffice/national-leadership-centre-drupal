<?php


namespace Drupal\nlc_prototype\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;

class DirectoryPrototypeAccess implements AccessInterface {

  /**
   * @param \Drupal\Core\Session\AccountInterface $account
   *
   * @return \Drupal\Core\Access\AccessResultAllowed
   */
  public function access(AccountInterface $account) {
    \Drupal::messenger()->addMessage($account->id());
    $queryString = \Drupal::request()->query->all();
    \Drupal::messenger()->addMessage(dpr($queryString, true));
    return AccessResult::allowed();
  }
}
