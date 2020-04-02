<?php

namespace Drupal\neo4j_db_entity\Model;

interface GraphEntityModelManagerInterface {

  /**
   * Appends a translation system to the translation chain.
   *
   * @param \Drupal\neo4j_db_entity\Model\GraphEntityModelInterface $entityModel
   *   The graph entity model interface to be appended to the model chain.
   *
   * @return $this
   */
  public function addEntityModel(GraphEntityModelInterface $entityModel);

  /**
   * Get all defined entity models.
   *
   * @return \Drupal\neo4j_db_entity\Model\GraphEntityModelInterface[][][]
   */
  public function getEntityModels();

  /**
   * Get an array of model type names for a given entity type and bundle.
   *
   * @param $entityType string
   * @param $bundle string
   *
   * @return array
   */
  public function getEntityModelTypes($entityType, $bundle);

  /**
   * Get a specified entity model.
   *
   * @param $entityType string
   *   The name of an entity type.
   * @param $bundle string
   *   The name of a bundle type for the given entity type.
   * @param $type string|null
   *   The type of a model, in the case where multiple models exist with the same entity type and bundle.
   *
   * @return \Drupal\neo4j_db_entity\Model\GraphEntityModelInterface
   *
   * @throws \Drupal\typed_data\Exception\InvalidArgumentException
   */
  public function getEntityModel($entityType, $bundle, $type = null);

  /**
   * Get a new clone of a specified entity model.
   *
   * @param $entityType string
   *   The name of an entity type.
   * @param $bundle string
   *   The name of a bundle type for the given entity type.
   * @param $type string|null
   *   The type of a model, in the case where multiple models exist with the same entity type and bundle.
   *
   * @return \Drupal\neo4j_db_entity\Model\GraphEntityModelInterface
   *
   * @throws \Drupal\typed_data\Exception\InvalidArgumentException
   */
  public function getNewEntityModel($entityType, $bundle, $type = null);

}
