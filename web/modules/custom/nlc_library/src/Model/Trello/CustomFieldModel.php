<?php


namespace Drupal\nlc_library\Model\Trello;

class CustomFieldModel extends AbstractTrelloModel {


  const CONNECT_FIELD_NAMES = [
    '5ebd0ff3becc79747d808227' => 'field_published_on',
    '5ebd101a6c1d485d83449d27' => 'field_read_time',
  ];

  /**
   * @var string
   */
  private $type;

  /**
   * @var string
   */
  protected $connectFieldName;

  /**
   * CustomFieldModel constructor.
   *
   * @param object $object
   */
  public function __construct($object) {
    parent::__construct($object);
    $this->type = $object->type;
    $this->setConnectFieldName();
  }

  public function modelType(): string {
    return 'customField';
  }

  /**
   * @return string
   */
  public function getType(): string {
    return $this->type;
  }

  /**
   * @return string
   */
  public function getConnectFieldName() {
    return $this->connectFieldName;
  }

  /**
   * Set the field name in Connect for custom fields of this model type.
   */
  public function setConnectFieldName(): void {
    $this->connectFieldName = self::CONNECT_FIELD_NAMES[$this->getId()];
  }

}
