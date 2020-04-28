<?php

namespace Drupal\nlc_network_individual\EventSubscriber;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\neo4j_db_entity\EventSubscriber\AbstractEntityEventViewSubscriber;
use Drupal\neo4j_db_entity\Event\Neo4jDbEntityEvent;
use Drupal\neo4j_db_entity\Model\GraphEntityModelManagerInterface;
use Drupal\nlc_network_individual\Model\Relationship\NetworkIndividualVisitOfRelationshipModel;
use Drupal\typed_data\Exception\InvalidArgumentException;
use Drupal\user\Entity\User;

class NetworkIndividualViewSubscriber extends AbstractEntityEventViewSubscriber {

  /**
   * @var \Drupal\user\UserInterface
   */
  protected $currentUser;

  /**
   * @var \Drupal\neo4j_db_entity\Model\User\GraphEntityUserUserModel
   */
  protected $currentUserGraphModel;

  /**
   * @var \GraphAware\Neo4j\OGM\Proxy\EntityProxy|null
   */
  protected $currentUserGraph;

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
   * @var \GraphAware\Neo4j\OGM\Proxy\EntityProxy|null
   */
  protected $accountGraph;

  /**
   * @var \Drupal\neo4j_db_entity\Model\Action\GraphEntityViewModel
   */
  protected $entityView;

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
    $this->currentUserGraphModel = $this->graphModelManager->getNewEntityModel('user', 'user', 'User');
    $this->currentUserGraphModel->setEntity($this->currentUser);
    $this->currentUserGraphModel->modelFindOneBy();
    $this->currentUserGraph = $this->currentUserGraphModel->getGraphNode();
  }

  /**
   * {@inheritDoc}
   */
  public function onEntityView(Neo4jDbEntityEvent $event) {
    if ($event->getEntity()->getEntityTypeId() === 'user') {
      $this->account = $event->getEntity();
      $this->accountGraph = $this->getGraphPerson($this->account);
      $this->currentUserViewsAccount();
    }
  }

  protected function currentUserViewsAccount() {
//    dpm($this->currentUserGraphModel);
    if ($this->hasCurrentUserGraph() && $this->hasAccountGraph()) {
      $this->entityView = \Drupal::service('neo4j_db.model.entity_view');
      $this->entityView->setVieweeEntity($this->accountGraph);
      $this->entityView->setViewerEntityModel($this->currentUserGraph);
      $ip = \Drupal::request()->getClientIp();
      $this->entityView->setIp($ip);
      $requestTime = \Drupal::time()->getRequestTime();
      $this->entityView->setRequestTime($requestTime);
//      \Drupal::logger('nlc_debug')->debug('<pre>' . print_r($this->entityView, true) . '</pre>');
      $this->entityView->modelPersist();
      /** @var \Drupal\nlc_network_individual\Model\Relationship\NetworkIndividualVisitOfRelationshipModel $visitOf */
      $visitOf = \Drupal::service('network_individual.model_relationship.person_view');
      $visitOf->setView($this->entityView);
      $visitOf->setPerson($this->accountGraph);
      $visitOf->setRequestTime($requestTime);
      $visitOf->modelPersist();
      dpm($visitOf);
    }
  }

  /**
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *
   * @return bool|\GraphAware\Neo4j\OGM\Proxy\EntityProxy|null
   */
  protected function getGraphPerson(EntityInterface $entity) {
    $graphModel = FALSE;
    try {
      $this->accountGraphModel = $this->graphModelManager->getEntityModel('user', 'user', 'NetworkIndividual');
      $this->accountGraphModel->setEntity($entity);
      $this->accountGraphModel->modelFindOneBy();
      $graphModel = $this->accountGraphModel->getGraphNode();
    }
    catch (InvalidArgumentException $e) {
      // Do something?
    }
    return $graphModel;
  }

  /**
   * @return bool
   */
  private function hasCurrentUserGraph() {
    return $this->currentUserGraph ? true : false;
  }

  /**
   * @return bool
   */
  private function hasAccountGraph() {
    return $this->accountGraph ? true : false;
  }

}
