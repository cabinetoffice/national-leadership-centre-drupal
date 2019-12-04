<?php
namespace Drupal\nlc_salesforce\SFAPI;

use Drupal\salesforce\SFID;

/**
 * Salesforce Wrapper class.
 * 
 * Provides helper functions around the Salesforce integration to the NLC site.
 */

class SFWrapper {

  /**
   * Definition of the field mapping between a SF contact and a Drupal user entity.
   */
  const CONTACT_FIELDS = [
    'nlc_sf_phone' => [
      'label' => 'Phone Number',
      'sf_field' => 'PhoneNumber__c',
      'format' => 'tel',
    ],
  ];

  /**
   * Definition of the field mapping between a SF role and Drupal profile.
   */
  const ROLE_FIELDS = [
    'nlc_role_phone' => [
      'label' => 'Phone number in role',
      'description' => 'Phone number when in this role.',
      'sf_field' => 'Role_phone__c',
      'format' => 'tel',
    ],
    'nlc_role_email' => [
      'label' => 'Email address in role',
      'description' => 'Email address when in this role.',
      'sf_field' => 'Role_email__c',
      'format' => 'email',
    ],
    'nlc_role_assistant_name' => [
      'label' => "Assistant's name",
      'description' => 'Name of the assistant when in this role.',
      'sf_field' => 'Assistant_name__c',
    ],
    'nlc_role_assistant_phone' => [
      'label' => "Assistant's phone",
      'description' => 'Phone number for an assistant when in this role.',
      'sf_field' => 'Assistant_phone__c',
      'format' => 'tel',
    ],
    'nlc_role_assistant_email' => [
      'label' => "Assistant's email",
      'description' => 'Email address for an assistant when in this role.',
      'sf_field' => 'Assistant_email__c',
      'format' => 'email',
    ],
  ];

  /**
   * The singelton instance of this class.
   */
  private static $instance = NULL;

  /**
   * Salesforce client.
   */
  private $client = NULL;
  /**
   * Salesforce map service.
   */
  private $mapService = NULL;
  /**
   * The submitted values.
   */
  private $submissions = [];

  /**
   * Create a new class to wrap the salesforce service.
   */
  private function __construct() {
    if (!$this->client) {
      $this->client = \Drupal::service('salesforce.client');
    }
  }

  /**
   * Get the Salesforce wrapper singleton.
   */
  public static function getInstance() {
    if (self::$instance === NULL) {
      self::$instance = new SFWrapper();
    }
    return self::$instance;
  }

  /**
   * Get the salesforce mapping service.
   */
  public function getMapService() {
    if ($this->mapService === NULL) {
      $this->mapService = \Drupal::service('entity_type.manager')
        ->getStorage('salesforce_mapped_object');
    }
    return $this->mapService;
  }

  /**
   * Get the first SF entity map if it exists.
   */
  public function getEntityMap($entity) {
    if ($service = $this->getMapService()) {
      if ($mappings = $service->loadByDrupal($entity->getEntityTypeId(), $entity->id())) {
        return array_pop($mappings);
      }
    }
    return NULL;
  }

  /**
   * Get a Salesforce object which matches the ID.
   *
   * @param $sfIdValue The Salesforce id.
   * @return SObject The Salesforce object
   */
  public function getObject($sfId) {
    /* TODO: Add caching depending on $sfId */
    $name = $this->client->getObjectTypeName(new SFID($sfId));
    if ($object = $this->client->objectRead($name, $sfId)) {
      return $object;
    }
    return NULL;
  }

  /**
   * Get field values from SF for the given entity.
   */
  public function getDetailsFromEntity($entity) {
    if ($map = $this->getEntityMap($entity)) {
      if ($sfId = $map->salesforce_id->value) {
        return $this->getDetails($sfId);
      }
    }
    return NULL;
  }

  /**
   * Get field values from the object with the passed ID.
   * 
   * @param $sfIdValue The Salesforce id.
   * @return [] Associatve array of field name and values from Salesforce.
   */
  public function getDetails($sfId) {
    if ($obj = $this->getObject($sfId)) {
      return $obj->fields();
    }
    return NULL;
  }

  /**
   * Store a submitted value.
   *
   * @param $field string
   *   Salesforce field name.
   * @param $value string
   *   The value to store.
   */
  public function addSubmission($field, $value) {
    $this->submissions[$field] = $value;
  }

  /**
   * Get the list of submitted values.
   *
   * @return array Associative array of sf field => value
   */
  public function getSubmissions() {
    return $this->submissions;
  }

}
