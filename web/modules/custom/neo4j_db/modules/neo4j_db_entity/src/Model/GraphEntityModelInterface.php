<?php


namespace Drupal\neo4j_db_entity\Model;

use Drupal\Core\Entity\EntityInterface;
use Drupal\neo4j_db\Model\GraphModelInterface;

interface GraphEntityModelInterface extends GraphModelInterface {

  /**
   * The entity type this graph model describes.
   *
   * @return string
   */
  public function entityType();

  /**
   * The entity bundle this graph model describes.
   *
   * @return string
   */
  public function bundle();

  /**
   * The entity model has an entity object attached.
   *
   * @return boolean
   */
  public function hasEntity();

  /**
   * The entity this graph model handles.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   */
  public function entity();

  /**
   * Set the entity attached object for the entity model.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   */
  public function setEntity(\Drupal\Core\Entity\EntityInterface $entity): void;

  /**
   * Build a graph entity model object given a Drupal entity object
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *
   * @return \Drupal\neo4j_db_entity\Model\GraphEntityModelInterface
   */
  public function buildModel(EntityInterface $entity);

}
