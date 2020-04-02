<?php


namespace Drupal\neo4j_db_entity\Model;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\neo4j_db\Database\Driver\bolt\Connection;
use \InvalidArgumentException;

/**
 * Provides a base entity model object for the knowledge graph.
 *
 * @package Drupal\neo4j_db_entity\Model
 */
abstract class AbstractGraphEntityModelBase implements GraphEntityModelInterface {

  use StringTranslationTrait;

  /**
   * @var \Drupal\neo4j_db\Database\Driver\bolt\Connection
   */
  protected $connection;

  /**
   * Entity object.
   *
   * @var \Drupal\Core\Entity\EntityInterface
   */
  protected $entity;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity type.
   *
   * @var string
   */
  protected $entityType;

  /**
   * The entity bundle.
   *
   * @var string
   */
  protected $bundle;

  /**
   * A type, to distinguish models with the same entityType and bundle.
   *
   * @var string
   */
  protected $type;

  /**
   * @var array
   */
  protected $findOneByCriteria = [];

  /**
   * Constructs a new AbstractGraphEntityModelBase object.
   *
   * @param \Drupal\Core\StringTranslation\TranslationInterface $string_translation
   * @param \Drupal\neo4j_db\Database\Driver\bolt\Connection $connection
   */
  public function __construct(TranslationInterface $string_translation, Connection $connection) {
    $this->connection = $connection;
    $this->stringTranslation = $string_translation;
    if (!$this->entityType()) {
      $message = $this->t('Missing entity type for this entity model');
      throw new InvalidArgumentException($message);
    }
    if (!$this->bundle()) {
      $message = $this->t('Missing bundle for this entity model');
      throw new InvalidArgumentException($message);
    }
    if (!$this->type()) {
      $message = $this->t('Missing bundle for this entity model');
      throw new InvalidArgumentException($message);
    }
  }

  /**
   * {@inheritDoc}
   */
  public function entityType(): string {
    return $this->entityType;
  }

  /**
   * {@inheritDoc}
   */
  public function bundle(): string {
    return $this->bundle;
  }

  /**
   * {@inheritDoc}
   */
  public function type(): string {
    return $this->type;
  }

  /**
   * {@inheritDoc}
   */
  public function hasEntity(): bool {
    return $this->entity !== null && $this->entity instanceof \Drupal\Core\Entity\EntityInterface;
  }

  /**
   * {@inheritDoc}
   */
  public function entity() {
    return $this->entity;
  }

  /**
   * {@inheritDoc}
   *
   */
  public function setEntity(EntityInterface $entity): void {
    $this->entity = $entity;
    $this->setFindOneByCriteria($this->baseFindOneByCriteria());
  }

  /**
   * {@inheritDoc}
   */
  public function findOneByCriteria() {
    return $this->findOneByCriteria;
  }

  /**
   * {@inheritDoc}
   */
  public function setFindOneByCriteria(array $criteria): void {
    $this->findOneByCriteria =  $criteria;
  }

  /**
   * {@inheritDoc}
   */
  public function addFindOneByCriteria(array $criteria): void {
    $this->findOneByCriteria = array_merge($this->findOneByCriteria, $criteria);
  }

  /**
   * Build the model object from the Drupal entity.
   *
   * @return \Drupal\neo4j_db_entity\Model\GraphEntityModelInterface
   *
   * @throws \InvalidArgumentException
   */
  public function buildModel() {
    if (!$this->hasEntity()) {
      $message = $this->t('Entity is required to build the model object');
      throw new InvalidArgumentException($message);
    }
    return $this;
  }

  public function modelPersist() {
    $this->connection
      ->persist($this)
      ->execute();
  }

  /**
   * @return object|null
   */
  public function modelFindOneBy() {
    return $this->connection
      ->findOneBy(get_class($this), $this->findOneByCriteria())
      ->execute();
  }

}
