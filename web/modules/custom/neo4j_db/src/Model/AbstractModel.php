<?php

namespace Drupal\neo4j_db\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * Class AbstractModel
 *
 * @package Drupal\neo4j_db\Model
 */
abstract class AbstractModel implements ModelInterface {

  /**
   * @var string
   */
  protected $drupalEntity;

  /**
   * @var string
   */
  protected $drupalBundle;

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
   * AbstractModel constructor.
   *
   * @throws \Drupal\neo4j_db\Model\GraphModelException
   */
  public function __construct() {
    if (!$this->drupalEntity) {
      throw new GraphModelException('Model object is missing $drupalEntity parameter');
    }
    if ($this->drupalEntity && !$this->drupalBundle) {
      throw new GraphModelException('Model object is missing $drupalBundle parameter');
    }
  }

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
