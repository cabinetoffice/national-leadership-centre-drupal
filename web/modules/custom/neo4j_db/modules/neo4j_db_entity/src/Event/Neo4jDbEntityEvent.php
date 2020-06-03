<?php


namespace Drupal\neo4j_db_entity\Event;

use Symfony\Component\EventDispatcher\Event;
use Drupal\Core\Entity\EntityInterface;

/**
 * Class to contain an entity event.
 */
class Neo4jDbEntityEvent extends Event {

  /**
   * The Entity.
   *
   * @var \Drupal\Core\Entity\EntityInterface
   */
  private $entity;

  /**
   * The event type.
   *
   * @var string
   */
  private $eventType;

  /**
   * Entity type ID.
   *
   * @var string
   */
  private $entityTypeId;

  /**
   * When viewing an entity, the entity view mode.
   *
   * @var string
   */
  private $entityViewMode;

  /**
   * Construct a new entity event.
   *
   * @param string $eventType
   *   The event type.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity which caused the event.
   * @param string $entityTypeId
   *   The type of entities being loaded (i.e. node, user, comment).
   * @param string $entityViewMode
   */
  public function __construct($eventType, EntityInterface $entity, $entityTypeId = null, $entityViewMode = null) {
    $this->eventType = $eventType;
    $this->entity = $entity;
    $this->entityTypeId = $entityTypeId;
  }

  /**
   * Get the entity from the event.
   *
   * @return EntityInterface
   */
  public function getEntity() {
    return $this->entity;
  }

  /**
   * Get the event type.
   *
   * @return string
   */
  public function getEventType() {
    return $this->eventType;
  }

  /**
   * Get the entity type ID.
   *
   * @return string
   */
  public function getEntityTypeId(): string {
    return $this->entityTypeId;
  }

  /**
   * Get the view mode when viewing an entity.
   *
   * @return string
   */
  public function getEntityViewMode(): string {
    return $this->entityViewMode;
  }

}
