<?php

namespace Drupal\nlc_library\Model\Trello;

use Drupal\nlc_library\Model\ModelInterface;

interface TrelloTermModelInterface extends ModelInterface {

  /**
   * An array of field properties for creating a taxonomy term of this type in Connect.
   *
   * @return array
   */
  public function getProperties(): array ;

  /**
   * The Connect vocabulary machine name for this Trello term.
   *
   * @return string
   */
  public function vocabulary(): string ;

}
