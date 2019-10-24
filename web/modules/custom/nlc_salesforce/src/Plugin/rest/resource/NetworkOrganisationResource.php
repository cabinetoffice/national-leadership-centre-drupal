<?php


namespace Drupal\nlc_salesforce\Plugin\rest\resource;

use Drupal\nlc_api\Plugin\rest\resource\NLCApiBaseResource;
use Psr\Log\LoggerInterface;

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

  /**
   * Salesforce API request service.
   *
   * @var \Drupal\nlc_salesforce\Salesforce\api\SalesforceApiRequest
   */
  private $sFApi;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);

    $this->sFApi = \Drupal::service('nlc_salesforce.api');
  }

  public function get($id) {
    
  }
}
