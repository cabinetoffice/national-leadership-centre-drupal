<?php

namespace Drupal\nlc_topics\EventSubscriber;

use Drupal\neo4j_db_entity\EventSubscriber\AbstractTaxonomyTermUpdateSubscriber;

class LibraryLabelUpdateSubscriber extends AbstractTaxonomyTermUpdateSubscriber {

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
