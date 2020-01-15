<?php

namespace Drupal\neo4j_db;

use Drupal\neo4j_db\Database\Database;

class DatabaseFactory {

  /**
   * @return \Drupal\neo4j_db\Database\Driver\bolt\Connection
   */
  public function create() {
    $connection = Database::getConnection();
    return $connection;
  }

}
