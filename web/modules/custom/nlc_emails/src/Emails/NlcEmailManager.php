<?php

namespace Drupal\nlc_emails\Emails;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;

class NlcEmailManager implements NlcEmailManagerInterface {

  use StringTranslationTrait;

  /**
   * The entity type manager
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The logger channel factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $logger;

  /**
   * Array of NLC emails.
   *
   * @var \Drupal\nlc_emails\Emails\NlcEmailHandlerInterface[]
   */
  protected $nlcEmails = [];

  /**
   * NlcEmailManager constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger
   * @param \Drupal\Core\StringTranslation\TranslationInterface $translation
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, LoggerChannelFactoryInterface $logger, TranslationInterface $translation) {
    $this->entityTypeManager = $entityTypeManager;
    $this->logger = $logger;
    $this->setStringTranslation($translation);
  }

  /**
   * {@inheritDoc}
   */
  public function addNlcEmailHandler(AbstractNlcEmailHandlerHandler $emailHandler): NlcEmailManager {
    $emailHandler->setEntityTypeManager($this->entityTypeManager);
    $emailHandler->setStringTranslation($this->getStringTranslation());
    $this->nlcEmails[$emailHandler->emailHandlerMachineName()] = $emailHandler;
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function getEmailHandlers() {
    return $this->nlcEmails;
  }

  /**
   * {@inheritDoc}
   */
  public function getEmailHandlerMachineNames(): array {
    return array_keys($this->nlcEmails);
  }

  /**
   * {@inheritDoc}
   */
  public function getEmailHandler($type) {
    return key_exists($type, $this->getEmailHandlers()) ? $this->getEmailHandlers()[$type] : false;;
  }

  /**
   * {@inheritDoc}
   */
  public function getEmailHandlersSummary(): array {
    $handlers = [];
    foreach ($this->getEmailHandlers() as $emailHandler) {
      $handlers[$emailHandler->emailHandlerMachineName()] = [
        'machine_name' => $emailHandler->emailHandlerMachineName(),
        'name' => $emailHandler->emailHandlerName(),
      ];
    }
    return $handlers;
  }

}
