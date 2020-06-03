<?php

namespace Drupal\neo4j_db\Database\Query;

use Drupal\neo4j_db\Database\Connection;
use Drupal\neo4j_db\Database\Database;

/**
 * Base class for graph query builders.
 */
abstract class Query {
  /**
   * The connection object on which to run this query.
   *
   * @var \Drupal\neo4j_db\Database\Connection
   */
  protected $connection;

  /**
   * The target of the connection object.
   *
   * @var string
   */
  protected $connectionTarget;

  /**
   * The key of the connection object.
   *
   * @var string
   */
  protected $connectionKey;

  /**
   * Constructs a Query object.
   *
   * @param \Drupal\neo4j_db\Database\Connection $connection
   *   Database connection object.
   */
  public function __construct(Connection $connection) {
    $this->connection = $connection;
    $this->connectionKey = $this->connection->getKey();
    $this->connectionTarget = $this->connection->getTarget();
  }

  /**
   * Implements the magic __sleep function to disconnect from the database.
   */
  public function __sleep() {
    $keys = get_object_vars($this);
    unset($keys['connection']);
    return array_keys($keys);
  }

  /**
   * Implements the magic __wakeup function to reconnect to the database.
   */
  public function __wakeup() {
    $this->connection = Database::getConnection($this->connectionTarget, $this->connectionKey);
  }

  /**
   * Runs the query against the database.
   */
  abstract public function execute();

}
