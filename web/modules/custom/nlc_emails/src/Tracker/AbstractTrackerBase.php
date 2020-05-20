<?php

namespace Drupal\nlc_emails\Tracker;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractTrackerBase implements TrackerInterface {

  /**
   * The database connection used by this plugin.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * The time service.
   *
   * @var \Drupal\Component\Datetime\TimeInterface|null
   */
  protected $timeService;

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *
   * @return static
   */
  public static function create(ContainerInterface $container) {
    /** @var static $tracker */
    $tracker = new static();
    $tracker->setDatabaseConnection($container->get('database'));
    $tracker->setTimeService($container->get('datetime.time'));

    return $tracker;
  }


  /**
   * Retrieves the database connection.
   *
   * @return \Drupal\Core\Database\Connection
   *   The database connection used by this plugin.
   */
  public function getDatabaseConnection() {
    return $this->connection ?: \Drupal::database();
  }

  /**
   * Sets the database connection.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection to use.
   *
   * @return $this
   */
  public function setDatabaseConnection(Connection $connection) {
    $this->connection = $connection;
    return $this;
  }

  /**
   * Retrieves the time service.
   *
   * @return \Drupal\Component\Datetime\TimeInterface
   *   The time service.
   */
  public function getTimeService() {
    return $this->timeService ?: \Drupal::time();
  }

  /**
   * Sets the time service.
   *
   * @param \Drupal\Component\Datetime\TimeInterface $time_service
   *   The new time service.
   *
   * @return $this
   */
  public function setTimeService(TimeInterface $time_service) {
    $this->timeService = $time_service;
    return $this;
  }

}
