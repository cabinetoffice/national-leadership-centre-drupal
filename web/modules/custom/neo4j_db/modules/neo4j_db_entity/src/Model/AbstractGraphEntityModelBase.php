<?php


namespace Drupal\neo4j_db_entity\Model;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
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
   */
  public function __construct(TranslationInterface $string_translation) {
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
  public function entity(): \Drupal\Core\Entity\EntityInterface {
    return $this->entity;
  }

  /**
   * {@inheritDoc}
   */
  public function setEntity(\Drupal\Core\Entity\EntityInterface $entity): void {
    $this->entity = $entity;
  }

}
