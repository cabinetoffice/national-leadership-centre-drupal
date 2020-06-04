<?php

namespace Drupal\nlc_network_individual\EventSubscriber;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\TypedData\Exception\MissingDataException;
use Drupal\neo4j_db_entity\EventSubscriber\AbstractUserDeleteSubscriber;
use Drupal\neo4j_db_entity\EventSubscriber\AbstractUserUpdateSubscriber;

class NetworkIndividualDeleteSubscriber extends AbstractUserDeleteSubscriber {

  /**
   * {@inheritDoc}
   */
  protected function subscriberModelService() {
    return 'network_individual.model.user';
  }

}
