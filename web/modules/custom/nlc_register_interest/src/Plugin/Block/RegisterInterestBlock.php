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
    if ($account->id() == 0 || $account->id() == 1) {
      // Don't show to anonymous or admin user, uid = 1.
      return AccessResult::forbidden();
    }
    /** @var \Drupal\user\UserInterface $user */
    $user = User::load($account->id());
    // Check if register interest is empty.
    $regEmpty = $user->get('field_register_interest')->value;
    if (!empty($regEmpty)) {
      return AccessResult::forbidden();
    }

    // Check for existing cohort.
    $cohortEmpty = $user->get('field_cohort')->isEmpty();

    return $cohortEmpty ? AccessResult::allowed() : AccessResult::forbidden();

  }
}
