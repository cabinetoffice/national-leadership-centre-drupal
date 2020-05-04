<?php


namespace Drupal\neo4j_db_entity\EventSubscriber;

use Drupal\neo4j_db_entity\Event\Neo4jDbEntityEvent;
use Drupal\neo4j_db_entity\Event\Neo4jDbEntityEventType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event subscriber for entity delete event.
 */
abstract class AbstractEntityEventDeleteSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [];
    $events[Neo4jDbEntityEventType::DELETE][] = ['onEntityDelete', 800];
    return $events;
  }

  /**
   * Method called when Event occurs.
   *
   * @param \Drupal\neo4j_db_entity\Event\Neo4jDbEntityEvent $event
   *   The event.
   */
  abstract public function onEntityDelete(Neo4jDbEntityEvent $event);

}
