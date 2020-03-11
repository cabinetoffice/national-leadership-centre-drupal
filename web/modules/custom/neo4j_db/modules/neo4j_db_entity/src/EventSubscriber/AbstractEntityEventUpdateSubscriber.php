<?php


namespace Drupal\neo4j_db\EventSubscriber;

use Composer\EventDispatcher\EventSubscriberInterface;
use Drupal\neo4j_db_entity\Event\Neo4jDbEntityEvent;
use Drupal\neo4j_db_entity\Event\Neo4jDbEntityEventType;

/**
 * Event subscriber for entity update event.
 */
abstract class AbstractEntityEventUpdateSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[Neo4jDbEntityEventType::UPDATE][] = ['onEntityUpdate', 800];
    return $events;
  }

  /**
   * Method called when Event occurs.
   *
   * @param \Drupal\neo4j_db_entity\Event\Neo4jDbEntityEvent $event
   *   The event.
   */
  abstract public function onEntityUpdate(Neo4jDbEntityEvent $event);

}
