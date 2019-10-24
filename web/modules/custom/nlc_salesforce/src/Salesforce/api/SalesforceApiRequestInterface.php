<?php


namespace Drupal\nlc_salesforce\Salesforce\api;

interface SalesforceApiRequestInterface {

  /**
   * Set the Salesforce ID.
   *
   * @param string|\Drupal\salesforce\SFID $id
   *
   * @throws \Exception
   */
  public function setId($id);

  /**
   * Get the Salesforce ID for this API request.
   *
   * @return \Drupal\salesforce\SFID
   */
  public function id();

  /**
   * @return boolean
   */
  public function logMe();
}
