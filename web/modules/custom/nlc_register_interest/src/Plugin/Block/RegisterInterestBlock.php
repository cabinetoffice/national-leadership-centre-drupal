<?php

namespace Drupal\nlc_register_interest\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\user\Entity\User;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Provides a link to view 'my cohort' in the Directory.
 *
 * @Block(
 *    id = "register_interest_block",
 *    admin_label = @Translation("Register Interest block"),
 * )
 */
class RegisterInterestBlock extends BlockBase {

  /**
   * Create the block content that allows a user to register interest in a cohort.
   *
   * @return array
   */
  public function build() {
    $build = \Drupal::formBuilder()->getForm('Drupal\nlc_register_interest\Form\RegisterInterestForm');
    $build['#attributes']['class'][] = 'container--negative';
    $build['#attached']['library'][] = 'nlc_register_interest/nlc_register_interest';
    return $build;
  }

  /**
   * Control access to this block
   */
  protected function blockAccess(AccountInterface $account) {
    if ($account->id() == 0) {
      // Don't show to anonymous.
      return AccessResult::forbidden();
    }
    // Check for existing cohort.
    $user = User::load($account->id());
    $cohortEmpty = $user->get('field_cohort')->isEmpty();

    return $cohortEmpty ? AccessResult::allowed() : AccessResult::forbidden();

  }
}
