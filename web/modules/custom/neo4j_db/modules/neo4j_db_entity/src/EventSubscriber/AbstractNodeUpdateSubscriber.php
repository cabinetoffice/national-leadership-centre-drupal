<?php

namespace Drupal\neo4j_db_entity\EventSubscriber;

use Drupal\neo4j_db_entity\Event\Neo4jDbEntityEvent;
use Drupal\neo4j_db_entity\Event\Neo4jDbEntityEventType;
use Drupal\neo4j_db_entity\EventSubscriber\AbstractEntityEventUpdateSubscriber;
use Drupal\neo4j_db_entity\Model\GraphEntityModelManagerInterface;

abstract class AbstractNodeUpdateSubscriber extends AbstractEntityEventUpdateSubscriber {

  /**
   * @var \Drupal\neo4j_db_entity\Model\GraphEntityModelManagerInterface
   */
  protected $graphModelManager;

  /**
   * @var \Drupal\taxonomy\TermInterface
   */
  protected $taxonomyTerm;

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
    if ($event->getEntity()->getEntityTypeId() === 'node' && $event->getEntity()->bundle() === $this->subscriberBundle()) {
      $this->taxonomyTerm = $event->getEntity();
      $this->updateGraphTopicNode();
    }
  }

  protected function updateGraphTopicNode() {
    /** @var \Drupal\neo4j_db_entity\Model\TaxonomyTerm\AbstractGraphEntityTaxonomyTermModel $term */
    $term = \Drupal::service($this->subscriberModelService());
    $term->setEntity($this->taxonomyTerm);
    $term->setEntityId($this->taxonomyTerm->id());
    $termNode = $term->modelFindOneBy();
    if ($termNode) {
      $termNode->setName($this->taxonomyTerm->getName());
      $term->modelFlush();
    }
    else {
      $event_type = Neo4jDbEntityEventType::INSERT;
      $event = new Neo4jDbEntityEvent($event_type, $this->taxonomyTerm, $this->taxonomyTerm->getEntityTypeId());
      _neo4j_db_dispatch($event_type, $event);
    }
  }

  /**
   * The subscriber bundle name for this taxonomy term entity.
   *
   * @return string
   */
  abstract protected function subscriberBundle();

  /**
   * The subscriber model service string for the taxonomy term.
   *
   * @return string
   */
  abstract protected function subscriberModelService();

}
