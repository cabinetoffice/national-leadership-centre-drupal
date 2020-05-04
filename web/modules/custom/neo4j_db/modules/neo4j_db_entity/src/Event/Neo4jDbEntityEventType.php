<?php

namespace Drupal\neo4j_db_entity\Event;

/**
 * Enumeration of entity event types.
 */
class Neo4jDbEntityEventType {
  const INSERT = 'event.insert';
  const UPDATE = 'event.update';
  const DELETE = 'event.delete';
  const LOAD = 'event.load';
  const VIEW = 'event.view';
}
