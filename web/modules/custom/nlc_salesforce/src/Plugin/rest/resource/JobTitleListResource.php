<?php

namespace Drupal\nlc_salesforce\Plugin\rest\resource;

use Drupal\Component\Utility\Xss;
use Drupal\rest\ResourceResponse;
use HttpRuntimeException;

/**
 * Provides a resource for getting the Job Titles list in the Salesforce CRM.
 *
 * @RestResource(
 *   id = "jobtitle",
 *   label = @Translation("NLC: Job Titles list"),
 *   uri_paths = {
 *     "canonical" = "/api/jobtitle"
 *   }
 * )
 *
 */
class JobTitleListResource extends AbstractSalesforceApiList {

  /**
   * @var string
   */
  protected $object_type_name = 'NetworkIndividualRole__c';

  /**
   * @var string
   */
  protected $field_name = 'Job_Title_Synonym__c';

  /**
   * @var string
   */
  protected $cache_key = 'nlc_salesforce:job_title_list';

}