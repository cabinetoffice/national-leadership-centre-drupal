<?php

namespace Drupal\nlc_library\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Intro copy for the NLC library.
 *
 * @Block(
 *    id = "library_intro_block",
 *    admin_label = @Translation("Library intro copy block"),
 * )
 *
 * @package Drupal\nlc_library\Plugin\Block
 */
class LibraryIntroCopyBlock extends BlockBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'nlc_library.config';

  public function build() {
    $config = \Drupal::configFactory()->get(static::SETTINGS);
    $intro_copy = $config->get('intro_copy');

    $build = [];

    $build['intro_copy'] = [
      '#type' => 'processed_text',
      '#text' => $intro_copy['value'],
      '#format' => $intro_copy['format'],
    ];

    return $build;
  }

}
