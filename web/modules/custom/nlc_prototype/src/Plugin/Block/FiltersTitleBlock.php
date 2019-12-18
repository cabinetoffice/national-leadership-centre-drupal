<?php

namespace Drupal\nlc_prototype\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Filters' title block.
 *
 * @Block(
 *   id = "filters_title_block",
 *   admin_label = @Translation("Filters title")
 * )
 */
class FiltersTitleBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' => '<h2 class="govuk-heading-m">' . $this->t('Filters') . '</h2>',
    ];
  }

}