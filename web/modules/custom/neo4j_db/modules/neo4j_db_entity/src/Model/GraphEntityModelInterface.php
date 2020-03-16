<?php


namespace Drupal\neo4j_db_entity\Model;

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
  public function entityBundle();

  public function

}
