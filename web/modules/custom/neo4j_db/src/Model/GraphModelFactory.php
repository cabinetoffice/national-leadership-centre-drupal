<?php

namespace Drupal\neo4j_db\Model;

/**
 * Factory to create graph model objects.
 *
 * @package Drupal\neo4j_db\Model
 */
class GraphModelFactory  {

  public function create() {
    return new GraphModel();
  }
}
