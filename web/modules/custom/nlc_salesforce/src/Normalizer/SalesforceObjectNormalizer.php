<?php


namespace Drupal\nlc_salesforce\Normalizer;

use Drupal\nlc_salesforce\Salesforce\object\SfObjectSupportedFields;
use Drupal\serialization\Normalizer\ComplexDataNormalizer;

class SalesforceObjectNormalizer extends ComplexDataNormalizer {

  protected $supportedInterfaceOrClass = [
    'Drupal\nlc_salesforce\Salesforce\object\SalesforceBaseObjectInterface',
    'Drupal\nlc_salesforce\Salesforce\object\SalesforceBaseListInterface',
    'Drupal\salesforce\Rest\RestResponseDescribe',
    'Drupal\nlc_salesforce\Salesforce\object\SfObjectSupportedFields',
    'Drupal\nlc_salesforce\Salesforce\object\SfObjectSupportedField',
    'stdClass',
  ];

}
