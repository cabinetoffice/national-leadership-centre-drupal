<?php


namespace Drupal\neo4j_db\Database\Query;

use Drupal\neo4j_db\Database\Connection;

/**
 * General class for an abstracted graph DB FindOneBy operation.
 *
 * @ingroup graph_database
 */
class FindOneBy extends AbstractFindBy {

  /**
   * @var array
   */
  protected $criteria;

  /**
   * FindOneBy constructor.
   *
   * @param \Drupal\neo4j_db\Database\Connection $connection
   *   Database connection object.
   * @param string $className
   *   The class name of the model object to retrieve.
   * @param array $criteria
   *   An array of criteria to help retrieve the right object.
   */
  public function __construct(Connection $connection, $className, $criteria) {
    parent::__construct($connection, $className);
    $this->criteria = $criteria;
  }

  /**
   * @return object|null
   */
  public function execute() {
    return $this->typeRepo->findOneBy($this->criteria);
  }
}
