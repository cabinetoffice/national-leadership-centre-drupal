<?php

namespace Drupal\neo4j_db\Database\Query;

use Drupal\neo4j_db\Database\Connection;

class Persist extends Query {

  /**
   * The object to handle in the query.
   *
   * @var object
   */
  protected $object;

  /**
   * Persist constructor.
   *
   * @param \Drupal\neo4j_db\Database\Connection $connection
   *   A Neo4j database connection.
   * @param object $object
   *   The object to persist to the DB.
   *
   * @throws \Exception
   */
  public function __construct(Connection $connection, $object) {
    parent::__construct($connection);
    $this->object = $object;
    $this->persist();
  }

  /**
   * Persist an object in Neo4j.
   *
   * @throws \Exception
   */
  private function persist() {
    $this->connection->getOgmConnection()->persist($this->object);
  }

  /**
   * return void
   */
  public function execute() {
    $this->connection->flush();
  }

}
