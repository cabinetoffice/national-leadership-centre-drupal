<?php


namespace Drupal\nlc_salesforce\Salesforce\object;

use Drupal\salesforce\SObject;

/**
 * Network Individual Salesforce model object class
 *
 * @package Drupal\nlc_salesforce\Salesforce\object
 */
class NetworkIndividualSalesforceObject extends SalesforceBaseObject {

  protected $includeSfObjectFields = true;

  /**
   * @var \stdClass
   */
  public $name;

  /**
   * @var string
   */
  public $email;

  /**
   * @var string
   */
  public $phone;

  /**
   * @var string
   */
  public $type;

  /**
   * @var \stdClass
   */
  public $socialMedia;

  protected function setBaseProperties() {
    parent::setBaseProperties(); // TODO: Change the autogenerated stub

    // Set the individual's name properties.
    $this->baseSetName();
    // Set the individual's email
    $this->baseSetField('email', 'EmailAddress__c');
    // Set the individual's phone
    $this->baseSetField('email', 'PhoneNumber__c');
    // Set the individual type
    $this->baseSetField('type', 'Type__c');
    // Set the individual's social media info
    $this->baseSetSocialMedia();
  }

  /**
   * Set the name properties of this object.
   */
  private function baseSetName() {
    $this->name = new \stdClass();
    $this->name->firstname = $this->sfObject->field('FirstName__c');
    $this->name->lastname = $this->sfObject->field('LastName__c');
    $this->name->fullname = $this->sfObject->field('FullName__c') ? $this->sfObject->field('FullName__c') : "{$this->name->firstname} {$this->name->lastname}";
    $this->name->title = $this->sfObject->field('Title__c');
  }

  private function baseSetSocialMedia() {
    $this->socialMedia = new \stdClass();
    $this->socialMedia->twitter = $this->sfObject->field('Twitter_Handle__c');
    $this->socialMedia->linkedIn = $this->sfObject->field('LinkedIn_Account__c');
  }


}
