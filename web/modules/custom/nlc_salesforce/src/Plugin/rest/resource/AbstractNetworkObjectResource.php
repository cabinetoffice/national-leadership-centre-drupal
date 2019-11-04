<?php

namespace Drupal\nlc_salesforce\Plugin\rest\resource;

use Drupal\nlc_api\Plugin\rest\resource\NLCApiBaseResource;

abstract class AbstractNetworkObjectResource extends NLCApiBaseResource {

  /**
   * Handles GET requests.
   *
   * @param string $id
   *   User ID.
   *
   * @return \Drupal\rest\ResourceResponse
   */
  public function get($id) {

  }

}
