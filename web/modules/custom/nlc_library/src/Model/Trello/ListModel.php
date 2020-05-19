<?php


namespace Drupal\nlc_library\Model\Trello;

class ListModel extends AbstractTrelloTermModel {

  /**
   * @var string[]
   */
  protected $properties = [
    'name' => 'getName',
    'field_trello_id' => 'getId',
  ];

  protected $excludeIds = ['5e989536131c9f10c0d29f5e'];

  public function __construct($object) {
    parent::__construct($object);
    $this->name = $this->topicTransformCase($object->name);
  }

  private function topicTransformCase($text) {
    $words = explode(' ', $text);
    foreach($words as $k => $word){
      if(strtoupper($word) !== $word) {
        $words[$k] = strtolower($word);
      }
    }
    return implode(' ', $words);
  }

  /**
   * {@inheritDoc}
   */
  public function modelType(): string {
    return 'list';
  }

  /**
   * {@inheritDoc}
   */
  public function vocabulary(): string {
   return 'topics';
  }

  /**
   * {@inheritDoc}
   */
  public function getProperties(): array {
    return $this->properties;
  }

}
