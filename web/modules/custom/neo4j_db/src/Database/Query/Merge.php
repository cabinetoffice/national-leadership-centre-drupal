<?php


namespace Drupal\neo4j_db\Database\Query;

use Drupal\neo4j_db\Database\Connection;

class Merge extends Query {

  /**
   * The object to handle in the query.
   *
   * @var object
   */
  protected $object;

  /**
   * Merge constructor.
   *
   * @param \Drupal\neo4j_db\Database\Connection $connection
   *   A Neo4j database connection.
   * @param object $object
   *   The object to merge in the DB.
   *
   * @throws \Exception
   */
  public function __construct(Connection $connection, $object) {
    parent::__construct($connection);
    $this->object = $object;
  }

  /**
   * Merge the object in the current Unit of Work.
   *
   * @return \Drupal\neo4j_db\Database\Query\Merge
   */
  public function merge() {
    $this->connection->getOgmConnection()->merge($this->object);
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function execute() {
    $this->merge();
    $this->connection->flush();
  }

}
