<?php

namespace Drupal\neo4j_db\Database\Query;

class Persist extends Query {

  /**
   * Persist an object in Neo4j.
   *
   * @throws \Exception
   */
  public function persist() {
    $this->connection->getOgmConnection()->persist($this->object);
  }

  /**
   * return void
   */
  public function execute() {
    $this->connection->getOgmConnection()->flush();
  }

}
