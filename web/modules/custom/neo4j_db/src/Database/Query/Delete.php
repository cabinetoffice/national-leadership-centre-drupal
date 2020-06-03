<?php

namespace Drupal\neo4j_db\Database\Query;

use Drupal\neo4j_db\Database\Connection;

/**
 * General class for an abstracted graph DB Delete operation.
 *
 * @ingroup graph_database
 */
class Delete extends Query {

  /**
   * The object to handle in the query.
   *
   * @var object
   */
  protected $object;

  /**
   * @var boolean
   */
  protected $detachRelationships = false;

  /**
   * Delete constructor.
   *
   * @param \Drupal\neo4j_db\Database\Connection $connection
   *   A Neo4j database connection.
   * @param object $object
   *   The object to delete from the DB.
   *
   * @throws \Exception
   */
  public function __construct(Connection $connection, $object) {
    parent::__construct($connection);
    $this->object = $object;
  }

  /**
   * Set the flag to detach relationships when deleting.
   *
   * @return \Drupal\neo4j_db\Database\Query\Delete
   */
  public function detachRelationships() {
    $this->detachRelationships = true;
    return $this;
  }

  /**
   * Remove the object from the current Unit of Work.
   *
   * @return \Drupal\neo4j_db\Database\Query\Delete
   */
  public function remove() {
    $this->connection->getOgmConnection()->remove($this->object, $this->detachRelationships);
    return $this;
  }

  /**
   * Perform the remove action from the Unit of Work and push to the database.
   *
   * @return void
   */
  public function execute() {
    $this->connection->flush();
  }

}
