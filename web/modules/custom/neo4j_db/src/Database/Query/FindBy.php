<?php


namespace Drupal\neo4j_db\Database\Query;

use Drupal\neo4j_db\Database\Connection;

/**
 * General class for an abstracted graph DB FindBy operation.
 *
 * @ingroup graph_database
 */
class FindBy extends AbstractFindBy {

  /**
   * @var array
   */
  protected $criteria;

  /**
   * @var array|null
   */
  protected $orderBy;

  /**
   * @var int|null
   */
  protected $limit;

  /**
   * @var int|null
   */
  protected $offset;

  /**
   * FindBy constructor.
   *
   * @param \Drupal\neo4j_db\Database\Connection $connection
   *   Database connection object.
   * @param string $className
   *   The class name of the model object to retrieve.
   * @param array $criteria
   *   An array of criteria to help retrieve the right object.
   * @param array|null $orderBy
   *   An array of parameters by which to order.
   * @param int|null $limit
   *   Limit the number of results.
   * @param int|null $offset
   *   A results offset.
   */
  public function __construct(Connection $connection, $className, array $criteria, array $orderBy = null, $limit = null, $offset = null) {
    parent::__construct($connection, $className);
    $this->criteria = $criteria;
    $this->orderBy = $orderBy;
    $this->limit = $limit;
    $this->offset = $offset;
  }

  /**
   * @return array
   */
  protected function execute() {
    return $this->typeRepo->findBy($this->criteria, $this->orderBy, $this->limit, $this->offset);
  }

  /**
   * @param array $criteria
   */
  public function setCriteria($criteria) {
    $this->criteria = $criteria;
  }

  /**
   * @param array|null $orderBy
   */
  public function setOrderBy($orderBy) {
    $this->orderBy = $orderBy;
  }

  /**
   * @param int|null $limit
   */
  public function setLimit($limit) {
    $this->limit = $limit;
  }

  /**
   * @param int|null $offset
   */
  public function setOffset($offset) {
    $this->offset = $offset;
  }

}
