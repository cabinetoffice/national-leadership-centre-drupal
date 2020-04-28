<?php

namespace Drupal\neo4j_db_entity\Model\Action;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\neo4j_db\Database\Driver\bolt\Connection;
use Drupal\neo4j_db\Model\AbstractModel;
use Drupal\neo4j_db_entity\Model\GraphEntityModelInterface;
use GraphAware\Neo4j\OGM\Proxy\EntityProxy;
use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * Class GraphEntityViewModel
 *
 * @package Drupal\neo4j_db_entity\Model\Action
 *
 * @OGM\Node(label="View")
 */
class GraphEntityViewModel extends AbstractModel {

  use StringTranslationTrait;

  /**
   * @var \Drupal\neo4j_db\Database\Driver\bolt\Connection
   */
  protected $connection;

  /**
   * Viewee entity model object.
   *
   * @var \GraphAware\Neo4j\OGM\Proxy\EntityProxy
   * 
   */
  protected $vieweeEntityModel;

  /**
   * Viewer entity model object
   *
   * @var \GraphAware\Neo4j\OGM\Proxy\EntityProxy
   */
  protected $viewerEntityModel;

  /**
   * @var string
   *
   * @OGM\Property(type="string")
   */
  protected $ip;

  /**
   * @var int
   *
   * @OGM\Property(type="int")
   */
  protected $requestTime;

  /**
   * @var \Drupal\neo4j_db_entity\Model\GraphEntityModelInterface
   *
   * @OGM\Relationship(type="visitOf", direction="OUTGOING", collection=false, mappedBy="view", targetEntity="Person")
   */
  protected $visitOf;

  /**
   * @var array
   */
  protected $findOneByCriteria = [];


  /**
   * Constructs a new AbstractGraphEntityModelBase object.
   *
   * @param \Drupal\Core\StringTranslation\TranslationInterface $string_translation
   * @param \Drupal\neo4j_db\Database\Driver\bolt\Connection $connection
   *
   * @throws \Drupal\neo4j_db\Model\GraphModelException
   */
  public function __construct(TranslationInterface $string_translation, Connection $connection) {
    parent::__construct();
    $this->connection = $connection;
    $this->stringTranslation = $string_translation;
  }

  /**
   * @param \GraphAware\Neo4j\OGM\Proxy\EntityProxy $entityModel
   */
  public function setVieweeEntity(EntityProxy $entityModel): void {
    $this->vieweeEntityModel = $entityModel;
  }

  /**
   * @param \GraphAware\Neo4j\OGM\Proxy\EntityProxy $entityModel
   */
  public function setViewerEntityModel(EntityProxy $entityModel): void {
    $this->viewerEntityModel = $entityModel;
  }

  /**
   * @param string $ip
   */
  public function setIp(string $ip): void {
    $this->ip = $ip;
  }

  /**
   * @param int $requestTime
   */
  public function setRequestTime(int $requestTime): void {
    $this->requestTime = $requestTime;
  }

  /**
   * @return \Drupal\neo4j_db\Database\Driver\bolt\Connection
   */
  public function connection(): \Drupal\neo4j_db\Database\Driver\bolt\Connection {
    return $this->connection;
  }

  /**
   * Persist this model to the graph DB.
   */
  public function modelPersist() {
    $this->connection
      ->persist($this)
      ->execute();
  }
}
