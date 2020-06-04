<?php


namespace Drupal\neo4j_db_entity\EventSubscriber;

use Drupal\neo4j_db_entity\Event\Neo4jDbEntityEvent;
use Drupal\neo4j_db_entity\Event\Neo4jDbEntityEventType;
use Drupal\neo4j_db_entity\Model\GraphEntityModelManagerInterface;

abstract class AbstractUserDeleteSubscriber extends AbstractEntityEventDeleteSubscriber {

  /**
   * @var \Drupal\neo4j_db_entity\Model\GraphEntityModelManagerInterface
   */
  protected $graphModelManager;

  /**
   * @var \Drupal\user\UserInterface
   */
  protected $account;

  /**
   * @var \Drupal\nlc_network_individual\Model\User\GraphEntityNetworkIndividualModel
   */
  protected $accountModel;

  /**
   * @var \GraphAware\Neo4j\OGM\Proxy\EntityProxy
   */
  protected $accountNode;

  /**
   * NetworkIndividualUserViewSubscriber constructor.
   *
   * @param \Drupal\neo4j_db_entity\Model\GraphEntityModelManagerInterface $model_manager
   *
   * @throws \Drupal\typed_data\Exception\InvalidArgumentException
   */
  public function __construct(GraphEntityModelManagerInterface $model_manager) {
    $this->graphModelManager = $model_manager;
  }

  /**
   * @inheritDoc
   */
  public function onEntityDelete(Neo4jDbEntityEvent $event) {
    if ($event->getEntity()->getEntityTypeId() === 'user') {
      $this->account = $event->getEntity();
      $this->deleteGraphUserNode();
    }
  }

  /**
   * Populate an account model object and persist it to the graph DB.
   */
  protected function deleteGraphUserNode(): void {
    $this->accountModel = \Drupal::service($this->subscriberModelService());
    $this->accountModel->setEntity($this->account);
    $accountNode = $this->accountModel->modelFindOneBy();
    if ($accountNode) {
      $this->accountModel->setId($accountNode->getId());
      $this->accountModel->modelDelete();
    }
  }

  /**
   * @return \Drupal\user\UserInterface
   */
  public function account(): \Drupal\user\UserInterface {
    return $this->account;
  }

  /**
   * The subscriber model service string for the taxonomy term.
   *
   * @return string
   */
  abstract protected function subscriberModelService();

}
