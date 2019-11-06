<?php

namespace Drupal\nlc_salesforce\Plugin\rest\resource;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\nlc_api\Plugin\rest\resource\NLCApiObjectDescriptionBaseResource;
use Drupal\nlc_api\Plugin\rest\resource\NLCApiObjectDescriptionMalformedException;
use Drupal\rest\ResourceResponse;
use HttpResponseException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Get a list of things from a Salesforce RESTful API request.
 *
 * @package Drupal\nlc_salesforce\Plugin\rest\resource
 */
abstract class AbstractSalesforceApiList extends NLCApiObjectDescriptionBaseResource {

  /**
   * The field name to use for the pick list values.
   *
   * @var string
   */
  protected $field_name;

  /**
   * Cache key for this Salesforce API list.
   *
   * @var string
   */
  protected $cache_key;
  
  /**
   * Salesforce API request service.
   *
   * @var \Drupal\nlc_salesforce\Salesforce\api\SalesforceApiRequest
   */
  protected $sFApi;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger, CacheBackendInterface $cache) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger, $cache);

    $this->sFApi = \Drupal::service('nlc_salesforce.api');

    if (empty($this->object_type_name)) {
      throw new NLCApiObjectDescriptionMalformedException('Missing required object field name');
    }

    if (empty($this->cache_key)) {
      throw new NLCApiObjectDescriptionMalformedException('Missing required cache key');
    }
  }

  /**
   * Responds to GET requests.
   *
   * @return ResourceResponse
   * @throws \HttpException
   */
  public function get() {
    $cache = $this->cache->get($this->cache_key);
    if (!empty($cache)) {
      $list = $cache->data;
    }
    else {
      try {
        $object = $this->sFApi->getSalesforce()->objectDescribe($this->object_type_name);
        $sf_list = $object->getField($this->field_name)['picklistValues'];
        $list = [];
        foreach ($sf_list as $item) {
          $value = Xss::filter($item['value']);
          $label = Xss::filter($item['label']);
          $list[$value] = $label;
        }
        $list = $this->composeResponseObject($list);
        $this->cache->set($this->cache_key, $list);
      }
      catch (\Exception $e) {
        throw new BadRequestHttpException($e->getMessage(), $e, $e->getCode());
      }
    }
    return $this->sendResponse($list);
  }

  abstract protected function composeResponseObject($data);

}
