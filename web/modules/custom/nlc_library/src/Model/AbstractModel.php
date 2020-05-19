<?php

namespace Drupal\nlc_library\Model;

abstract class AbstractModel implements ModelInterface {

  /**
   * Board ID.
   *
   * @var string
   */
  protected $boardId;

  /**
   * Item ID.
   *
   * @var string
   */
  protected $id;

  /**
   * Item name.
   *
   * @var string
   */
  protected $name;

  /**
   * Array of item IDs to exclude from adding to
   *
   * @var array
   */
  protected $excludeIds = [];

  /**
   * AbstractModel constructor.
   *
   * @param object $object
   */
  public function __construct($object) {
    $this->boardId = $object->idBoard ?? null;
    $this->id = $object->id;
    $this->name = $object->name;
  }

  /**
   * {@inheritDoc}
   */
  public function getBoardId(): string {
    return $this->boardId;
  }

  /**
   * {@inheritDoc}
   */
  public function getId(): string {
    return $this->id;
  }

  /**
   * {@inheritDoc}
   */
  public function getName(): string {
    return $this->name;
  }

  /**
   * {@inheritDoc}
   */
  public function getExcludeIds(): array {
    return $this->excludeIds;
  }

  /**
   * {@inheritDoc}
   */
  public function setExcludeIds(array $excludeIds): void {
    $this->excludeIds = $excludeIds;
  }

  /**
   * {@inheritDoc}
   */
  public function addExcludeId($id): void {
    $this->excludeIds[] = $id;
  }

  /**
   * {@inheritDoc}
   */
  public function shouldExcludeId($strict = false): bool {
    return in_array($this->getId(), $this->getExcludeIds(), $strict);
  }

}
