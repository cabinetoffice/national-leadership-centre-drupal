<?php


namespace Drupal\neo4j_db\EventSubscriber;

use Composer\EventDispatcher\EventSubscriberInterface;
use Drupal\neo4j_db_entity\Event\Neo4jDbEntityEvent;
use Drupal\neo4j_db_entity\Event\Neo4jDbEntityEventType;

/**
 * Event subscriber for entity insert event.
 */
abstract class AbstractEntityEventInsertSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[Neo4jDbEntityEventType::INSERT][] = ['onEntityInsert', 800];
    return $events;
  }

  /**
   * Method called when Event occurs.
   *
   * @param \Drupal\neo4j_db_entity\Event\Neo4jDbEntityEvent $event
   *   The event.
   */
  abstract public function onEntityInsert(Neo4jDbEntityEvent $event);

}
