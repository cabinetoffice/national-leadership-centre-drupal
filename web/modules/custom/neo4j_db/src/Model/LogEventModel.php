<?php

namespace Drupal\neo4j_db\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * A Logged Event model object
 *
 * @package Drupal\neo4j_db\Model
 *
 * @OGM\Node(label="LogEvent")
 */
class LogEventModel extends AbstractModel {

  protected $drupalEntity = NULL;

  protected $drupalBundle = NULL;

  /**
   * @var string
   *
   * @OGM\Property(type="string")
   */
  protected $event;

  /**
   * @param string $event
   */
  public function setEvent($event) {
    $this->event = $event;
  }

  /**
   * @return string
   */
  public function getEvent() {
    return $this->event;
  }
}
