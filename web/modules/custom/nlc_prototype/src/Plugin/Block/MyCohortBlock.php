<?php


namespace Drupal\nlc_prototype\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\user\Entity\User;

/**
 * Provides a link to view 'my cohort' in the Directory.
 *
 * @Block(
 *    id = "my_cohort_block",
 *    admin_label = @Translation("My Cohort block"),
 * )
 */
class MyCohortBlock extends BlockBase {

  /**
   * Create the block content that provides a link to the account user's cohort(s).
   *
   * @return array
   */
  public function build() {
    $build = [];
    // Current user.
    $accountProxy = \Drupal::currentUser();

     // Get User entity object for this account profile page.
     /** @var \Drupal\user\Entity\User $account */
    $account = \Drupal::routeMatch()->getParameter('user');
    $roles = $account->getRoles();
    if ($accountProxy->isAuthenticated() && $accountProxy->id() !== 1 && count($roles) == 1 && $account->id() == $accountProxy->id()) {
      /** @var \Drupal\user\Entity\User $account */
      if ($cohorts = $account->get('field_cohort')) {
        $options = [];
        // The account user may be in more than one cohort.
        foreach ($cohorts as $cohort) {
          $options['query']['directory'][] = 'cohort:' . $cohort->getValue()['target_id'];
        }
        if (!empty($options)) {
          $build['cohort_link'] = [
            '#type' => 'link',
            '#title' => $this->t('See who is in my cohort'),
            '#url' => \Drupal\Core\Url::fromRoute('view.directory.page_1', [], $options),
            '#cache' => [
              'keys' => ['entity_view', 'user', $account->id()]
            ],
          ];
        }
      }
    }

    return $build;
  }

}
