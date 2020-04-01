<?php

namespace Drupal\nlc_network_individual\EventSubscriber;

use Drupal\Core\Session\AccountInterface;
use Drupal\neo4j_db_entity\EventSubscriber\AbstractEntityEventViewSubscriber;
use Drupal\neo4j_db_entity\Event\Neo4jDbEntityEvent;
use Drupal\neo4j_db_entity\Model\GraphEntityModelManagerInterface;
use Drupal\typed_data\Exception\InvalidArgumentException;
use Drupal\user\Entity\User;

class NetworkIndividualUserViewSubscriber extends AbstractEntityEventViewSubscriber {

  /**
   * @var \Drupal\user\UserInterface
   */
  protected $currentUser;

  /**
   * @var \Drupal\neo4j_db_entity\Model\GraphEntityModelInterface
   */
  protected $currentUserGraphModel;

  /**
   * @var \Drupal\neo4j_db_entity\Model\GraphEntityModelManagerInterface
   */
  protected $graphModelManager;

  /**
   * @var \Drupal\user\UserInterface
   */
  protected $account;

  /**
   * @var \Drupal\neo4j_db_entity\Model\GraphEntityModelInterface
   */
  protected $accountGraphModel;

  /**
   * NetworkIndividualUserViewSubscriber constructor.
   *
   * @param \Drupal\Core\Session\AccountInterface $current_user
   * @param \Drupal\neo4j_db_entity\Model\GraphEntityModelManagerInterface $model_manager
   *
   * @throws \Drupal\typed_data\Exception\InvalidArgumentException
   */
  public function __construct(AccountInterface $current_user, GraphEntityModelManagerInterface $model_manager) {
    $this->currentUser = User::load($current_user->id());
    $this->graphModelManager = $model_manager;
    $this->currentUserGraphModel = $this->graphModelManager->getNewEntityModel('user', 'user');
    $this->currentUserGraphModel->setEntity($this->currentUser);
  }

  /**
   * {@inheritDoc}
   */
  public function onEntityView(Neo4jDbEntityEvent $event) {
    if ($event->getEntity()->getEntityTypeId() === 'user') {
      $this->account = $event->getEntity();
      try {
        $this->accountGraphModel = $this->graphModelManager->getNewEntityModel('user', 'user');
        $this->accountGraphModel->setEntity($this->account);
          $this->accountGraphModel->buildModel();
//          $this->accountGraphModel->modelPersist();
      }
      catch (InvalidArgumentException $e) {
        // Do something if there's no user model?
      }
    }
  }

}
