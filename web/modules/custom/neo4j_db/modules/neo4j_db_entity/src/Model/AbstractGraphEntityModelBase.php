<?php


namespace Drupal\neo4j_db_entity\Model;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\neo4j_db\Database\Driver\bolt\Connection;
use \InvalidArgumentException;
use phpDocumentor\Reflection\Types\Boolean;

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
   */
  public function setEntity(\Drupal\Core\Entity\EntityInterface $entity): void {
    $this->entity = $entity;
  }

  /**
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

}
