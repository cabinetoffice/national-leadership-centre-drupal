<?php


namespace Drupal\neo4j_db_entity\EventSubscriber;

use Drupal\neo4j_db_entity\Event\Neo4jDbEntityEvent;
use Drupal\neo4j_db_entity\Event\Neo4jDbEntityEventType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event subscriber for entity events.
 */
abstract class AbstractEntityEventSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [];
    $events[Neo4jDbEntityEventType::INSERT][] = ['onEntityInsert', 800];
    $events[Neo4jDbEntityEventType::UPDATE][] = ['onEntityUpdate', 800];
    $events[Neo4jDbEntityEventType::DELETE][] = ['onEntityDelete', 800];
    $events[Neo4jDbEntityEventType::LOAD][] = ['onEntityLoad', 800];
    $events[Neo4jDbEntityEventType::VIEW][] = ['onEntityView', 800];
    return $events;
  }

  /**
   * Method called when entity is created.
   *
   * @param \Drupal\neo4j_db_entity\Event\Neo4jDbEntityEvent $event
   *   The event.
   */
  abstract public function onEntityInsert(Neo4jDbEntityEvent $event);

  /**
   * Method called when entity is updated.
   *
   * @param \Drupal\neo4j_db_entity\Event\Neo4jDbEntityEvent $event
   *   The event.
   */
  abstract public function onEntityUpdate(Neo4jDbEntityEvent $event);

  /**
   * Method called when entity is deleted.
   *
   * @param \\Drupal\neo4j_db_entity\Event\Neo4jDbEntityEvent $event
   *   The event.
   */
  abstract public function onEntityDelete(Neo4jDbEntityEvent $event);

  /**
   * Method called when entity is loaded.
   *
   * @param \\Drupal\neo4j_db_entity\Event\Neo4jDbEntityEvent $event
   *   The event.
   */
  abstract public function onEntityLoad(Neo4jDbEntityEvent $event);

  /**
   * Method called when entity is viewed.
   *
   * @param \\Drupal\neo4j_db_entity\Event\Neo4jDbEntityEvent $event
   *   The event.
   */
  abstract public function onEntityView(Neo4jDbEntityEvent $event);
}
