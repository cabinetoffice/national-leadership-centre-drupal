<?php


namespace Drupal\nlc_salesforce\Salesforce\object;

class SfObjectSupportedFields {

  /**
   * Name of the Salesforce object.
   *
   * @var string
   */
  private $name;

  /**
   * Array of Salesforce object fields.
   *
   * @var array
   */
  private $objectFields = [];

  /**
   * Array of SF Object field objects.
   *
   * @var \Drupal\nlc_salesforce\Salesforce\object\SfObjectSupportedField[]
   */
  private $fields = [];

  /**
   * SfObjectSupportedFields constructor.
   *
   * @param string $name
   *   Salesforce object name
   * @param array $objectFields
   *   Array of all fields on the object.
   */
  public function __construct($name, $objectFields) {
    $this->name = $name;
    $this->objectFields = $objectFields;
    foreach ($this->objectFields as $objectField) {
      $field = new SfObjectSupportedField($objectField);
      if ($field->isCustom()) {
        $fieldName = $field->getName();
        $this->fields[$fieldName] = $field;
      }
    }
  }

  /**
   * @param $property
   *
   * @return \Drupal\nlc_salesforce\Salesforce\object\SfObjectSupportedField|null
   */
  public function getField($property) {
    if (array_key_exists($property, $this->fields)) {
      return $this->fields[$property];
    }
    return null;
  }

  /**
   * @return \Drupal\nlc_salesforce\Salesforce\object\SfObjectSupportedField[]
   */
  public function getFields() {
    return $this->fields;
  }

  /**
   * Get a named property of an object field.
   *
   * @param $property
   *   The property to check.
   * @param array $field
   *   Object field array.
   *
   * @return string|bool|null
   */
  private function getFieldProperty($property, $field) {
    return $field[$property];
  }

}
