<?php


namespace Drupal\nlc_salesforce\Salesforce\object;

class SfObjectSupportedField {

  /**
   * Salesforce array of the object field.
   *
   * @var array
   */
  private $field;

  /**
   * @var bool
   */
  private $calculated = false;

  /**
   * @var bool
   */
  private $createable = false;

  /**
   * @var bool
   */
  private $custom = false;

  /**
   * @var null|string
   */
  private $defaultValue;

  /**
   * @var bool
   */
  private $dependentPicklist = false;

  /**
   * @var string
   */
  private $name;

  /**
   * @var array
   */
  private $picklistValues = [];

  /**
   * @var bool
   */
  private $updateable = false;

  /**
   * @var string
   */
  private $type;

  /**
   * SfObjectSupportedField constructor.
   *
   * @param array $field
   *   Salesforce array of the object field.
   */
  public function __construct($field) {
    $this->field = $field;

//    $this->setCalculated($field);
//    $this->setCreatable($field);
//    $this->setCustom($field);
//    $this->setDefaultValue($field);
//    $this->setDependentPicklist($field);
//    $this->setName($field);
//    $this->setPicklistValues($field);
//    $this->setType($field);
//    $this->setUpdateable($field);

    $refObj = new \ReflectionObject($this);
    foreach ($refObj->getProperties() as $property) {
      $name = $property->getName();
      if ($name !== 'field' && !empty($field[$name])) {
        $this->$name = $field[$name];
      }
    }

  }

  /**
   * Getter.
   *
   * @param string $name
   *
   * @return mixed
   */
  public function __get($name) {
    if (array_key_exists($name, $this->field)) {
      return $this->field[$name];
    }
    return false;
  }

  /**
   * Setter.
   *
   * @param $property
   * @param $value
   *
   * @return $this
   */
  public function __set($property, $value) {
    if (property_exists($this, $property)) {
      $this->$property = $value;
    }

    return $this;
  }

  /**
   * @return bool
   */
  public function isCalculated() {
    return $this->calculated;
  }

  /**
   * @param array $field
   */
  public function setCalculated($field) {
    $this->calculated = $field['calculated'];
  }

  /**
   * @return bool
   */
  public function isCreateable() {
    return $this->createable;
  }

  /**
   * @param array $field
   */
  public function setCreateable($field) {
    $this->createable = $field['createable'];
  }

  /**
   * @return bool
   */
  public function isCustom() {
    return $this->custom;
  }

  /**
   * @param array $field
   */
  public function setCustom($field) {
    $this->custom = $field['custom'];
  }

  /**
   * @return bool
   */
  public function isUpdateable() {
    return $this->updateable;
  }

  /**
   * @param array $field
   */
  public function setUpdateable($field) {
    $this->updateable = $field['updateable'];
  }

  /**
   * @return string|null
   */
  public function getDefaultValue() {
    return $this->defaultValue;
  }

  /**
   * @param array $field
   */
  public function setDefaultValue($field) {
    $this->defaultValue = $field['defaultValue'];
  }

  /**
   * @return bool
   */
  public function hasDependentPicklist() {
    return $this->dependentPicklist;
  }

  /**
   * @param array $field
   */
  public function setDependentPicklist($field) {
    $this->dependentPicklist = $field['dependentPicklist'];
  }

  /**
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @param array $field
   */
  public function setName($field) {
    $this->name = $field['name'];
  }

  /**
   * @return array
   */
  public function getPicklistValues() {
    return $this->picklistValues;
  }

  /**
   * @param array $field
   */
  public function setPicklistValues($field) {
    $this->picklistValues = $field['picklistValues'];
  }

  /**
   * @return string
   */
  public function getType() {
    return $this->type;
  }

  /**
   * @param array $field
   */
  public function setType($field) {
    $this->type = $field['type'];
  }

}
