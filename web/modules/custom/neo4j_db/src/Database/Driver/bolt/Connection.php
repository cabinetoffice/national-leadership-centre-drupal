<?php

namespace Drupal\neo4j_db\Database\Driver\bolt;

use Drupal\Core\Database\Connection as DatabaseConnection;
use GraphAware\Neo4j\Client\ClientBuilder;

/**
 * @addtogroup database
 * @{
 */

/**
 * Neo4j implementation of \Drupal\Core\Database\Connection.
 */
class Connection extends DatabaseConnection {

  /**
   * Neo4j network connection protocol.
   *
   * @var string
   */
  protected $protocol = 'bolt';

  /**
   * {@inheritDoc}
   */
  public function driver() {
    return 'neo4j';
  }

  /**
   * {@inheritDoc}
   *
   * @return string
   */
  public function databaseType() {
    return 'neo4j';
  }

  /**
   * {@inheritdoc}
   */
  public static function open(array &$connection_options = []) {
    $connection_options['port'] = empty($connection_options['port']) ? 7687 : $connection_options['port'];
    $uri = "bolt://{$connection_options['username']}:{$connection_options['password']}@{$connection_options['host']}:{$connection_options['port']}";
    $client = ClientBuilder::create()
      ->addConnection('bolt', $uri)
      ->build();
    return $client;
  }

  /**
   * {@inheritDoc}
   */
  public function createDatabase($database) {
    // TODO: Implement createDatabase() method.
  }

  /**
   * {@inheritDoc}
   */
  public function nextId($existing_id = 0) {
    // TODO: Implement nextId() method.
  }

  /**
   * {@inheritDoc}
   */
  public function queryRange($query, $from, $count, array $args = [], array $options = []) {
    // TODO: Implement queryRange() method.
  }

  /**
   * {@inheritDoc}
   */
  public function queryTemporary($query, array $args = [], array $options = []) {
    // TODO: Implement queryTemporary() method.
  }

  /**
   * {@inheritDoc}
   */
  public function mapConditionOperator($operator) {
    // TODO: Implement mapConditionOperator() method.
  }

}

/**
 * @} End of "addtogroup database".
 */
