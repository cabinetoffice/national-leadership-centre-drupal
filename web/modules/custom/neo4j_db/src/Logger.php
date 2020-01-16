<?php

namespace Drupal\neo4j_db;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\neo4j_db\Database\Driver\bolt\Connection;
use Drupal\neo4j_db\Model\LogEventModel;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class Logger implements EventSubscriberInterface {

  /**
   * @var Connection
   */
  protected $connection;

  /**
   * @var AccountProxyInterface
   */
  protected $currentUser;

  /**
   * @var LoggerInterface
   */
  protected $log;

  public function __construct(Connection $connection, AccountProxyInterface $currentUser, LoggerInterface $loggerFactory) {
    $this->connection = $connection;
    $this->currentUser = $currentUser;
    $this->log = $loggerFactory;
  }

  public function onRequest(KernelEvent $event) {
    $model = new LogEventModel();
    $model->setName('Test');
    $model->setEvent(\Drupal::time()->getRequestTime());
    $this->connection
      ->persist($model)
      ->execute();
  }

  public static function getSubscribedEvents() {
    $events = [];

    $events[KernelEvents::REQUEST][] = ['onRequest', -50];

    return $events;
  }

}
