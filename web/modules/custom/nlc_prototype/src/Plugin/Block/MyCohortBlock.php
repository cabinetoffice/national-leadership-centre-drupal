<?php

namespace Drupal\nlc_prototype\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\TypedData\Exception\MissingDataException;
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

    $roles = $accountProxy->getRoles();
    if ($accountProxy->isAuthenticated() && $accountProxy->id() !== 1 && count($roles) == 1) {
      // Get User entity object for the current user.
      /** @var \Drupal\user\UserInterface $account */
      $account = User::load($accountProxy->id());
      if ($account->get('field_cohort')->count() > 0) {
        try {
          $cohorts = $account->get('field_cohort');
          $options = [];
          // The account user may be in more than one cohort. Get the last one, assuming it's the most recent.
          $index = count($cohorts->getValue()) - 1;
          $cohort = $cohorts->get($index);
          $entity = \Drupal::entityTypeManager()
            ->getStorage('node')
            ->load($cohort->target_id);
          $options['query']['directory'][] = 'cohort:' . $cohort->target_id;
          if (!empty($options)) {
            $build['cohort'] = [
              '#type' => 'container',
              '#attributes' => [
                'class' => ['govuk-inset-text'],
              ],
              '#cache' => [
                'tags' => ['directory_view:user:' . $account->id()]
              ],
              'cohort_info' => [
                '#prefix' => '<p>',
                '#suffix' => '</p>',
                'cohort_intro' => [
                  '#markup' => $this->t('You are registered for @cohort.', ['@cohort' => $entity->label()]),
                  '#suffix' => ' ',
                ],
                'cohort_link' => [
                  '#type' => 'link',
                  '#title' => $this->t('View your fellow cohort delegates'),
                  '#url' => \Drupal\Core\Url::fromRoute('view.directory.page_1', [], $options),
                ],
              ],
            ];
          }
        }
        catch (MissingDataException $e) {
          // Do nothing?
        }
      }
    }

    return $build;
  }

}
