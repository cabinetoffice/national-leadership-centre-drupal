<?php


namespace Drupal\nlc_topics\EventSubscriber;

use Drupal\neo4j_db_entity\Event\Neo4jDbEntityEvent;
use Drupal\neo4j_db_entity\EventSubscriber\AbstractEntityEventDeleteSubscriber;
use Drupal\neo4j_db_entity\Model\GraphEntityModelManagerInterface;

class TopicDeleteSubscriber extends AbstractEntityEventDeleteSubscriber {

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

  public function onEntityDelete(Neo4jDbEntityEvent $event) {
    if ($event->getEntity()->getEntityTypeId() === 'taxonomy_term' && $event->getEntity()->bundle() === 'topics') {
      $this->taxonomyTerm = $event->getEntity();
      $this->deleteGraphTopicNode();
    }
  }

  protected function deleteGraphTopicNode() {
    /** @var \Drupal\nlc_topics\Model\TaxonomyTerm\GraphEntityTopicModel $topic */
    $topic = \Drupal::service('nlc_topics.model.taxonomy_term.topics');
    $topic->setEntity($this->taxonomyTerm);
    $topic->setEntityId($this->taxonomyTerm->id());
    $topic->modelFindOneBy();
    if ($topic->getId()) {
      $topic->modelDelete();
    }
  }

}
