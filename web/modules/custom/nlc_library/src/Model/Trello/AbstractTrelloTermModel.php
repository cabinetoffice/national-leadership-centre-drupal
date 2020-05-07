<?php


namespace Drupal\nlc_library\Model\Trello;

use Drupal\nlc_library\Model\AbstractModel;

/**
 * Class AbstractTrelloTermModel
 *
 * @package Drupal\nlc_library\Model\Trello
 */
abstract class AbstractTrelloTermModel extends AbstractTrelloModel implements TrelloTermModelInterface {

  /**
   * @var string[]
   */
  protected $properties = [];

  /**
   * AbstractTrelloTermModel constructor.
   *
   * @param \stdClass $object
   *
   * @throws \Drupal\nlc_library\Model\Trello\TrelloTermModelException
   */
  public function __construct($object) {
    if (empty($this->getProperties())) {
      throw new TrelloTermModelException(sprintf('Missing required parameters for this %s model object', get_class($this)));
    }
    parent::__construct($object);
  }
}
