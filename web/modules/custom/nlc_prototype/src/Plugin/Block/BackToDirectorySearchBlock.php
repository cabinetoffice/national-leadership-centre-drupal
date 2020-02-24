<?php

namespace Drupal\nlc_prototype\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Provides a link to return to the Directory search.
 *
 * @Block(
 *    id = "back_to_diretory_search_block",
 *    admin_label = @Translation("Back To Directory Search block"),
 * )
 */
class BackToDirectorySearchBlock extends BlockBase {

  /**
   * Create the block content that provides the 'back to search' link.
   *
   * @return array
   */
  public function build() {
    $build = [];

    $url = Url::fromRoute('view.senior_leaders_directory_es.page');
    $back_link = Link::fromTextAndUrl(t('&lt; Back to search results'), $url);
    $link = $back_link->toRenderable();
    $link['#attributes'] = [
      'class' => ['govuk-link'],
      'id' => 'back-to-search-link',
    ];

    $build['back_link'] = [
      'link' => $link,
      '#prefix' => '<p>',
      '#suffix' => '</p>',
      '#attached' => [
          'library' => [
            'nlc_prototype/back_to_search'
          ],
        ],
      ];

    return $build;
  }

}
