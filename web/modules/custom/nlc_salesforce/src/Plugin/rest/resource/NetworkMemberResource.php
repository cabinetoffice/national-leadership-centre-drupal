<?php

namespace Drupal\nlc_salesforce\Plugin\rest\resource;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\nlc_api\Plugin\rest\resource\NLCApiBaseResource;
use Drupal\nlc_salesforce\Salesforce\object\NetworkIndividualSalesforceObject;
use Drupal\rest\ResourceResponse;
use Drupal\salesforce\SFID;
use Drupal\user\Entity\User;
use Psr\Log\LoggerInterface;

/**
 * Provides a resource for getting, editing and deleting personal data on Network Members in the Salesforce CRM.
 * @RestResource(
 *   id = "networkmember",
 *   label = @Translation("NLC Network: Member Collection"),
 *   uri_paths = {
 *     "canonical" = "/api/member/{id}",
 *     "create" = "/api/member"
 *   }
 * )
 *
 */
class NetworkMemberResource extends NLCApiBaseResource {


  public function get($id) {
    $account = User::load($id);
    // In the event there are a lot of user_load() calls, cache the results.
    $sfIds = &drupal_static('nlc_salesforce_user_sfids_cache', []);
    $sfIndividuals = &drupal_static('nlc_salesforce_user_sfids_cache', []);
    $field = &drupal_static(__METHOD__, NULL);

    if (!isset($field)) {
      $field_name = 'field_salesforce_record_id';
      $field = FieldConfig::loadByName('user', 'user', $field_name);
    }
    if ($field) {
      $uid = $account->id();
      if (isset($sfIds[$uid])) {
        $sfIdValue = $sfIds[$uid];
      }
      else {
        if ($account->hasField($field->getName()) && !$account->get($field->getName())->isEmpty()) {
          $sfIdValue = $account->get($field->getName())
            ->get(0)
            ->getValue()['value'];
        }
        else {
          // User does not have a valid Salesforce object ID set.
          $code = 404;
          $return = [
            'message' => t('No record ID for user @id', ['@id' => $id]),
            'code' => $code,
          ];
          return new ResourceResponse($return, $code);
        }
      }
      try {
        $this->sFApi->setId($sfIdValue);
        $sfIds[$uid] = $this->sFApi->id();
        if ($this->sFApi->id() instanceof SFID) {
          $name = $this->sFApi->getSalesforce()->getObjectTypeName($this->sFApi->id());
          if ($object = $this->sFApi->getSalesforce()->objectDescribe($name)) {
            return new ResourceResponse($object->getFields());
          }
          if ($object = $this->sFApi->getSalesforce()->objectRead($name, $this->sFApi->id()->__toString() . '/NetworkOrganisation__r')) {
            $individual = new NetworkIndividualSalesforceObject($this->sFApi->id(), $object);
            return new ResourceResponse($individual);
          }
        }
      }
      catch (\Exception $e) {
        return new ResourceResponse($e->getMessage(), $e->getCode());
      }
      return new ResourceResponse(['message' => 'Error', 'code' => 503], 503);
    }
  }

  public function patch($uid) {

  }

}
