<?php


namespace Drupal\neo4j_db\Database\Query;

use Drupal\neo4j_db\Database\Connection;

/**
 * General class for an abstracted graph DB FindOneById operation.
 *
 * @ingroup graph_database
 */
class FindOneById extends AbstractFindBy {

  /**
   * @var int
   */
  protected $id;

  /**
   * FindOneById constructor.
   *
   * @param \Drupal\neo4j_db\Database\Connection $connection
   *   Database connection object.
   * @param string $className
   *   The class name of the model object to retrieve.
   * @param int $id
   *   The ID of the item to retrieve.
   */
  public function __construct(Connection $connection, $className, $id) {
    parent::__construct($connection, $className);
    $this->id = $id;
  }

  /**
   * @return object|null
   */
  public function execute() {
    return $this->typeRepo->findOneById($this->id);
  }

}
