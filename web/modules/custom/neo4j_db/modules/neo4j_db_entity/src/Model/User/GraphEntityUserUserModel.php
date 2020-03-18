<?php

namespace Drupal\neo4j_db_entity\Model\User;

use Drupal\Core\Entity\EntityInterface;
use Drupal\neo4j_db_entity\Model\AbstractGraphEntityModelBase;

class GraphEntityUserUserModel extends AbstractGraphEntityModelBase {

  protected $entityType = 'user';

  protected $bundle = 'user';

  public function buildModel(EntityInterface $entity) {
    // TODO: Implement buildModel() method.
  }

}
