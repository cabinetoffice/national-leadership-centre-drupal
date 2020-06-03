<?php

namespace Drupal\nlc_topics\EventSubscriber;

use Drupal\neo4j_db_entity\EventSubscriber\AbstractTaxonomyTermDeleteSubscriber;

class LibraryLabelDeleteSubscriber extends AbstractTaxonomyTermDeleteSubscriber {

  /**
   * {@inheritDoc}
   */
  protected function subscriberBundle() {
    return 'library_labels';
  }

  /**
   * {@inheritDoc}
   */
  protected function subscriberModelService() {
    return 'nlc_topics.model.taxonomy_term.library_labels';
  }

}
