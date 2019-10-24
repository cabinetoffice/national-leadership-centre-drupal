<?php

namespace Drupal\nlc_api\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
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

}
