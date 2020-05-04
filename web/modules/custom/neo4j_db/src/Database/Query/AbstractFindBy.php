<?php


namespace Drupal\neo4j_db\Database\Query;

use Drupal\neo4j_db\Database\Connection;

abstract class AbstractFindBy extends Query {

  /**
   * @var string
   */
  protected $className;

  /**
   * @var \GraphAware\Neo4j\OGM\Repository\BaseRepository
   */
  protected $typeRepo;

  /**
   * AbstractFindBy constructor.
   *
   * @param \Drupal\neo4j_db\Database\Connection $connection
   *   Database connection object.
   * @param string $className
   *   The class name of the model object to retrieve.
   */
  public function __construct(Connection $connection, $className) {
    parent::__construct($connection);
    $this->className = $className;
    $this->typeRepo = $this->setRepository();
  }

  /**
   * @return \GraphAware\Neo4j\OGM\Repository\BaseRepository
   */
  protected function setRepository() {
    return $this->connection->getOgmConnection()->getRepository($this->className);
  }

}
