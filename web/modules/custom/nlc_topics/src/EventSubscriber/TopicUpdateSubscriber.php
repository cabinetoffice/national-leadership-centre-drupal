<?php

namespace Drupal\nlc_topics\EventSubscriber;

use Drupal\neo4j_db_entity\Event\Neo4jDbEntityEvent;
use Drupal\neo4j_db_entity\Event\Neo4jDbEntityEventType;
use Drupal\neo4j_db_entity\EventSubscriber\AbstractEntityEventUpdateSubscriber;
use Drupal\neo4j_db_entity\Model\GraphEntityModelManagerInterface;

class TopicUpdateSubscriber extends AbstractEntityEventUpdateSubscriber {

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
    if ($event->getEntity()->getEntityTypeId() === 'taxonomy_term' && $event->getEntity()->bundle() === 'topics') {
      $this->taxonomyTerm = $event->getEntity();
      $this->updateGraphTopicNode();
    }
  }

  protected function updateGraphTopicNode() {
    /** @var \Drupal\nlc_topics\Model\TaxonomyTerm\GraphEntityTopicModel $topic */
    $topic = \Drupal::service('nlc_topics.model.taxonomy_term.topics');
    $topic->setEntity($this->taxonomyTerm);
    $topic->setEntityId($this->taxonomyTerm->id());
    $topicNode = $topic->modelFindOneBy();
    if ($topicNode) {
      $topicNode->setName($this->taxonomyTerm->getName());
      $topic->modelFlush();
    }
    else {
      $event_type = Neo4jDbEntityEventType::INSERT;
      $event = new Neo4jDbEntityEvent($event_type, $this->taxonomyTerm, $this->taxonomyTerm->getEntityTypeId());
      _neo4j_db_dispatch($event_type, $event);
    }
  }

}
