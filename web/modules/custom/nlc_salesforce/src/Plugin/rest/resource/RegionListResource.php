<?php

namespace Drupal\nlc_salesforce\Plugin\rest\resource;

use Drupal\nlc_salesforce\Salesforce\object\RegionList;

/**
 * Provides a resource for getting the region list in the Salesforce CRM.
 *
 * @RestResource(
 *   id = "regionlist",
 *   label = @Translation("NLC: Region list"),
 *   uri_paths = {
 *     "canonical" = "/api/region"
 *   }
 * )
 *
 */
class RegionListResource extends AbstractSalesforceApiList {

  /**
   * @var string
   */
  protected $object_type_name = 'NetworkOrganisation__c';

  /**
   * @var string
   */
  protected $field_name = 'Region__c';

  /**
   * @var string
   */
  protected $cache_key = 'nlc_salesforce:region_list';

  /**
   * @param $data
   *
   * @return \Drupal\nlc_salesforce\Salesforce\object\SalesforceBaseListInterface
   * @throws \Drupal\nlc_salesforce\Salesforce\object\SalesforceObjectException
   */
  protected function composeResponseObject($data) {
    return new RegionList($data);
  }

}
