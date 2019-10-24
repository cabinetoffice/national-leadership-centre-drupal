<?php


namespace Drupal\nlc_salesforce\Salesforce\api;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\salesforce\Rest\RestClient;
use Drupal\salesforce\SFID;

class SalesforceApiRequest implements SalesforceApiRequestInterface {

  /**
   * Salesforce ID for this request.
   *
   * @var \Drupal\salesforce\SFID
   */
  private $id;

  /**
   * @var \Drupal\salesforce\Rest\RestClient
   */
  private $salesforce;

  /**
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  private $logger;

  /**
   * SalesforceApiRequest constructor.
   *
   * @param \Drupal\salesforce\Rest\RestClient
   *   Salesforce REST client.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface
   *   Logger
   *
   * @throws \Exception
   */
  public function __construct(RestClient $salesforce, LoggerChannelFactoryInterface $logger) {
    $this->salesforce = $salesforce;
    $this->logger = $logger->get('salesforce_api');
  }

  /**
   * {@inheritDoc}
   */
  public function setId($id) {
    $this->id = $id instanceof SFID ? $id : new SFID($id);
  }

  /**
   * {@inheritDoc}
   */
  public function id() {
    return $this->id;
  }

  /**
   * @return \Drupal\salesforce\Rest\RestClient
   */
  public function getSalesforce() {
    $this->logMe();
    return $this->salesforce;
  }



  /**
   * @return bool
   */
  public function logMe() {
    return true;
  }

}
