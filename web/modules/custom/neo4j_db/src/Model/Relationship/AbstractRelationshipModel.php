<?php

namespace Drupal\neo4j_db\Model\Relationship;

use GraphAware\Neo4j\OGM\Annotations as OGM;

abstract class AbstractRelationshipModel implements RelationshipModelInterface {

  /**
   * @OGM\GraphId()
   */
  protected $id;

  /**
   * @return mixed
   */
  public function id() {
    return $this->id;
  }

  /**
   * Persist this model to the graph DB.
   */
  public function modelPersist() {
    $this->connection
      ->persist($this)
      ->execute();
  }

}
