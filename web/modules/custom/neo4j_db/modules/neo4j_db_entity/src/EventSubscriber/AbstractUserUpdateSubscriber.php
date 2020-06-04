<?php


namespace Drupal\neo4j_db_entity\EventSubscriber;

use Drupal\neo4j_db_entity\Event\Neo4jDbEntityEvent;
use Drupal\neo4j_db_entity\Event\Neo4jDbEntityEventType;
use Drupal\neo4j_db_entity\Model\GraphEntityModelManagerInterface;

abstract class AbstractUserUpdateSubscriber extends AbstractEntityEventUpdateSubscriber {

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
  public function onEntityUpdate(Neo4jDbEntityEvent $event) {
    if ($event->getEntity()->getEntityTypeId() === 'user') {
      $this->account = $event->getEntity();
      $this->updateGraphUserNode();
    }
  }

  /**
   * Populate an account model object and persist it to the graph DB.
   */
  protected function updateGraphUserNode(): void {
    $this->accountModel = \Drupal::service($this->subscriberModelService());
    $this->accountModel->setEntity($this->account);
    $accountNode = $this->accountModel->modelFindOneBy();
    if ($accountNode) {
      $this->accountNode = $accountNode;
      $this->setAccountModelExtraFieldValues();
      $this->accountModel->modelFlush();
    }
    else {
      $event_type = Neo4jDbEntityEventType::INSERT;
      $event = new Neo4jDbEntityEvent($event_type, $this->account(), $this->account()->getEntityTypeId());
      _neo4j_db_dispatch($event_type, $event);
    }
  }

  /**
   * @return \Drupal\user\UserInterface
   */
  public function account(): \Drupal\user\UserInterface {
    return $this->account;
  }

  /**
   * @return \Drupal\nlc_network_individual\Model\User\GraphEntityNetworkIndividualModel
   */
  public function accountModel(): \Drupal\nlc_network_individual\Model\User\GraphEntityNetworkIndividualModel {
    return $this->accountModel;
  }

  /**
   * @return \GraphAware\Neo4j\OGM\Proxy\EntityProxy
   */
  public function accountNode(): \GraphAware\Neo4j\OGM\Proxy\EntityProxy {
    return $this->accountNode;
  }

  /**
   * Set any extra field values on the account model before it's persisted in the graph DB.
   *
   * @return void
   */
  abstract protected function setAccountModelExtraFieldValues(): void;

  /**
   * The subscriber model service string for the taxonomy term.
   *
   * @return string
   */
  abstract protected function subscriberModelService();

}
