<?php

namespace Drupal\nlc_api\Plugin\rest\resource;

use Symfony\Component\HttpFoundation\Request;

interface NLCApiBaseResourceInterface {

  /**
   * Get the current HEAD version of the API
   *
   * @return int|string
   *    The current version as an integer or string (if we decide to use MAJOR.MINOR.PATCH notation)
   */
  public function getApiVersionCurrent();

  /**
   *
   * Get the version of the API from the request, or the current default if header for version is absent.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *    The HTTP request.
   *
   * @return string
   *    String representation of the API version requested.
   */
  public function getApiVersionRequest(Request $request);

}
