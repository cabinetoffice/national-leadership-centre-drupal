<?php

namespace Drupal\nlc_salesforce\EventSubscriber;

use Drupal\salesforce\Event\SalesforceEvents;
use Drupal\salesforce_mapping\Event\SalesforcePushParamsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Drupal\nlc_salesforce\SFAPI\SFWrapper;

/**
 * Class NLCSalesforceSubscriber.
 *
 * @package Drupal\nlc_salesforce
 */
class NLCSalesforceSubscriber implements EventSubscriberInterface {

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
      SalesforceEvents::PUSH_PARAMS => 'pushParamsAlter',
    ];
  }

}
