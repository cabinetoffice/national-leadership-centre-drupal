<?php

namespace Drupal\nlc_emails\Emails;

use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\Component\Datetime\DateTimePlus;
use Drupal\nlc_emails\Tracker\AbstractTrackerBase;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Email object
 *
 * @package Drupal\nlc_emails\Emails
 */
class Email implements EmailInterface {

  /**
   * @var \Drupal\nlc_emails\Emails\NlcEmailManager
   */
  protected $emailManager;

  /**
   * @var string
   */
  protected $handlerMachineName;

  /**
   * @var string
   */
  protected $datasource;

  /**
   * @var int
   */
  protected $uid;

  /**
   * @var string
   */
  protected $email;

  /**
   * @var string
   */
  protected $itemId;

  /**
   * @var \Drupal\Component\Datetime\DateTimePlus
   */
  protected $changed;

  /**
   * @var \Drupal\Component\Datetime\DateTimePlus
   */
  protected $sent;

  /**
   * Email constructor.
   *
   * @param array $context
   *   A context array to create the Email object.
   */
  public function __construct(array $context) {
    $emailManager = \Drupal::service('nlc_emails.email_manager');
    $this->emailManager = $emailManager;
    $this->setHandlerMachineName($context['machine_name']);
    $this->setDatasource($context['datasource']);
    $this->setItemId($context['item_id']);
    $this->setUid($context['uid']);
    $this->setEmail($context['email']);
  }

  /**
   * {@inheritDoc}
   */
  public function getHandlerMachineName(): string {
    return $this->handlerMachineName;
  }

  /**
   * {@inheritDoc}
   */
  public function setHandlerMachineName($machineName): void {
    $this->handlerMachineName = $machineName;
  }

  /**
   * {@inheritDoc}
   */
  public function getDatasource(): string {
    return $this->datasource;
  }

  /**
   * {@inheritDoc}
   */
  public function setDatasource(string $datasource): void {
    $this->datasource = $datasource;
  }

  /**
   * {@inheritDoc}
   */
  public function getDatasourceParts(): array {
    return explode(self::DATASOURCE_SEPARATOR, $this->getDatasource());
  }

  /**
   * {@inheritDoc}
   */
  public function getDatasourceEntity() {
    return $this->getDatasourceParts()[0] ?? false;
  }

  /**
   * {@inheritDoc}
   */
  public function getDatasourceBundle() {
    return $this->getDatasourceParts()[1] ?? false;
  }


  /**
   * Get the email manager
   *
   * @return \Drupal\nlc_emails\Emails\NlcEmailManager
   */
  private function getEmailManager(): \Drupal\nlc_emails\Emails\NlcEmailManager {
    return $this->emailManager;
  }

  /**
   * {@inheritDoc}
   */
  public function getUid(): int {
    return $this->uid;
  }

  /**
   * {@inheritDoc}
   */
  public function setUid(int $uid): void {
    $this->uid = $uid;
  }

  /**
   * {@inheritDoc}
   */
  public function getEmail(): string {
    return $this->email;
  }

  /**
   * {@inheritDoc}
   */
  public function setEmail(string $email): void {
    $this->email = $email;
  }

  /**
   * {@inheritDoc}
   */
  public function getItemId(): string {
    return $this->itemId;
  }

  /**
   * {@inheritDoc}
   */
  public function setItemId(string $itemId): void {
    $this->itemId = $itemId;
  }

  /**
   * {@inheritDoc}
   */
  public function getChanged(): DateTimePlus {
    return $this->changed;
  }

  /**
   * {@inheritDoc}
   */
  public function setChanged(string $timestamp): void {
    $tz = $this->getTimezone();
    $this->changed = DateTimePlus::createFromTimestamp($timestamp, $tz);
  }

  /**
   * {@inheritDoc}
   */
  public function getSent(): DateTimePlus {
    return $this->sent;
  }

  /**
   * {@inheritDoc}
   */
  public function setSent(string $sent): void {
    $tz = $this->getTimezone();
    $this->sent = DateTimePlus::createFromTimestamp($sent, $tz);
  }

  /**
   * Get a timezone string to use for handling timestamp > DateTimePlus objects.
   *
   * @return string
   */
  protected function getTimezone() {
    return $this->dateFormat === DateTimeItemInterface::DATE_STORAGE_FORMAT
      ? DateTimeItemInterface::STORAGE_TIMEZONE
      : date_default_timezone_get();
  }

  /**
   * {@inheritDoc}
   */
  public function getEmailUser(): UserInterface {
    /** @var UserInterface $user */
    $user = User::load($this->getUid());
    return $user;
  }

}
