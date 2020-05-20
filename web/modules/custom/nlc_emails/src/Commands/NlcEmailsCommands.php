<?php

namespace Drupal\nlc_emails\Commands;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Consolidation\OutputFormatters\StructuredData\UnstructuredData;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\nlc_emails\Utility\CommandHelper;
use Drush\Commands\DrushCommands;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Defines drush commands for the NLC Connect email sending module.
 */
class NlcEmailsCommands extends DrushCommands {

  /**
   * The command helper.
   *
   * @var \Drupal\nlc_emails\Utility\CommandHelper
   */
  protected $commandHelper;

  /**
   * Constructs a NlcEmailsCommands object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
   *   The event dispatcher.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, ModuleHandlerInterface $moduleHandler, EventDispatcherInterface $eventDispatcher) {
    parent::__construct();

    $this->commandHelper = new CommandHelper($entityTypeManager, $moduleHandler, $eventDispatcher, 'dt');
  }

  /**
   * {@inheritdoc}
   */
  public function setLogger(LoggerInterface $logger) {
    parent::setLogger($logger);
    $this->commandHelper->setLogger($logger);
  }

  /**
   * Lists all email handlers.
   *
   * @command nlc-emails:list
   *
   * @usage drush nlc-emails:list
   *   List all email handlers.
   *
   * @field-labels
   *   machine_name: Machine name
   *   name: Name
   *
   * @aliases nlce-l
   *
   * @return \Consolidation\OutputFormatters\StructuredData\RowsOfFields
   *   The table rows.
   */
  public function listHandlersCommand() {
    $rows = $this->commandHelper->listHandlersCommand();
    return new RowsOfFields($rows);
  }

  /**
   * @command nlc-emails:hello
   * @aliases nlce-h
   *
   * @return \Consolidation\OutputFormatters\StructuredData\UnstructuredData
   */
  public function sayHello() {
    return new UnstructuredData(['Hello world']);
  }



}
