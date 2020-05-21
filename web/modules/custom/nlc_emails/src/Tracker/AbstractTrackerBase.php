<?php

namespace Drupal\nlc_emails\Tracker;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Database\Connection;
use Drupal\nlc_emails\Emails\Email;
use Drupal\nlc_emails\Emails\NlcEmailHandlerInterface;
use Drupal\nlc_emails\LoggerTrait;
use Drupal\nlc_emails\Utility\Utility;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractTrackerBase implements TrackerInterface {

  use LoggerTrait;

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
    $select->condition('machine_name', $this->getHandler()->emailHandlerMachineName());
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
      ->fields(['machine_name', 'datasource', 'item_id', 'changed', 'sent', 'status', 'uid', 'email']);
  }

  /**
   * Creates an UPDATE statement for this tracker.
   *
   * @return \Drupal\Core\Database\Query\Update
   *   An UPDATE statement.
   */
  protected function createUpdateStatement() {
    return $this->getDatabaseConnection()->update('nlc_emails_item')
      ->condition('machine_name', $this->getHandler()->emailHandlerMachineName());
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
   * Creates a SELECT statement which filters on the not indexed items.
   *
   * @param string|null $datasource_id
   *   (optional) If specified, only items of the datasource with that ID are
   *   retrieved.
   *
   * @return \Drupal\Core\Database\Query\SelectInterface
   *   A SELECT statement.
   */
  protected function createRemainingItemsStatement($datasource_id = NULL) {
    $select = $this->createSelectStatement();
    $select->fields('nei', ['machine_name', 'datasource', 'item_id', 'changed', 'sent', 'status', 'uid', 'email']);
    if ($datasource_id) {
      $select->condition('datasource', $datasource_id);
    }
    $select->condition('nei.status', $this::STATUS_NOT_SENT, '=');
    // Use the same direction for both sorts to avoid performance problems.
    $order = 'ASC';
    $select->orderBy('nei.changed', $order);
    // Add a secondary sort on item ID to make the order completely predictable.
    $select->orderBy('nei.item_id', $order);

    return $select;
  }

  /**
   * {@inheritdoc}
   */
  public function trackEmailsInserted(array $emails) {
    $transaction = $this->getDatabaseConnection()->startTransaction();
//    print_r(array_keys($emails));
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
          /** @var \Drupal\nlc_emails\Emails\Email $email */
          $email = $emails[$item_id];
          $insert->values([
            'machine_name' => $machine_name,
            'datasource' => $email->getDatasource(),
            'item_id' => $item_id,
            'changed' => $this->getTimeService()->getRequestTime(),
            'sent' => null,
            'status' => $this::STATUS_NOT_SENT,
            'uid' => $email->getUid(),
            'email' => $email->getEmail(),
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

  /**
   * {@inheritdoc}
   */
  public function trackItemsUpdated(array $emails = NULL) {
    $transaction = $this->getDatabaseConnection()->startTransaction();
    try {
      // Process the IDs in chunks so we don't create an overly large UPDATE
      // statement.
      $ids_chunks = ($emails !== NULL ? array_chunk($emails, 1000) : [NULL]);
      foreach ($ids_chunks as $ids_chunk) {
        $update = $this->createUpdateStatement();
        $update->fields([
          'changed' => $this->getTimeService()->getRequestTime(),
          'status' => $this::STATUS_NOT_SENT,
        ]);
        if ($ids_chunk) {
          $update->condition('item_id', $ids_chunk, 'IN');
        }
        $update->execute();
      }
    }
    catch (\Exception $e) {
      $this->logException($e);
      $transaction->rollBack();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function trackAllItemsUpdated($machine_namme) {
    $transaction = $this->getDatabaseConnection()->startTransaction();
    try {
      $update = $this->createUpdateStatement();
      $update->fields([
        'changed' => $this->getTimeService()->getRequestTime(),
        'status' => $this::STATUS_NOT_SENT,
      ]);
      if ($machine_namme) {
        $update->condition('machine_namme', $machine_namme);
      }
      $update->execute();
    }
    catch (\Exception $e) {
      $this->logException($e);
      $transaction->rollBack();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function trackEmailsSent(array $ids) {
    $transaction = $this->getDatabaseConnection()->startTransaction();
    try {
      // Process the IDs in chunks so we don't create an overly large UPDATE
      // statement.
      $ids_chunks = array_chunk($ids, 1000);
      foreach ($ids_chunks as $ids_chunk) {
        $update = $this->createUpdateStatement();
        $update->fields([
          'changed' => $this->getTimeService()->getRequestTime(),
          'sent' => $this->getTimeService()->getRequestTime(),
          'status' => $this::STATUS_SENT,
        ]);
        $update->condition('item_id', $ids_chunk, 'IN');
        $update->execute();
      }
    }
    catch (\Exception $e) {
      $this->logException($e);
      $transaction->rollBack();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getRemainingItems($limit = -1, $datasource_id = NULL) {
    try {
      $select = $this->createRemainingItemsStatement($datasource_id);
      if ($limit >= 0) {
        $select->range(0, $limit);
      }
      return $select->execute()->fetchAll();
    }
    catch (\Exception $e) {
      $this->logException($e);
      return [];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getRemainingItemsCount(?string $datasource = NULL) {
    try {
      $select = $this->createRemainingItemsStatement();
      if ($datasource) {
        $select->condition('datasource', $datasource);
      }
      return (int) $select->countQuery()->execute()->fetchField();
    }
    catch (\Exception $e) {
      $this->logException($e);
      return 0;
    }
  }

}
