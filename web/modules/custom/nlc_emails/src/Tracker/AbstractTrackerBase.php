<?php

namespace Drupal\nlc_emails\Tracker;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Database\Connection;
use Drupal\nlc_emails\Emails\NlcEmailHandlerInterface;
use Drupal\nlc_emails\Utility\Utility;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractTrackerBase implements TrackerInterface {

  /**
   * Status value that represents emails that have been sent in their latest form.
   */
  const STATUS_SENT = 0;

  /**
   * Status value that represents emails that still need to be sent.
   */
  const STATUS_NOT_SENT = 1;

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
   * @var NlcEmailHandlerInterface
   */
  protected $handler;

  public function __construct(NlcEmailHandlerInterface $handler) {
    $this->setHandler($handler);
  }

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
   * {@inheritdoc}
   */
  public function getHandler() {
    return $this->handler;
  }

  /**
   * {@inheritdoc}
   */
  public function setHandler(NlcEmailHandlerInterface $handler) {
    $this->handler = $handler;
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


  /**
   * Creates a SELECT statement for this tracker.
   *
   * @return \Drupal\Core\Database\Query\SelectInterface
   *   A SELECT statement.
   */
  protected function createSelectStatement() {
    $select = $this->getDatabaseConnection()->select('nlc_emails_item', 'nei');
    $select->condition('machine_name', $this->getHandler()->);
    return $select;
  }

  /**
   * Creates an INSERT statement for this tracker.
   *
   * @return \Drupal\Core\Database\Query\Insert
   *   An INSERT statement.
   */
  protected function createInsertStatement() {
    return $this->getDatabaseConnection()->insert('nlc_emails_item')
      ->fields(['machine_name', 'item_id', 'sent', 'status', 'uid', 'email']);
  }

  /**
   * Creates an UPDATE statement for this tracker.
   *
   * @return \Drupal\Core\Database\Query\Update
   *   An UPDATE statement.
   */
  protected function createUpdateStatement() {
    return $this->getDatabaseConnection()->update('nlc_emails_item')
      ->condition('machine_name', $this->getHandler()->emailHandlerMachineName())
  }

  /**
   * Creates a DELETE statement for this tracker.
   *
   * @return \Drupal\Core\Database\Query\Delete
   *   A DELETE Statement.
   */
  protected function createDeleteStatement() {
    return $this->getDatabaseConnection()->delete('nlc_emails_item')
      ->condition('machine_name', $this->getHandler()->emailHandlerMachineName());
  }

  /**
   * {@inheritdoc}
   */
  public function trackItemsInserted(array $emails) {
    $transaction = $this->getDatabaseConnection()->startTransaction();
    try {
      $machine_name = $this->getHandler()->emailHandlerMachineName();
      // Process the IDs in chunks so we don't create an overly large INSERT
      // statement.
      $ids = array_keys($emails);
      foreach (array_chunk($ids, 1000) as $ids_chunk) {
        // We have to make sure we don't try to insert duplicate items.
        $select = $this->createSelectStatement()
          ->fields('nei', ['item_id']);
        $select->condition('item_id', $ids_chunk, 'IN');
        $existing = $select
          ->execute()
          ->fetchCol();
        $existing = array_flip($existing);

        $insert = $this->createInsertStatement();
        foreach ($ids_chunk as $item_id) {
          if (isset($existing[$item_id])) {
            continue;
          }
          $insert->values([
            'machine_name' => $machine_name,
            'item_id' => $item_id,
            'changed' => $this->getTimeService()->getRequestTime(),
            'status' => $this::STATUS_NOT_SENT,
            'uid' => $emails[$item_id]['uid'],
            'email' => $emails[$item_id]['email']
          ]);
        }
        if ($insert->count()) {
          $insert->execute();
        }
      }
    }
    catch (\Exception $e) {
      $this->logException($e);
      $transaction->rollBack();
    }
  }

}
