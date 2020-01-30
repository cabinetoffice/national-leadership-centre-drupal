<?php

namespace Drupal\neo4j_db\Model;

interface GraphModelInterface {

  /**
   * Discover a named model object.
   *
   * @param $name
   *
   * @return \Drupal\neo4j_db\Model\ModelInterface
   */
  public function discover($name);

}
