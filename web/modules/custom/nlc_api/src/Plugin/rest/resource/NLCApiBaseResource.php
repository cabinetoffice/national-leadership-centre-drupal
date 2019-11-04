<?php

namespace Drupal\nlc_api\Plugin\rest\resource;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

abstract class NLCApiBaseResource extends ResourceBase implements NLCApiBaseResourceInterface {

  /**
   * Current Agency App REST API version
   *
   * @var int
   */
  private $apiVersionCurrent = 1;

  /**
   * The header that contains the API version request.
   *
   * @var string
   */
  private $versionHeader = 'Accepts-version';

  /**
   * The cache service.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cache;

  /**
   * Salesforce API request service.
   *
   * @var \Drupal\nlc_salesforce\Salesforce\api\SalesforceApiRequest
   */
  protected $sFApi;

  /**
   * Response code.
   *
   * @var int
   */
  protected $responseCode = 200;

  /**
   * Response headers.
   *
   * @var array
   */
  protected $responseHeaders = [];

  /**
   * Constructs a \Drupal\nlc_api\Plugin\rest\resource\NLCApiBaseResource object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   A cache service instance.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger, CacheBackendInterface $cache) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->cache = $cache;
    $this->sFApi = \Drupal::service('nlc_salesforce.api');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest'),
      $container->get('cache.default')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getApiVersionCurrent() {
    return $this->apiVersionCurrent;
  }

  /**
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @return int|string
   */
  public function getApiVersionRequest(Request $request) {
    $apiVersionRequest = $request->headers->has($this->versionHeader) ? (int) $request->headers->get($this->versionHeader) : $this->getApiVersionCurrent();

    // Handle a request for a version greater than the current version
    $apiVersionRequest = $apiVersionRequest > $this->getApiVersionCurrent() ? $this->getApiVersionCurrent() : $apiVersionRequest;

    return $apiVersionRequest;
  }

  /**
   * Is there a request for embedded items?
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @return array|null
   */
  public function getApiEmbedRequest(Request $request) {
    $embed = [];

    if ($apiEmbedRequest = $request->query->get('embed')) {
      $items = explode(',', $apiEmbedRequest);
      foreach ($items as $item) {
        $this->assignArrayByPath($embed, $item);
      }
    }
    return $embed;
  }

  /**
   * Parse dot notation and populate a multi-dimensional array with values for embed request.
   *
   * @param array $arr - The multi-dimensional array to populate, passed by reference.
   * @param string $path - The dot notation path to parse.
   * @param bool|string $value - The value to assign to array keys.
   * @param string $separator - The separator in the path string.
   */
  private function assignArrayByPath(&$arr, $path, $value = TRUE, $separator = '.') {
    $keys = explode($separator, $path);

    foreach ($keys as $key) {
      $arr = &$arr[$key];
    }

    $arr = $value;
  }

  /**
   * Validate that the required fields are included in the POST/PATCH request.
   *
   * @param array $data
   * @param array $required
   * @throws BadRequestHttpException
   */
  protected function validatePostRequiredFields($data, $required = []) {
    if ($diff = array_diff($required, array_keys($data))) {
      throw new BadRequestHttpException(t('Required parameters not found in request: @params', ['@params' => implode(', ', $diff)]));
    }
  }

  /**
   * Return a REST resource response.
   *
   * @param array|object $data
   *   Response data
   * @param array|object $cacheableDependency
   *   A cacheable dependency for the response.
   *
   * @return \Drupal\rest\ResourceResponse
   */
  public function sendResponse($data, $cacheableDependency = NULL) {
    $response = new ResourceResponse($data, $this->responseCode, $this->responseHeaders);
    if (isset($cacheableDependency)) {
      $response->addCacheableDependency($cacheableDependency);
    }
    return $response;
  }

}
