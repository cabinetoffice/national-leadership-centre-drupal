<?php

namespace Drupal\nlc_emails\Emails;

use Drupal\Component\Datetime\DateTimePlus;
use Drupal\user\UserInterface;

/**
 * EmailInterface interface
 *
 * @package Drupal\nlc_emails\Emails
 */
interface EmailInterface {

  /**
   * String used to separate a datasource string.
   */
  const DATASOURCE_SEPARATOR = ':';

  /**
   * Get the machine name for the email handler for this email.
   *
   * @return string
   */
  public function getHandlerMachineName(): string;

  /**
   * Set the machine name for the email handler for this email.
   *
   * @param string $machineName
   *   The handler machine name string.
   */
  public function setHandlerMachineName($machineName): void;

  /**
   * Get the email datasource string
   *
   * @return string
   */
  public function getDatasource(): string;

  /**
   * Get the email datasource string.
   *
   * @param string $datasource
   *   The datasource string.
   */
  public function setDatasource(string $datasource): void;

  /**
   * Get the entity/bundle parts of the email datasource.
   *
   * @return array
   */
  public function getDatasourceParts(): array;

  /**
   * Get the datasource entity string.
   *
   * @return string|bool
   */
  public function getDatasourceEntity();

  /**
   * Get the datasource bundle string.
   *
   * @return string|bool
   */
  public function getDatasourceBundle();

  /**
   * Get the User ID for this email.
   *
   * @return int
   */
  public function getUid(): int;

  /**
   * Set the User ID for this email.
   *
   * @param int uid
   */
  public function setUid(int $uid): void;

  /**
   * Get the recipient email address for this email.
   *
   * @return string
   */
  public function getEmail(): string;

  /**
   * Set the recipient email address for this email.
   *
   * @param string $email
   *   The email address string.
   */
  public function setEmail(string $email): void;

  /**
   * Get the item ID for this email.
   *
   * @return string
   *   Item ID string.
   */
  public function getItemId(): string;

  /**
   * @param string $itemId
   *   The item ID string.
   */
  public function setItemId(string $itemId): void;

  /**
   * @return \Drupal\Component\Datetime\DateTimePlus
   */
  public function getChanged(): DateTimePlus;

  /**
   * @param string $timestamp
   */
  public function setChanged(string $timestamp): void;

  /**
   * @return \Drupal\Component\Datetime\DateTimePlus
   */
  public function getSent(): DateTimePlus;

  /**
   * Get the
   *
   * @param string $timestamp
   */
  public function setSent(string $timestamp): void;

  /**
   * @return int
   */
  public function getStatus(): int;

  /**
   * Get the email status.
   *
   * @param int $status
   */
  public function setStatus(int $status): void;

  /**
   * Get the Drupal user for this email.
   *
   * @return \Drupal\user\UserInterface
   */
  public function getEmailUser(): UserInterface;


}
