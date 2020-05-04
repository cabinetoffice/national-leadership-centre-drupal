<?php

namespace Drupal\nlc_network_individual\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\neo4j_db\EventSubscriber\AbstractEntityEventLoadSubscriber;
use Drupal\neo4j_db_entity\Event\Neo4jDbEntityEvent;
use Drupal\neo4j_db_entity\Event\Neo4jDbEntityEventType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NetworkIndividualLoadSubscriber extends AbstractEntityEventLoadSubscriber {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  public function onEntityLoad(Neo4jDbEntityEvent $event) {
    // TODO: Implement onEntityLoad() method.
  }

}
