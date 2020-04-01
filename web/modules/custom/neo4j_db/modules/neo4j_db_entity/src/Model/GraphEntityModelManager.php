<?php

namespace Drupal\neo4j_db_entity\Model;

class GraphEntityModelManager implements GraphEntityModelManagerInterface {

  /**
   * An unsorted array of arrays of active graph entity models.
   * The keys are entity types.
   *
   * An associative array. The keys are bundle types. Values
   * are arrays of TranslatorInterface objects.
   *
   * @var \Drupal\neo4j_db_entity\Model\GraphEntityModelInterface[][]
   */
  protected $entityModels = [];

  /**
   * {@inheritDoc}
   */
  public function addEntityModel(GraphEntityModelInterface $entityModel) {
    $this->entityModels[$entityModel->entityType()][$entityModel->bundle()] = $entityModel;
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function getEntityModels(): array {
    return $this->entityModels;
  }

  /**
   * {@inheritDoc}
   */
  public function getEntityModel($type, $bundle) {
    if (isset($this->entityModels[$type]) && isset($this->entityModels[$type][$bundle])) {
      return $this->entityModels[$type][$bundle];
    }
    else {
      throw new \InvalidArgumentException(sprintf('No graph entity model has been registered for %s: %s', $type, $bundle));
    }
  }

  /**
   * {@inheritDoc}
   */
  public function getNewEntityModel ($type, $bundle) {
    return clone $this->getEntityModel($type, $bundle);
  }

}
