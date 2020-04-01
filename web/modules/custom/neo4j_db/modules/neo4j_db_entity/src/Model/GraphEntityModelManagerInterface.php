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
   * @return \Drupal\neo4j_db_entity\Model\GraphEntityModelInterface[][]
   */
  public function getEntityModels();

  /**
   * Get a specified entity model.
   *
   * @param $type string
   *   The name of an entity type.
   * @param $bundle string
   *   The name of a bundle type for the given entity type.
   *
   * @return \Drupal\neo4j_db_entity\Model\GraphEntityModelInterface
   *
   * @throws \Drupal\typed_data\Exception\InvalidArgumentException
   */
  public function getEntityModel($type, $bundle);

  /**
   * Get a new clone of a specified entity model.
   *
   * @param $type string
   *   The name of an entity type.
   * @param $bundle string
   *   The name of a bundle type for the given entity type.
   *
   * @return \Drupal\neo4j_db_entity\Model\GraphEntityModelInterface
   *
   * @throws \Drupal\typed_data\Exception\InvalidArgumentException
   */
  public function getNewEntityModel($type, $bundle);

}
