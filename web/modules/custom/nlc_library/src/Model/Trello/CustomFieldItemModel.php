<?php


namespace Drupal\nlc_library\Model\Trello;

class CustomFieldItemModel extends AbstractTrelloModel {

  /**
   * @var string
   */
  protected $type;

  /**
   * @var string
   */
  protected $fieldName;

  /**
   * @var array
   */
  protected $value;

  /**
   * @var string
   */
  protected $idCustomField;

  /**
   * @var \Drupal\nlc_library\Model\Trello\CustomFieldModel
   */
  protected $customFieldModel;

  /**
   * AbstractCustomFieldModel constructor.
   *
   * @param object $object
   */
  public function __construct($object) {
    parent::__construct($object);
    $this->idCustomField = $object->idCustomField;
    $this->value = $object->value;
  }

  /**
   * {@inheritDoc}
   */
  public function modelType(): string {
    return 'custom_field';
  }

  /**
   * The value of the custom field.
   *
   * @return mixed
   */
  public function getValue() {
    $type = $this->getCustomFieldModel()->getType();
    $value = $this->value->{$type};
    switch ($this->getCustomFieldModel()->getType()) {
      case 'number':
        $value = intval($value);
        break;

      case 'date':
        $timestamp = strtotime($value);
        $value = date('Y-m-d', $timestamp);
    }
    return $value;
  }

  /**
   * @return string
   */
  public function getIdCustomField(): string {
    return $this->idCustomField;
  }

  /**
   * @return \Drupal\nlc_library\Model\Trello\CustomFieldModel
   */
  public function getCustomFieldModel(): \Drupal\nlc_library\Model\Trello\CustomFieldModel {
    return $this->customFieldModel;
  }

  /**
   * @param \Drupal\nlc_library\Model\Trello\CustomFieldModel $customFieldModel
   */
  public function setCustomFieldModel(\Drupal\nlc_library\Model\Trello\CustomFieldModel $customFieldModel): void {
    $this->customFieldModel = $customFieldModel;
  }

}
