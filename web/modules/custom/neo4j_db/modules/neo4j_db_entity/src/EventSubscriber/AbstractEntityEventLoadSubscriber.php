<?php


namespace Drupal\neo4j_db_entity\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\neo4j_db_entity\Event\Neo4jDbEntityEvent;
use Drupal\neo4j_db_entity\Event\Neo4jDbEntityEventType;

/**
 * Event subscriber for entity load event.
 */
abstract class AbstractEntityEventLoadSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [];
    $events[Neo4jDbEntityEventType::LOAD][] = ['onEntityLoad', 800];
    return $events;
  }

  /**
   * Method called when Event occurs.
   *
   * @param \Drupal\neo4j_db_entity\Event\Neo4jDbEntityEvent $event
   *   The event.
   */
  abstract public function onEntityLoad(Neo4jDbEntityEvent $event);

}
