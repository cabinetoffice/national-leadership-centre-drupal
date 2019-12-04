<?php
namespace Drupal\nlc_salesforce\EventSubscriber;

use Drupal\salesforce\Event\SalesforceEvents;
use Drupal\salesforce_mapping\Event\SalesforcePullEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\salesforce_mapping\Event\SalesforcePushParamsEvent;

use Drupal\nlc_salesforce\SFAPI\SFWrapper;

/**
 * Class NLCSalesforceSubscriber.
 *
 * @package Drupal\nlc_salesforce
 */
class NLCSalesforceSubscriber implements EventSubscriberInterface {

  /**
   * PULL_PREPULL event subscriber.
   */
  public function pullPrepull(SalesforcePullEvent $event) {

    $mapping = $event->getMapping();
    switch ($mapping->id()) {
      case 'network_individual_role_profile_':
        $sf_data = $event->getMappedObject()->getSalesforceRecord();

        try {
          if (!$sf_data->field('Network_Individual__c')) {
            // If the Individual is not set, don't pull the record.
            $event->disallowPull();
            \Drupal::logger('nlc_salesforce')->notice('Skipped import of Role @role (@id) due to missing Network Individual', [
              '@role' => $sf_data->field('Name'),
              '@id' => $sf_data->field('Id'),
            ]);
          }
        }
        catch (\Exception $e) {
          // Fall through if "Individual" field was not found.
          $event->disallowPull();
        }
        break;
    }
  }

  /*
   * SalesforcePushParamsEvent callback.
   *
   * @param \Drupal\salesforce_mapping\Event\SalesforcePushParamsEvent $event
   *   The event.
   */
  public function pushParamsAlter(SalesforcePushParamsEvent $event) {
    /** @var \Drupal\Core\Entity\Entity $entity */
    $entity = $event->getEntity();

    if (in_array($entity->getEntityTypeId(), ['user', 'profile'])) {
      $params = $event->getParams();

      $client = SFWrapper::getInstance();
      foreach($client->getSubmissions() as $field => $val) {
        $params->setParam($field, $val);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      SalesforceEvents::PULL_PREPULL => 'pullPrepull',
      SalesforceEvents::PUSH_PARAMS => 'pushParamsAlter',
    ];
  }

}

