<?php


namespace Drupal\nlc_salesforce\Salesforce\object;

class SfObjectSupportedFields {

  private $supportedFields = [];

  /**
   * SfObjectSupportedFields constructor.
   *
   * @param array $supportedFields
   */
  public function __construct($supportedFields) {
    $this->supportedFields = $supportedFields;
  }



}
