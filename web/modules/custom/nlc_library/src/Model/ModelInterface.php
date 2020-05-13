<?php

namespace Drupal\nlc_library\Model;

interface ModelInterface {

  /**
   * The model type.
   *
   * @return string
   */
  public function modelType(): string;

  /**
   * Get the board ID for this model item.
   *
   * @return string
   */
  public function getBoardId(): string;

  /**
   * Get the item ID for this model item.
   *
   * @return string
   */
  public function getId(): string;

  /**
   * Get the name value of this model item.
   *
   * @return string
   */
  public function getName(): string;

  /**
   * Get the array of IDs for items that should be excluded.
   *
   * @return array
   */
  public function getExcludeIds(): array;

  /**
   * Set the array of IDs for items that should be excluded.
   *
   * @param array $excludeIds
   */
  public function setExcludeIds(array $excludeIds): void;

  /**
   * Add an ID to the array of items that should be excluded.
   *
   * @param string $id
   */
  public function addExcludeId($id): void;

  /**
   * Should we exclude the item with this ID?
   *
   * @param bool $strict
   *
   * @return bool
   */
  public function shouldExcludeId($strict = false): bool;

}
