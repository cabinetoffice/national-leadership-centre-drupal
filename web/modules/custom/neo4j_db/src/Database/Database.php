<?php

namespace Drupal\neo4j_db\Database;

use Drupal\Core\Database\ConnectionNotDefinedException;
use \Drupal\Core\Database\Database as CoreDatabase;
use Drupal\Core\Database\DriverNotSpecifiedException;
use Drupal\Core\Database\Log;

/**
 * Primary front-controller for the graph database system.
 *
 * This class is uninstantiatable and un-extendable. It acts to encapsulate
 * all control and shepherding of graph database connections into a single
 * location without the use of globals.
 */
abstract class Database {

  /**
   * The database target this connection is for.
   *
   * We need this information for later auditing and logging.
   *
   * @var string|null
   */
  protected $target = NULL;

  /**
   * The key representing this connection.
   *
   * The key is a unique string which identifies a database connection. A
   * connection can be a single server or a cluster of primary and replicas
   * (use target to pick between primary and replica).
   *
   * @var string|null
   */
  protected $key = NULL;

  /**
   * The current database logging object for this connection.
   *
   * @var \Drupal\Core\Database\Log|null
   */
  protected $logger = NULL;

  /**
   * A nested array of all active connections. It is keyed by database name
   * and target.
   *
   * @var array
   */
  protected static $connections = [];

  /**
   * A processed copy of the database connection information from settings.php.
   *
   * @var array
   */
  protected static $databaseInfo = [];

  /**
   * The key of the currently active database connection.
   *
   * @var string
   */
  protected static $activeKey = 'graph';

  /**
   * An array of active query log objects.
   *
   * Every connection has one and only one logger object for all targets and
   * logging keys.
   *
   * array(
   *   '$db_key' => DatabaseLog object.
   * );
   *
   * @var array
   */
  protected static $logs = [];

  /**
   * A list of key/target credentials to simply ignore.
   *
   * @var array
   */
  protected static $ignoreTargets = [];


  /**
   * Starts logging a given logging key on the specified connection.
   *
   * @param string $logging_key
   *   The logging key to log.
   * @param string $key
   *   The database connection key for which we want to log.
   *
   * @return \Drupal\Core\Database\Log
   *   The query log object. Note that the log object does support richer
   *   methods than the few exposed through the Database class, so in some
   *   cases it may be desirable to access it directly.
   *
   * @see \Drupal\Core\Database\Log
   */
  final public static function startLog($logging_key, $key = 'graph') {
    if (empty(self::$logs[$key])) {
      self::$logs[$key] = new Log($key);

      // Every target already active for this connection key needs to have the
      // logging object associated with it.
      if (!empty(self::$connections[$key])) {
        foreach (self::$connections[$key] as $connection) {
          $connection->setLogger(self::$logs[$key]);
        }
      }
    }

    self::$logs[$key]->start($logging_key);
    return self::$logs[$key];
  }

  /**
   * Gets the connection object for the specified database key and target.
   *
   * @param string $target
   *   The database target name.
   * @param string $key
   *   The database connection key. Defaults to NULL which means the active key.
   *
   * @return \Drupal\neo4j_db\Database\Driver\bolt\Connection
   *   The corresponding connection object.
   */
  final public static function getConnection($target = 'default', $key = NULL) {
    if (!isset($key)) {
      // By default, we want the active connection, set in setActiveConnection.
      $key = self::$activeKey;
    }

    // Make sure the database info is set.
    if (empty(self::$databaseInfo)) {
      self::$databaseInfo = self::getAllConnectionInfo();
    }

    // If the requested target does not exist, or if it is ignored, we fall back
    // to the default target. The target is typically either "default" or
    // "replica", indicating to use a replica SQL server if one is available. If
    // it's not available, then the default/primary server is the correct server
    // to use.
    if (!empty(self::$ignoreTargets[$key][$target]) || !isset(self::$databaseInfo[$key][$target])) {
      $target = 'default';
    }

    if (!isset(self::$connections[$key][$target])) {
      // If necessary, a new connection is opened.
      self::$connections[$key][$target] = self::openConnection($key, $target);
    }
    return self::$connections[$key][$target];
  }

  /**
   * Opens a connection to the server specified by the given key and target.
   *
   * @param string $key
   *   The database connection key, as specified in settings.php. The default is
   *   "default".
   * @param string $target
   *   The database target to open.
   *
   * @return
   *
   * @throws \Drupal\Core\Database\ConnectionNotDefinedException
   * @throws \Drupal\Core\Database\DriverNotSpecifiedException
   */
  final protected static function openConnection($key, $target) {
    // If the requested database does not exist then it is an unrecoverable
    // error.
    if (!isset(self::$databaseInfo[$key])) {
      exit;
      throw new ConnectionNotDefinedException('The specified database connection is not defined: ' . $key);
    }

    if (!$driver = self::$databaseInfo[$key][$target]['driver']) {
      throw new DriverNotSpecifiedException('Driver not specified for this database connection: ' . $key);
    }

    $namespace = static::getDatabaseDriverNamespace(self::$databaseInfo[$key][$target]);
    $driver_class = $namespace . '\\Connection';

    $connection = $driver_class::open(self::$databaseInfo[$key][$target]);
    $new_connection = new $driver_class($connection, self::$databaseInfo[$key][$target]);
    $new_connection->setTarget($target);
    $new_connection->setKey($key);

    // If we have any active logging objects for this connection key, we need
    // to associate them with the connection we just opened.
    if (!empty(self::$logs[$key])) {
      $new_connection->setLogger(self::$logs[$key]);
    }

    return $new_connection;
  }


  /**
   * Gets information on the specified graph database connection.
   *
   * @param string $key
   *   (optional) The connection key for which to return information.
   *
   * @return array|null
   */
  final public static function getConnectionInfo($key = 'graph') {
    return CoreDatabase::getConnectionInfo($key);
  }

  /**
   * Gets connection information for all available databases.
   *
   * @return array
   */
  final public static function getAllConnectionInfo() {
    return CoreDatabase::getAllConnectionInfo();
  }


  /**
   * Gets the PHP namespace of a database driver from the connection info.
   *
   * @param array $connection_info
   *   The database connection information, as defined in settings.php. The
   *   structure of this array depends on the database driver it is connecting
   *   to.
   *
   * @return string
   *   The PHP namespace of the driver's database.
   */
  protected static function getDatabaseDriverNamespace(array $connection_info) {
    if (isset($connection_info['namespace'])) {
      return $connection_info['namespace'];
    }
    // Fallback for Drupal 7 settings.php.
    return 'Drupal\\Core\\Database\\Driver\\' . $connection_info['driver'];
  }
}
