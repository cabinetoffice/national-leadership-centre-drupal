<?php


namespace Drupal\nlc_topics\EventSubscriber;

use Drupal\neo4j_db_entity\Event\Neo4jDbEntityEvent;
use Drupal\neo4j_db_entity\EventSubscriber\AbstractEntityEventInsertSubscriber;
use Drupal\neo4j_db_entity\Model\GraphEntityModelManagerInterface;

class TopicInsertSubscriber extends AbstractEntityEventInsertSubscriber {

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
   * {@inheritDoc}
   */
  public function onEntityInsert(Neo4jDbEntityEvent $event) {
    if ($event->getEntity()->getEntityTypeId() === 'taxonomy_term' && $event->getEntity()->bundle() === 'topics') {
      $this->taxonomyTerm = $event->getEntity();
      $this->insertGraphTopicNode();
    }
  }

  protected function insertGraphTopicNode() {
    /** @var \Drupal\nlc_topics\Model\TaxonomyTerm\GraphEntityTopicModel $topic */
    $topic = \Drupal::service('nlc_topics.model.taxonomy_term.topics');
    $topic->setEntity($this->taxonomyTerm);
    $topic->setEntityId($this->taxonomyTerm->id());
    $topic->setName($this->taxonomyTerm->getName());
    $topic->modelPersist();
  }

}
