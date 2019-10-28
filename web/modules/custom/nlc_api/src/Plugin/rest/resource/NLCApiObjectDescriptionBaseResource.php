<?php

namespace Drupal\nlc_api\Plugin\rest\resource;

use Drupal\Core\Cache\CacheBackendInterface;
use Psr\Log\LoggerInterface;

/**
 * Class NLCApiObjectDescriptionBaseResource
 *
 * @package Drupal\nlc_api\Plugin\rest\resource
 */
abstract class NLCApiObjectDescriptionBaseResource extends NLCApiBaseResource implements NLCApiObjectDescriptionBaseInterface {

  /**
   * @var string
   */
  protected $object_type_name;

  /**
   * NLCApiObjectDescriptionBaseResource constructor.
   *
   * {@inheritDoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger, CacheBackendInterface $cache) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger, $cache);

    if (empty($this->object_type_name)) {
      throw new NLCApiObjectDescriptionMalformedException('Missing required object type name');
    }
  }

}
