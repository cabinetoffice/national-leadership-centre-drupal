<?php


namespace Drupal\nlc_salesforce\Normalizer;

use Drupal\serialization\Normalizer\ComplexDataNormalizer;

class SalesforceObjectNormalizer extends ComplexDataNormalizer {

  protected $supportedInterfaceOrClass = [
    'Drupal\nlc_salesforce\Salesforce\object\SalesforceBaseObjectInterface',
    'Drupal\nlc_salesforce\Salesforce\object\SalesforceBaseListInterface',
    'stdClass',
  ];

}
