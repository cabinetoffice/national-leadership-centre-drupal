<?php

namespace Drupal\neo4j_db;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\neo4j_db\Database\Driver\bolt\Connection;
use Drupal\neo4j_db\Model\GraphModelInterface;
use Drupal\neo4j_db\Model\LogEventModel;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class Logger implements EventSubscriberInterface {

  /**
   * @var \Drupal\neo4j_db\Database\Driver\bolt\Connection
   */
  protected $connection;

  /**
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * @var \Psr\Log\LoggerInterface
   */
  protected $log;

  /**
   * @var \Drupal\neo4j_db\Model\GraphModelInterface
   */
  protected $graphModel;

  public function __construct(Connection $connection, AccountProxyInterface $currentUser, LoggerInterface $loggerFactory, GraphModelInterface $graphModel) {
    $this->connection = $connection;
    $this->currentUser = $currentUser;
    $this->log = $loggerFactory;
    $this->graphModel = $graphModel;
  }

  public function onRequest(KernelEvent $event) {
    // Create an event log in the graph DB.
//    $model = new LogEventModel();
//    $model->setId(20);
//    $model->setName('Test');
//    $model->setEvent(\Drupal::time()->getRequestTime());
//    $this->connection
//      ->persist($model)
//      ->execute();

    // Update an existing event.
//    $className = LogEventModel::class;
//    $params = ['event' => 1579190443];
//    $logEvent = $this->connection
//      ->findOneBy($className, $params)
//      ->execute();
//    $logEvent->setName('My New Test');
//    $this->connection->flush();
//    $this->log->debug($this->graphModel->discover('LogEvent'));
  }

  public static function getSubscribedEvents() {
    $events = [];

    $events[KernelEvents::REQUEST][] = ['onRequest', -50];

    return $events;
  }

}
