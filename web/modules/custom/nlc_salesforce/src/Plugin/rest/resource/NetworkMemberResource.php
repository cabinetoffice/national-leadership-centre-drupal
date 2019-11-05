<?php

namespace Drupal\nlc_salesforce\Plugin\rest\resource;

use Drupal\field\Entity\FieldConfig;
use Drupal\nlc_salesforce\Salesforce\object\NetworkIndividualSalesforceObject;
use Drupal\rest\ResourceResponse;
use Drupal\salesforce\SFID;
use Drupal\user\Entity\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Provides a resource for getting, editing and deleting personal data on Network Members in the Salesforce CRM.
 * @RestResource(
 *   id = "networkmember",
 *   label = @Translation("NLC Network: Member"),
 *   uri_paths = {
 *     "canonical" = "/api/member/{id}",
 *     "create" = "/api/member"
 *   }
 * )
 *
 */
class NetworkMemberResource extends AbstractNetworkObjectResource {

  public $sfIdFieldName =  'field_salesforce_record_id';

  /**
   * Handles GET requests.
   *
   * @param string $id
   *   User ID.
   *
   * @return \Drupal\rest\ResourceResponse
   */
  public function get($id) {
    $account = User::load($id);
    // In the event there are a lot of user_load() calls, cache the results.
    $sfIds = &drupal_static('nlc_salesforce_user_sfids_cache', []);
    $sfIndividuals = &drupal_static('nlc_salesforce_user_sfids_cache', []);
    $field = $this->getSfIDField();

    $uid = $account->id();
    if (isset($sfIds[$uid])) {
      $sfIdValue = $sfIds[$uid];
    }
    else {
      $sfIdValue = $this->getUserFieldSfId($account, $field);
      if (!isset($sfIdValue)) {
        // User does not have a valid Salesforce object ID set.
        $code = 404;
        $return = [
          'message' => t('No record ID for user @id', ['@id' => $id]),
          'code' => $code,
        ];
        $response = new ResourceResponse($return, $code);
        $response->addCacheableDependency($account);
        return $response;
      }
    }
    try {
      $this->sFApi->setId($sfIdValue);
      $sfIds[$uid] = $this->sFApi->id();
      if ($this->sFApi->id() instanceof SFID) {
        $name = $this->sFApi->getSalesforce()->getObjectTypeName($this->sFApi->id());
        if ($object = $this->sFApi->getSalesforce()->objectRead($name, $this->sFApi->id()->__toString())) {
          $individual = new NetworkIndividualSalesforceObject($this->sFApi->id(), $this->sFApi->getSalesforce(), $object);
          $response = new ResourceResponse($individual);
          $response->addCacheableDependency($account);
          return $response;
        }
      }
    }
    catch (\Exception $e) {
      $response = new ResourceResponse($e->getMessage(), $e->getCode());
      $response->addCacheableDependency($account);
      return $response;
    }
  }

  /**
   * Handles PATCH requests.
   *
   * @param string|int $id
   *   User ID.
   * @param array $data
   *   Array of data to update in Salesforce.
   *
   * @return \Drupal\rest\ResourceResponse
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   */
  public function patch($id, $data) {
    $user = \Drupal::currentUser();
    if ($user->id() !== $id) {
      throw new HttpException(403, t('Forbidden'));
    }
    /** @var \Drupal\user\UserInterface $account */
    $account = User::load($id);
    $uid = $account->id();
    // In the event there are a lot of user_load() calls, cache the results.
    $sfIds = &drupal_static('nlc_salesforce_user_sfids_cache', []);
    $field = $this->getSfIDField();

    if (isset($sfIds[$uid])) {
      $sfIdValue = $sfIds[$uid];
    }
    else {
      $sfIdValue = $this->getUserFieldSfId($account, $field);

      if (!isset($sfIdValue)) {
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
        $params = [
          'PhoneNumber__c' => $data['phone'],
        ];
        $object = $this->sFApi->getSalesforce()->objectUpdate($name, $this->sFApi->id()->__toString(), $params);
        // Save the account so the next
        $now = \Drupal::time()->getRequestTime();
        $account->setChangedTime($now);
        $account->save();
        $response = new ResourceResponse($object);
        $response->addCacheableDependency($account);
        return $response;
      }
    }
    catch (\Exception $e) {
      return new ResourceResponse($e->getMessage(), $e->getCode());
    }

    $build = [
      'message' => 'Got it',
      'data' => $data,
    ];


    throw new HttpException(503, t('Error'));
  }

  /**
   * Get the Drupal field for the Salesforce ID for this object.
   *
   * @return array|\Drupal\field\Entity\FieldConfig
   */
  public function getSfIDField() {
    $field = &drupal_static(__METHOD__, NULL);
    if (!isset($field)) {
      $field = FieldConfig::loadByName('user', 'user', $this->sfIdFieldName);
    }
    return $field;
  }

  /**
   * Get the Salesforce ID for a user account.
   *
   * @param \Drupal\Core\Entity\EntityInterface $account
   * @param \Drupal\field\Entity\FieldConfig $field
   *
   * @return string|null
   */
  public function getUserFieldSfId ($account, $field) {
    if ($account->hasField($field->getName()) && !$account->get($field->getName())->isEmpty()) {
      $sfIdValue = $account->get($field->getName())
        ->get(0)
        ->getValue()['value'];
      return $sfIdValue;
    }
    return null;
  }

}
