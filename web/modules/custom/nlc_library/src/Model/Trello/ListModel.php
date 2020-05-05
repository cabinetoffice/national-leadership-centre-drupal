<?php


namespace Drupal\nlc_library\Model\Trello;

class ListModel extends AbstractTrelloModel {

  protected $excludeIds = ['5e989536131c9f10c0d29f5e'];

  public function __construct($object) {
    parent::__construct($object);
  }

  /**
   * {@inheritDoc}
   */
  public function modelType(): string {
    return 'list';
  }

}
