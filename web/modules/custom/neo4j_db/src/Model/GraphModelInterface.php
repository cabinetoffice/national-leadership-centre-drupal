<?php

namespace Drupal\neo4j_db\Model;

interface GraphModelInterface {

  /**
   * Persist a model object in the graph database.
   *
   * @return void
   */
  public function modelPersist();
}
