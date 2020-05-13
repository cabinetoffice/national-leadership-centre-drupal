<?php

namespace Drupal\nlc_library\Model\Trello;

class LabelModel extends AbstractTrelloTermModel{

  protected $properties = [
    'name' => 'getName',
    'field_trello_id' => 'getId',
    'field_trello_colour' => 'getColour',
  ];

  /**
   * @var string
   */
  private $colour;

  public function __construct($object) {
    parent::__construct($object);
    $this->colour = $object->color;
  }

  /**
   * {@inheritDoc}
   */
  public function modelType(): string {
    return 'label';
  }

  /**
   * {@inheritDoc}
   */
  public function vocabulary(): string {
    return 'library_labels';
  }

  /**
   * {@inheritDoc}
   */
  public function getProperties(): array {
    return $this->properties;
  }

  /**
   * @return string
   */
  public function getColour(): string {
    return $this->colour;
  }

}
