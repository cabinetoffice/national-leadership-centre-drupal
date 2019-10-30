<?php


namespace Drupal\nlc_salesforce\Salesforce\object;

abstract class SalesforceBaseList implements SalesforceBaseListInterface {

  /**
   * List type
   *
   * @var string
   */
  public $type;

  /**
   * @var array
   */
  public $values;

  /**
   * SalesforceBaseList constructor.
   *
   * @param array $list
   *
   * @throws \Drupal\nlc_salesforce\Salesforce\object\SalesforceObjectException
   */
  public function __construct($list) {
    if (empty($this->type)) {
      $message = t('Missing object type property in @class', ['@class' => __CLASS__]);
      throw new SalesforceObjectException($message);
    }
    $this->setList($list);
  }

  /**
   * {@inheritDoc}
   */
  public function setList($list) {
    $this->values = $list;
  }

  /**
   * {@inheritDoc}
   */
  public function getList() {
    return $this->list;
  }

}
