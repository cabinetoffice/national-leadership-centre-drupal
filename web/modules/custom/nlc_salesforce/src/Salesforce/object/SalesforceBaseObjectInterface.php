<?php

namespace Drupal\nlc_salesforce\Salesforce\object;

interface SalesforceBaseObjectInterface {

  /**
   * Get the Salesforce ID of this object.
   *
   * @return string
   */
  public function id();

}
