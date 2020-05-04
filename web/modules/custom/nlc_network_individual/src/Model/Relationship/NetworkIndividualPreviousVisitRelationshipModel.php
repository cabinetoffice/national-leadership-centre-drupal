<?php

namespace Drupal\nlc_network_individual\Model\Relationship;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\neo4j_db\Database\Driver\bolt\Connection;
use Drupal\neo4j_db\Model\Relationship\AbstractRelationshipModel;
use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * Class NetworkIndividualPreviousVisitRelationshipModel
 *
 * @package Drupal\nlc_network_individual\Model\Relationship
 *
 * @OGM\RelationshipEntity(type="previousVisit")
 */
class NetworkIndividualPreviousVisitRelationshipModel extends AbstractRelationshipModel {

  use StringTranslationTrait;

  /**
   * @var \Drupal\neo4j_db\Database\Driver\bolt\Connection
   */
  protected $connection;

  /**
   * @OGM\EndNode(targetEntity="Drupal\neo4j_db_entity\Model\Action\GraphEntityViewModel")
   */
  protected $previous;

  /**
   * @OGM\StartNode(targetEntity="Drupal\neo4j_db_entity\Model\Action\GraphEntityViewModel")
   */
  protected $current;

  /**
   * NetworkIndividualVisitOfRelationshipModel constructor.
   *
   * @param \Drupal\Core\StringTranslation\TranslationInterface $string_translation
   * @param \Drupal\neo4j_db\Database\Driver\bolt\Connection $connection
   */
  public function __construct(TranslationInterface $string_translation, Connection $connection) {
    $this->connection = $connection;
    $this->stringTranslation = $string_translation;
  }

  /**
   * @return \Drupal\neo4j_db\Database\Driver\bolt\Connection
   */
  public function connection(): \Drupal\neo4j_db\Database\Driver\bolt\Connection {
    return $this->connection;
  }

  /**
   * @param mixed $previous
   */
  public function setPrevious($previous): void {
    $this->previous = $previous;
  }

  /**
   * @param mixed $current
   */
  public function setCurrent($current): void {
    $this->current = $current;
  }

}
