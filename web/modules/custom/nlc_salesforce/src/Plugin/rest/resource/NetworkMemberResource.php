<?php

namespace Drupal\nlc_salesforce\Plugin\rest\resource;

use Drupal\Core\TypedData\Exception\MissingDataException;
use Drupal\field\Entity\FieldConfig;
use Drupal\nlc_salesforce\Salesforce\object\NetworkIndividualSalesforceObject;
use Drupal\rest\ResourceResponse;
use Drupal\salesforce\SFID;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;
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

  protected $sfObjectName = 'NetworkIndividual__c';

  /**
   * Handles GET requests.
   *
   * @param string $id
   *   User ID.
   *
   * @return \Drupal\rest\ResourceResponse
   */
  public function get($id) {
    /** @var \Drupal\user\UserInterface $account */
    $account = User::load($id);
    $field = $this->getSfIDField();
    try {
      $sfIdValue = $this->getUserFieldSfId($account, $field);
      $this->sFApi->setId($sfIdValue);
      if ($this->sFApi->id() instanceof SFID) {
        $name = $this->sFApi->getSalesforce()->getObjectTypeName($this->sFApi->id());
        if ($object = $this->sFApi->getSalesforce()->objectRead($name, $this->sFApi->id()->__toString())) {
          $individual = new NetworkIndividualSalesforceObject($this->sFApi->id(), $this->sFApi->getSalesforce(), $name, $object);
          $individual->setBaseProperties();
          $response = new ResourceResponse($individual);
          $response->addCacheableDependency($account);
          return $response;
        }
      }
    }
    catch (MissingDataException $e) {
      return $this->noSfIdValue($id, $account);
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
    try {
      /** @var \Drupal\user\UserInterface $account */
      $account = User::load($id);
      // Get the Salesforce ID for this user account and set it for the REST request.
      $field = $this->getSfIDField();
      $sfIdValue = $this->getUserFieldSfId($account, $field);
      $this->sFApi->setId($sfIdValue);
      if ($this->sFApi->id() instanceof SFID) {
        $name = $this->sFApi->getSalesforce()->getObjectTypeName($this->sFApi->id());
        // Create a blank internal SF object so we can check the supported fields when setting the params.
        $this->sfObject = new NetworkIndividualSalesforceObject($this->sFApi->id(), $this->sFApi->getSalesforce(), $name);
        $name = $this->sFApi->getSalesforce()->getObjectTypeName($this->sFApi->id());
        $this->prepareParams($data);
        $object = $this->sFApi->getSalesforce()->objectUpdate($name, $this->sFApi->id()->__toString(), $this->params);
        // Save the account so the next
        $now = \Drupal::time()->getRequestTime();
        $account->setChangedTime($now);
        $account->save();
        $response = new ResourceResponse($this->params);
        $response->addCacheableDependency($account);
        return $response;
      }
    }
    catch (MissingDataException $e) {
      // The user did not have a Salesforce ID value set.
      return $this->noSfIdValue($id, $account);
    }
    catch (\Exception $e) {
      $response = new ResourceResponse($e->getMessage(), $e->getCode());
      $response->addCacheableDependency($account);
      return $response;
    }

    $build = [
      'message' => 'Got it',
      'data' => $data,
    ];


    throw new HttpException(503, t('Error'));
  }

  /**
   * {@inheritDoc}
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
   * @param \Drupal\user\UserInterface $account
   * @param \Drupal\field\Entity\FieldConfig $field
   *
   * @return string|null
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function getUserFieldSfId (UserInterface $account, $field) {
    if ($account->hasField($field->getName()) && !$account->get($field->getName())->isEmpty()) {
      $sfIdValue = $account->get($field->getName())
        ->get(0)
        ->getValue()['value'];
      return $sfIdValue;
    }
    return null;
  }

  /**
   * User does not have a valid Salesforce object ID set, so create a response.
   *
   * @param int $id
   * @param \Drupal\user\UserInterface $account
   *
   * @return \Drupal\rest\ResourceResponse
   */
  private function noSfIdValue($id, UserInterface $account) {
    $code = 404;
    $return = [
      'message' => t('No CRM record ID for user @id', ['@id' => $id]),
      'code' => $code,
    ];
    $response = new ResourceResponse($return, $code);
    $response->addCacheableDependency($account);
    return $response;
  }

}
