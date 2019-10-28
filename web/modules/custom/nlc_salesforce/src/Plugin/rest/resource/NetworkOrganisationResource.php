<?php


namespace Drupal\nlc_salesforce\Plugin\rest\resource;

use Drupal\nlc_api\Plugin\rest\resource\NLCApiBaseResource;

/**
 * Provides a resource for getting, editing and deleting personal data on Network Organisations in the Salesforce CRM.
 * @RestResource(
 *   id = "networkorganisation",
 *   label = @Translation("NLC Network: Organisation Collection"),
 *   uri_paths = {
 *     "canonical" = "/api/organisation/{id}",
 *     "create" = "/api/organisation"
 *   }
 * )
 *
 */
class NetworkOrganisationResource extends NLCApiBaseResource  {


  public function get($id) {

  }
}
