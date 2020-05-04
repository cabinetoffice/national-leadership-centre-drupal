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
   * @var \Drupal\neo4j_db_entity\Model\GraphEntityModelInterface[][][]
   */
  protected $entityModels = [];

  /**
   * {@inheritDoc}
   */
  public function addEntityModel(GraphEntityModelInterface $entityModel) {
    $this->entityModels[$entityModel->entityType()][$entityModel->bundle()][$entityModel->type()] = $entityModel;
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
  public function getEntityModelTypes($entityType, $bundle) {
    $modelTypes = [];
    if (isset($this->entityModels[$entityType]) && isset($this->entityModels[$entityType][$bundle])) {
      $modelTypes = array_keys($this->entityModels[$entityType][$bundle]);
    }
    return $modelTypes;
  }

  /**
   * {@inheritDoc}
   */
  public function getEntityModel($entityType, $bundle, $type = null) {
    if (isset($this->entityModels[$entityType]) && isset($this->entityModels[$entityType][$bundle])) {
      if (isset($type)) {
        if (isset($this->entityModels[$entityType][$bundle][$type])) {
          return $this->entityModels[$entityType][$bundle][$type];
        }
        else {
          throw new \InvalidArgumentException(sprintf('No graph entity model has been registered for %s: %s (%s)', $entityType, $bundle, $type));
        }
      }
      return current($this->entityModels[$entityType][$bundle]);
    }
    else {
      throw new \InvalidArgumentException(sprintf('No graph entity model has been registered for %s: %s', $entityType, $bundle));
    }
  }

  /**
   * {@inheritDoc}
   */
  public function getNewEntityModel ($entityType, $bundle, $type = null) {
    return clone $this->getEntityModel($entityType, $bundle, $type);
  }

}
