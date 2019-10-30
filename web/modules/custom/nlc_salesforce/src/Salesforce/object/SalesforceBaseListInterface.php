<?php

namespace Drupal\nlc_salesforce\Salesforce\object;

interface SalesforceBaseListInterface {

  /**
   * Set the list data array.
   *
   * @param $data
   *
   * @return void
   */
  public function setList($data);

  /**
   * Get the list array.
   *
   * @return array
   */
  public function getList();
}
