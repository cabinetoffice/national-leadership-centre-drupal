<?php

namespace Drupal\nlc_network_individual\EventSubscriber;

use Drupal\Core\Session\AccountInterface;
use Drupal\neo4j_db_entity\EventSubscriber\AbstractEntityEventViewSubscriber;
use Drupal\neo4j_db_entity\Event\Neo4jDbEntityEvent;

class NetworkIndividualUserViewSubscriber extends AbstractEntityEventViewSubscriber {

  /**
   * @var AccountInterface
   */
  protected $currentUser;

  /**
   * @var \Drupal\user\UserInterface
   */
  protected $account;

  public function __construct(AccountInterface $current_user) {
    $this->currentUser = $current_user;
  }

  public function onEntityView(Neo4jDbEntityEvent $event) {
    $this->account = $event->getEntity();
  }

}
