<?php

namespace Drupal\nlc_salesforce\Plugin\rest\resource;

use Drupal\Component\Utility\Xss;
use Drupal\rest\ResourceResponse;
use HttpRuntimeException;

/**
 * Provides a resource for getting the Job Titles list in the Salesforce CRM.
 *
 * @RestResource(
 *   id = "sectorlist",
 *   label = @Translation("NLC: Sector list"),
 *   uri_paths = {
 *     "canonical" = "/api/sector"
 *   }
 * )
 *
 */
class SectorListResource extends AbstractSalesforceApiList {

  /**
   * @var string
   */
  protected $object_type_name = 'NetworkOrganisation__c';

  /**
   * @var string
   */
  protected $field_name = 'Sector__c';

  /**
   * @var string
   */
  protected $cache_key = 'nlc_salesforce:sector_list';

}
