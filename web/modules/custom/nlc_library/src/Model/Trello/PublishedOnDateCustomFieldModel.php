<?php


namespace Drupal\nlc_library\Model\Trello;

class PublishedOnDateCustomFieldModel extends AbstractCustomFieldModel {

  public function __construct($object) {
    parent::__construct($object);
    $this->value = $object->value;
  }

  /**
   * {@inheritDoc}
   */
  public function modelType(): string {
    return 'published_on_date';
  }

  /**
   * @return string
   */
  public function connectFieldName(): string {
    return 'field_read_time';
  }

}
