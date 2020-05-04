<?php

namespace Drupal\neo4j_db\Model;

interface ModelInterface {

  /**
   * Get the ID of this data model object.
   *
   * @return int
   */
  public function id();

  /**
   * Get the name parameter of the data model object.
   *
   * @return string
   */
  public function getName();

  /**
   * Set the name parameter of the data model object.
   *
   * @param string $name
   */
  public function setName($name);

}
