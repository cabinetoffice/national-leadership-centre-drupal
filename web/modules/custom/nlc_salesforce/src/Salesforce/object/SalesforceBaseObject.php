<?php

namespace Drupal\nlc_salesforce\Salesforce\object;

use Drupal\Component\Utility\Xss;
use Drupal\salesforce\Exception;
use Drupal\salesforce\Rest\RestClient;
use Drupal\salesforce\SFID;
use Drupal\salesforce\SObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class SalesforceBaseObject implements SalesforceBaseObjectInterface {

  /**
   * Salesforce ID of this object
   *
   * @var \Drupal\salesforce\SFID
   */
  private $salesforceId;

  /**
   * Salesforce REST client
   *
   * @var \Drupal\salesforce\Rest\RestClient
   */
  private $sfApi;

  /**
   * Salesforce object name.
   *
   * @var string
   */
  private $name;

  /**
   * Salesforce object.
   *
   * @var \Drupal\salesforce\SObject
   */
  protected $sfObject;

  /**
   * A logger instance.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * @var \Drupal\nlc_salesforce\Salesforce\object\SfObjectSupportedFields
   */
  protected $supportedFields;

  /**
   * @var string
   */
  private $dateTimeFormat = 'Y-m-d H:i';

  /**
   * @var string
   */
  public $objectId;

  /**
   * @var string
   */
  public $created;

  /**
   * @var string
   */
  public $updated;

  /**
   * @var string
   */
  protected $sfObjectName;

  /**
   * @var bool
   */
  protected $includeSfObjectFields = false;

  protected $includeSfObjectDescription = false;

  protected $friendlyNames = [];

  /**
   * @var array
   */
//  public $sfObjectFields = [];

  /**
   * SalesforceObjectBase constructor.
   *
   * @param \Drupal\salesforce\SFID $sfId
   *   Salesforce ID of this object.
   * @param RestClient $sfApi
   *   Salesforce REST client object.
   * @param \Drupal\salesforce\SObject $sf_object
   *   Salesforce object.
   *
   * @throws \Drupal\nlc_salesforce\Salesforce\object\SalesforceObjectException
   */
  public function __construct(SFID $sfId, RestClient $sfApi, $name, SObject $sf_object = null) {
    $this->salesforceId = $sfId;
    $this->sfApi = $sfApi;
    $this->name = $name;
    $this->sfObject = $sf_object;
    // Set the supported fields for this object.
    try {
      $this->setSupportedFields();
    }
    catch (\Exception $e) {
      $this->supportedFields = [$e->getCode(), $e->getMessage()];
    }

    if (!$this->sfObjectName) {
      $message = t('Missing sfObjectName property');
      throw new SalesforceObjectException($message);
    }
  }

  protected function setBaseProperties() {
    $this->objectId = $this->sfObject->field('Id');
    try {
      $this->created = $this->createdDate();
      $this->updated = $this->updatedDate();
    }
    catch (\Exception $e) {

    }

    if ($this->isIncludeSfObjectFields()) {
      $this->sfObjectFields = $this->sfObject->fields();
    }

    if ($this->isIncludeSfObjectDescription()) {
      try {
        $this->sfObjectTypeName = $this->sfObjectName;
//        $this->sfObjectDescription = $this->sfObjectDescribe()->getFields();
        $this->sfObjectDescription = $this->getSupportedFields();
      }
      catch (\Exception $e) {
        $this->sfObjectDescription = [
          'code' => $e->getCode(),
          'message' => $e->getMessage(),
        ];
      }
    }
  }

  /**
   * @return \Drupal\salesforce\Rest\RestResponseDescribe
   * @throws \Exception
   */
  public function sfObjectDescribe() {
    return $this->sfApi->objectDescribe($this->sfObjectType);
  }

  /**
   * Set an object property with the value from a Salesforce object key.
   *
   * @param $propName
   *   Property name.
   * @param $sfKey
   *   Salesforce object key.
   */
  protected function baseSetField($propName, $sfKey) {
    $this->{$propName} = Xss::filter($this->sfObject->field($sfKey));
  }

  /**
   * @return \Drupal\nlc_salesforce\Salesforce\object\SfObjectSupportedFields
   */
  public function getSupportedFields() {
    return $this->supportedFields;
  }

  /**
   * @param bool $includesfObjectFields
   */
  public function setIncludeSfObjectFields($includesfObjectFields) {
    $this->includesfObjectFields = empty($includesfObjectFields) ? false : true;
    $this->sfObjectFields = $this->sfObject->fields();
  }

  /**
   * @return bool
   */
  public function isIncludeSfObjectFields() {
    return $this->includeSfObjectFields;
  }

  /**
   * @return bool
   */
  public function isIncludeSfObjectDescription() {
    return $this->includeSfObjectDescription;
  }

  /**
   * @return array
   */
  public function getFriendlyNames() {
    return $this->friendlyNames;
  }

  /**
   * Get the Salesforce ID of this object.
   *
   * @return string
   */
  public function id() {
    return $this->salesforceId;
  }

  /**
   * Set the object created datetime array.
   *
   * @param string $dateTimeFormat
   *
   * @return array
   * @throws \Exception
   */
  public function createdDate($dateTimeFormat = null) {
    $dateTimeFormat = isset($dateTimeFormat) ? $dateTimeFormat : $this->dateTimeFormat;
    return $this->formatSfDate('CreatedDate', $dateTimeFormat);
  }

  /**
   * Set the object update datetime array.
   *
   * @param string $dateTimeFormat
   *
   * @return array
   * @throws \Exception
   */
  public function updatedDate($dateTimeFormat = null) {
    $dateTimeFormat = isset($dateTimeFormat) ? $dateTimeFormat : $this->dateTimeFormat;
    return $this->formatSfDate('LastModifiedDate', $dateTimeFormat);
  }

  /**
   * Create a formatted SF date, with some bonus options.
   *
   * @param $key
   * @param $format
   *
   * @return array
   *
   * @throws \Exception
   */
  private function formatSfDate($key, $format) {
    $sfDate = $this->sfObject->field($key);
    $dateTime = [
      'formatted' => $this->formatDatetime($sfDate, $format),
      'raw' => $sfDate,
      'iso8601' => $this->formatDatetime($sfDate, 'c'),
      'rfc2822' => $this->formatDatetime($sfDate, 'r'),
    ];
    return $dateTime;
  }

  /**
   * Set the datetime format for SF dates in this object.
   *
   * @param string $dateTimeFormat
   */
  public function setDateTimeFormat($dateTimeFormat) {
    $this->dateTimeFormat = $dateTimeFormat;
  }

  protected function ownerId() {
    return $this->sfObject->field('OwnerId');
  }

  /**
   * @param $dateTime
   * @param $format
   *
   * @return string
   * @throws \Exception
   */
  private function formatDatetime($dateTime, $format) {
    $dateTime = new \DateTime($dateTime);
    return $dateTime->format($format);
  }

  /**
   * @throws \Exception
   */
  protected function setSupportedFields() {
    $objectFields = $this->sfObjectDescribe()->getFields();
    $this->supportedFields = new SfObjectSupportedFields($this->name, $objectFields);
  }

  protected function objectFields() {
  }

}
