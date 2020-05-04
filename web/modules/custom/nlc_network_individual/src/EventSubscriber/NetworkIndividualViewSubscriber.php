<?php

namespace Drupal\nlc_network_individual\EventSubscriber;

use Drupal\Component\Graph\Graph;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\neo4j_db_entity\EventSubscriber\AbstractEntityEventViewSubscriber;
use Drupal\neo4j_db_entity\Event\Neo4jDbEntityEvent;
use Drupal\neo4j_db_entity\Model\Action\GraphEntityViewModel;
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
   * The current request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

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
    $this->requestStack = \Drupal::service('request_stack');
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
      /** @var \Drupal\neo4j_db_entity\Model\Action\GraphEntityViewModel entityView */
      $this->entityView = \Drupal::service('neo4j_db.model.entity_view');
      $this->entityView->setVieweeEntity($this->accountGraph);
      $this->entityView->setViewerEntityModel($this->currentUserGraph);
      $ip = \Drupal::request()->getClientIp();
      $this->entityView->setIp($ip);
      $requestTime = \Drupal::time()->getRequestTime();
      $this->entityView->setRequestTime($requestTime);
      $this->entityView->setRequestUri($this->requestStack->getCurrentRequest()->getUri());
      $this->entityView->setMethod($this->requestStack->getCurrentRequest()->getMethod());
      $this->entityView->modelPersist();
      /** @var \Drupal\nlc_network_individual\Model\Relationship\NetworkIndividualVisitOfRelationshipModel $visitOf */
      $visitOf = \Drupal::service('network_individual.model_relationship.person_view');
      $visitOf->setView($this->entityView);
      $visitOf->setPerson($this->accountGraph);
      $visitOf->setRequestTime($requestTime);
      $this->entityView->setVisitOf($visitOf);
      $this->accountGraph->setVisitOf($visitOf);
      /** @var \Drupal\nlc_network_individual\Model\Relationship\NetworkIndividualVisitRelationshipModel $visit */
      $visit = \Drupal::service('network_individual.model_relationship.user_view');
      $visit->setView($this->entityView);
      $visit->setUser($this->currentUserGraph);
      $visit->setRequestTime($requestTime);
      $this->entityView->setVisit($visit);
      $this->currentUserGraph->setVisit($visit);
      $this->entityView->connection()->flush();
      // Can we add connect this visit to a previous visit?
      $query = $this->entityView->connection()->getOgmConnection()->createQuery('MATCH (u:User)-[:visit]->(v:View) WHERE id(u) = {user_id} RETURN v ORDER BY v.requestTime DESC SKIP 1 LIMIT 1');
      $query->addEntityMapping('n', \Drupal\neo4j_db_entity\Model\Action\GraphEntityViewModel::class);
      $query->setParameter('user_id', $this->currentUserGraph->getId());
      $result = $query->execute();
      if (count($result)) {
        /** @var \GraphAware\Bolt\Result\Type\Node $previousViewNode */
        $previousViewNode = current($result)['v'];
        /** @var \Drupal\neo4j_db_entity\Model\Action\GraphEntityViewModel $previousView */
        $previousView = \Drupal::service('neo4j_db.model.entity_view');
        $result = $previousView->connection()->findOneById(get_class($previousView), $previousViewNode->identity())->execute();
        /** @var \Drupal\nlc_network_individual\Model\Relationship\NetworkIndividualPreviousVisitRelationshipModel $previous */
        $previous = \Drupal::service('network_individual.model_relationship.previous_user_view');
        $previous->setPrevious($result);
        $previous->setCurrent($this->entityView);
        $this->entityView->setPreviousView($previous);
        $previousView->setCurrentView($previous);
        $this->entityView->connection()->flush();
      }

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
