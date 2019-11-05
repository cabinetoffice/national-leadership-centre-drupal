<?php

namespace Drupal\nlc_salesforce\Plugin\rest\resource;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\nlc_api\Plugin\rest\resource\NLCApiBaseResource;
use Drupal\salesforce\Exception;
use Psr\Log\LoggerInterface;

abstract class AbstractNetworkObjectResource extends NLCApiBaseResource {

  public $sfIdFieldName;

  /**
   * {@inheritDoc}
   *
   * @throws \Exception
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger, CacheBackendInterface $cache) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger, $cache);

    if (!$this->sfIdFieldName) {
      $message = t('Missing sfIdFieldName property for @class', ['@class' == __CLASS__]);
      throw new Exception($message);
    }

  }

  /**
   * Handles GET requests.
   *
   * @param string $id
   *   User ID.
   *
   * @return \Drupal\rest\ResourceResponse
   */
  abstract public function get($id);

  abstract public function getSfIDField();

}
