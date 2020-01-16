<?php

namespace Drupal\neo4j_db\Database;

use Drupal\Core\Database\Log;
use GraphAware\Neo4j\OGM\EntityManager;

/**
 * @addtogroup database
 * @{
 */

/**
 * Neo4j implementation, like \Drupal\Core\Database\Connection but not PDO.
 */
abstract class Connection {

  /**
   * Neo4j network connection protocol, 'bolt' by default.
   *
   * @var string
   */
  protected $protocol = 'bolt';

  /**
   * The actual graph DB connection.
   *
   * @var \GraphAware\Neo4j\OGM\EntityManager
   */
  protected $ogmConnection;

  /**
   * The connection information for this connection object.
   *
   * @var array
   */
  protected $connectionOptions = [];

  /**
   * Index of what driver-specific class to use for various operations.
   *
   * @var array
   */
  protected $driverClasses = [];

  /**
   * Constructs a Connection object.
   *
   * @param \GraphAware\Neo4j\OGM\EntityManager $connection
   *   An object of the Neo4j OGM EntityManager class representing a database connection.
   * @param array $connection_options
   *   An array of options for the connection. May include the following:
   *   - prefix
   *   - namespace
   *   - Other driver-specific options.
   */
  public function __construct(EntityManager $connection, array $connection_options) {
    $this->ogmConnection = $connection;
    $this->connectionOptions = $connection_options;
  }


  /**
   * {@inheritdoc}
   */
  public static function open(array &$connection_options = []) {
    $connection_options['port'] = empty($connection_options['port']) ? 7687 : $connection_options['port'];
    $uri = "bolt://{$connection_options['username']}:{$connection_options['password']}@{$connection_options['host']}:{$connection_options['port']}";
    $client = EntityManager::create($uri);
    return $client;
  }

  /**
   * Gets the driver-specific override class if any for the specified class.
   *
   * @param string $class
   *   The class for which we want the potentially driver-specific class.
   * @return string
   *   The name of the class that should be used for this driver.
   */
  public function getDriverClass($class) {
    if (empty($this->driverClasses[$class])) {
      if (empty($this->connectionOptions['namespace'])) {
        // Fallback for Drupal 7 settings.php and the test runner script.
        $this->connectionOptions['namespace'] = (new \ReflectionObject($this))->getNamespaceName();
      }
      $driver_class = $this->connectionOptions['namespace'] . '\\' . $class;
      $this->driverClasses[$class] = class_exists($driver_class) ? $driver_class : $class;
    }
    return $this->driverClasses[$class];
  }

  /**
   * @return \GraphAware\Neo4j\OGM\EntityManager
   */
  public function getOgmConnection() {
    return $this->ogmConnection;
  }

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
   * Tells this connection object what its target value is.
   *
   * This is needed for logging and auditing. It's sloppy to do in the
   * constructor because the constructor for child classes has a different
   * signature. We therefore also ensure that this function is only ever
   * called once.
   *
   * @param string $target
   *   (optional) The target this connection is for.
   */
  public function setTarget($target = NULL) {
    if (!isset($this->target)) {
      $this->target = $target;
    }
  }

  /**
   * Returns the target this connection is associated with.
   *
   * @return string|null
   *   The target string of this connection, or NULL if no target is set.
   */
  public function getTarget() {
    return $this->target;
  }

  /**
   * Tells this connection object what its key is.
   *
   * @param string $key
   *   The key this connection is for.
   */
  public function setKey($key) {
    if (!isset($this->key)) {
      $this->key = $key;
    }
  }

  /**
   * Returns the key this connection is associated with.
   *
   * @return string|null
   *   The key of this connection, or NULL if no key is set.
   */
  public function getKey() {
    return $this->key;
  }

  /**
   * Associates a logging object with this connection.
   *
   * @param \Drupal\Core\Database\Log $logger
   *   The logging object we want to use.
   */
  public function setLogger(Log $logger) {
    $this->logger = $logger;
  }

  /**
   * Gets the current logging object for this connection.
   *
   * @return \Drupal\Core\Database\Log|null
   *   The current logging object for this connection. If there isn't one,
   *   NULL is returned.
   */
  public function getLogger() {
    return $this->logger;
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
