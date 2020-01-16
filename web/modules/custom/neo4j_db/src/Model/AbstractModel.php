<?php

namespace Drupal\neo4j_db\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * Class AbstractModel
 *
 * @package Drupal\neo4j_db\Model
 */
abstract class AbstractModel {
  /**
   * @var int
   *
   * @OGM\GraphId()
   */
  protected $id;

  /**
   * @var string
   *
   * @OGM\Property(type="string")
   */
  protected $name;

  /**
   * @var int
   *
   * @OGM\Property(type="int")
   */
  protected $created;

  /**
   * @var int
   *
   * @OGM\Property(type="int")
   */
  protected $updated;

  /**
   * @return int
   */
  public function id()
  {
    return $this->id;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }

}
