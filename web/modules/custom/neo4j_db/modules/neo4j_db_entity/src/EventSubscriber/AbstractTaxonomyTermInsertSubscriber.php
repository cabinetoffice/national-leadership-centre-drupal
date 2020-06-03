<?php

namespace Drupal\neo4j_db_entity\EventSubscriber;

use Drupal\neo4j_db_entity\Event\Neo4jDbEntityEvent;
use Drupal\neo4j_db_entity\EventSubscriber\AbstractEntityEventInsertSubscriber;
use Drupal\neo4j_db_entity\Model\GraphEntityModelManagerInterface;

abstract class AbstractTaxonomyTermInsertSubscriber extends AbstractEntityEventInsertSubscriber {

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
    if ($event->getEntity()->getEntityTypeId() === 'taxonomy_term' && $event->getEntity()->bundle() === $this->subscriberBundle()) {
      $this->taxonomyTerm = $event->getEntity();
      $this->insertGraphTopicNode();
    }
  }

  protected function insertGraphTopicNode() {
    /** @var \Drupal\neo4j_db_entity\Model\TaxonomyTerm\AbstractGraphEntityTaxonomyTermModel $term */
    $term = \Drupal::service($this->subscriberModelService());
    $term->setEntity($this->taxonomyTerm);
    $term->setEntityId($this->taxonomyTerm->id());
    $term->setName($this->taxonomyTerm->getName());
    $term->modelPersist();
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
