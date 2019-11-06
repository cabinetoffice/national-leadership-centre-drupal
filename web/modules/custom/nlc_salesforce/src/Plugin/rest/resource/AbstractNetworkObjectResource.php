<?php

namespace Drupal\nlc_salesforce\Plugin\rest\resource;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\nlc_api\Plugin\rest\resource\NLCApiBaseResource;
use Drupal\nlc_salesforce\Salesforce\object\SfObjectSupportedFields;
use Drupal\salesforce\Exception;
use Psr\Log\LoggerInterface;

abstract class AbstractNetworkObjectResource extends NLCApiBaseResource {

  public $sfIdFieldName;
  protected $sfObjectName;

  /**
   * @var \Drupal\nlc_salesforce\Salesforce\object\SalesforceBaseObject
   */
  protected $sfObject;

  /**
   * Parameters for a PATCH or POST request.
   *
   * @var array
   */
  protected $params = [];

  /**
   * Salesforce API request service.
   *
   * @var \Drupal\nlc_salesforce\Salesforce\api\SalesforceApiRequest
   */
  protected $sFApi;

  protected $objectSupportedFields;

  /**
   * {@inheritDoc}
   *
   * @throws \Exception
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger, CacheBackendInterface $cache) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger, $cache);

    $this->sFApi = \Drupal::service('nlc_salesforce.api');

    if (!$this->sfIdFieldName) {
      $message = t('Missing required sfIdFieldName property for @class', ['@class' == __CLASS__]);
      throw new Exception($message);
    }

    if (!$this->sfObjectName) {
      $message = t('Missing required sfObjectName property for @class', ['@class' == __CLASS__]);
      throw new Exception($message);
    }

    $this->objectSupportedFields = new SfObjectSupportedFields($this->sfObjectName, $this->sFApi->getSalesforce()->objectDescribe($this->sfObjectName)->getFields());

  }

  /**
   * Get the Drupal field for the Salesforce ID for this object.
   *
   * @return array|\Drupal\field\Entity\FieldConfig
   */
  abstract public function getSfIDField();

  /**
   * Handles GET requests.
   *
   * @param string $id
   *   User ID.
   *
   * @return \Drupal\rest\ResourceResponse
   */
  abstract public function get($id);

  /**
   * @param $data
   *
   * @throws \Exception
   */
  protected function prepareParams($data) {
    if (!$this->sfObject) {
      $message = t('Missing required sfObject property for @class', ['@class' == __CLASS__]);
      throw new \Exception($message);
    }

    $this->paramsArray($data);
  }

  private function paramsArray($data, $parent = null) {
    $friendlyNames = $this->sfObject->getFriendlyNames();
    foreach ($data as $key => $item) {
      $sfFieldName = null;
      if (is_array($item)) {
        $this->paramsArray($item, $key);
      }
      else {
        if (isset($parent)) {
          $sfFieldName = $friendlyNames[$parent][$key];
        }
        else {
          $sfFieldName = $friendlyNames[$key];
        }
        if ($this->sfObject->getSupportedFields()
          ->getField($sfFieldName)
          ->isUpdateable()) {
          $this->params[$sfFieldName] = $item;
        }
      }
    }
  }

}
