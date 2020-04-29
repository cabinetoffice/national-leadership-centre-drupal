<?php

namespace Drupal\nlc_network_individual\Model\Relationship;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\neo4j_db\Database\Driver\bolt\Connection;
use Drupal\neo4j_db\Model\Relationship\AbstractRelationshipModel;
use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * Class NetworkIndividualVisitRelationshipModel
 *
 * @package Drupal\nlc_network_individual\Model\Relationship
 *
 * @OGM\RelationshipEntity(type="visit")
 */
class NetworkIndividualVisitRelationshipModel extends AbstractRelationshipModel {

  use StringTranslationTrait;

  /**
   * @var \Drupal\neo4j_db\Database\Driver\bolt\Connection
   */
  protected $connection;

  /**
   * @OGM\StartNode(targetEntity="Drupal\neo4j_db_entity\Model\Action\GraphEntityViewModel")
   */
  protected $view;

  /**
   * @OGM\EndNode(targetEntity="Drupal\neo4j_db_entity\Model\User\GraphEntityUserUserModel")
   */
  protected $user;

  /**
   * @var string
   *
   * @OGM\Property(type="string")
   */
  protected $requestTime;

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
   * @param mixed $view
   */
  public function setView($view): void {
    $this->view = $view;
  }

  /**
   * @param mixed $user
   */
  public function setUser($user): void {
    $this->user = $user;
  }

  /**
   * @param string $requestTime
   */
  public function setRequestTime(string $requestTime): void {
    $this->requestTime = $requestTime;
  }

}
