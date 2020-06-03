<?php

namespace Drupal\nlc_topics\EventSubscriber;

use Drupal\neo4j_db_entity\EventSubscriber\AbstractTaxonomyTermDeleteSubscriber;

class TopicDeleteSubscriber extends AbstractTaxonomyTermDeleteSubscriber {

  /**
   * {@inheritDoc}
   */
  protected function subscriberBundle() {
    return 'topics';
  }

  /**
   * {@inheritDoc}
   */
  protected function subscriberModelService() {
    return 'nlc_topics.model.taxonomy_term.topics';
  }

}
