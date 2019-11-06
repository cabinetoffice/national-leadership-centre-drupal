<?php


namespace Drupal\nlc_salesforce\Salesforce\object;

use Drupal\salesforce\SObject;
use Drupal\Component\Utility\Xss;

/**
 * Network Individual Salesforce model object class
 *
 * @package Drupal\nlc_salesforce\Salesforce\object
 */
class NetworkIndividualSalesforceObject extends SalesforceBaseObject {

  protected $includeSfObjectFields = true;
  protected $includeSfObjectDescription = false;

  /**
   * @var string
   */
  protected $sfObjectName = 'NetworkIndividual__c';

  protected $friendlyNames = [
    'name' => [
      'firstname' => 'FirstName__c',
      'lastname' => 'LastName__c',
      'fullname' => 'FullName__c',
      'title' => 'Title__c',
    ],
    'email' => 'EmailAddress__c',
    'phone' => 'PhoneNumber__c',
    'type' => 'Type__c',
    'socialMedia' => [
      'twitter' => 'Twitter_Handle__c',
      'linkedIn' => 'LinkedIn_Account__c',
    ],
  ];

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

  protected $sfObjectType = 'NetworkIndividual__c';

  public function setBaseProperties() {
    parent::setBaseProperties();

    // Set the individual's name properties.
    $this->baseSetName();
    // Set the individual's email
    $this->baseSetField('email', 'EmailAddress__c');
    // Set the individual's phone
    $this->baseSetField('phone', 'PhoneNumber__c');
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
    $this->name->title = Xss::filter($this->sfObject->field('Title__c'));
    $this->name->firstname = Xss::filter($this->sfObject->field('FirstName__c'));
    $this->name->lastname = Xss::filter($this->sfObject->field('LastName__c'));
    $this->name->fullname = $this->sfObject->field('FullName__c') ? Xss::filter($this->sfObject->field('FullName__c')) : implode(' ', [$this->name->title, $this->name->firstname, $this->name->lastname]);
  }

  private function baseSetSocialMedia() {
    $this->socialMedia = new \stdClass();
    $this->socialMedia->twitter = Xss::filter($this->sfObject->field('Twitter_Handle__c'));
    $this->socialMedia->linkedIn = Xss::filter($this->sfObject->field('LinkedIn_Account__c'));
  }

}
