<?php

namespace Drupal\nlc_network_individual\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\neo4j_db_entity\Event\Neo4jDbEntityEvent;
use Drupal\neo4j_db_entity\Event\Neo4jDbEntityEventType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NetworkIndividualLoadSubscriber implements EventSubscriberInterface {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Constructs a new NetworkIndividualViewSubscriber instance.
   *
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   *
   */
  public function __construct(AccountInterface $current_user) {
    $this->currentUser = $current_user;
  }

  /**
   * @param \Drupal\Core\Routing\RouteBuildEvent $event
   *   The route build event.
   */
  public function onLoad(Neo4jDbEntityEvent $event) {

  }

  public static function getSubscribedEvents() {
    $events = [];

    $events[Neo4jDbEntityEventType::LOAD][] = ['onLoad'];

    return $events;
  }

}
