<?php


namespace Drupal\neo4j_db\Database\Query;

use Drupal\neo4j_db\Database\Connection;

class FindOneBy extends Query {

  /**
   * @var string
   */
  protected $className;

  /**
   * @var \GraphAware\Neo4j\OGM\Repository\BaseRepository
   */
  protected $typeRepo;

  /**
   * @var array
   */
  protected $params;

  /**
   * FindOneBy constructor.
   *
   * @param \Drupal\neo4j_db\Database\Connection $connection
   *   Database connection object.
   * @param string $className
   *   The class name of the model object to retrieve.
   * @param array $params
   *   An array of parameters to help retrieve the right object.
   */
  public function __construct(Connection $connection, $className, $params) {
    parent::__construct($connection);
    $this->className = $className;
    $this->typeRepo = $this->setRepository();
    $this->params = $params;
  }

  /**
   * @return \GraphAware\Neo4j\OGM\Repository\BaseRepository
   */
  protected function setRepository() {
    return $this->connection->getOgmConnection()->getRepository($this->className);
  }

  /**
   * @return object|null
   */
  public function execute() {
    return $this->typeRepo->findOneBy($this->params);
  }
}
