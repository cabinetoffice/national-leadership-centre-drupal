<?php

namespace Drupal\nlc_prototype\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;

/**
 * Provides intro text for the Directory.
 *
 * @Block(
 *    id = "directory_intro_block",
 *    admin_label = @Translation("Directory intro block"),
 * )
 */
class DirectoryIntroBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' => '<p class="govuk-body-l">' . $this->t('You can use this service to find others within the National Leadership Centre Network.') . '</p>',
    ];
  }

}