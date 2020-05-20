<?php

namespace Drupal\nlc_emails\Commands;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Consolidation\OutputFormatters\StructuredData\UnstructuredData;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\nlc_emails\Emails\NlcEmailManagerInterface;
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
   * @param \Drupal\nlc_emails\Emails\NlcEmailManagerInterface $emailManager
   *   The email handler manager.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
   *   The event dispatcher.
   */
  public function __construct(NlcEmailManagerInterface $emailManager, ModuleHandlerInterface $moduleHandler, EventDispatcherInterface $eventDispatcher) {
    parent::__construct();

    $this->commandHelper = new CommandHelper($emailManager, $moduleHandler, $eventDispatcher, 'dt');
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
   * @aliases nlce-l,nlc-emails-list
   *
   * @return \Consolidation\OutputFormatters\StructuredData\RowsOfFields
   *   The table rows.
   */
  public function listHandlers() {
    $rows = $this->commandHelper->listHandlersCommand();
    return new RowsOfFields($rows);
  }

  /**
   * @param string $handlerId
   *   The machine name of an email handler.
   *
   * @param array $options
   *   (optional) An array of options.
   *
   * @command nlc-emails:send
   *
   * @option limit
   *   The maximum number of emails to send. Set to 0 to send all emails.
   *   Defaults to 0 (send all).
   * @option batch-size
   *   The maximum number of emails to send per batch run. Set to 0 to send all
   *   emails at once. Defaults to the "Cron batch size" setting of the sender.
   *
   * @usage drush nlc-emails:send machine_name
   *   Send all all emails for the handler with the ID machine_name.
   * @usage drush nlce-s machine_name
   *   Alias to send all all emails for the handler with the ID machine_name.
   * @usage drush nlce-s machine_name 100
   *   Send a maximum number of 100 emails for the handler with the ID machine_name.
   * @usage drush nlce-s machine_name 100 10
   *   Send a maximum number of 100 emails (10 items per batch run) for the
   *   handler with the ID machine_name.
   *
   * @aliases nlce-s,nlc-emails-send
   *
   * @field-labels
   *   uid: User ID
   *   name: Name
   *   email: Email address
   *
   * @return \Consolidation\OutputFormatters\StructuredData\RowsOfFields
   *   The table rows.
   *
   * @throws \Exception
   *   If a batch process could not be created.
   */
  public function sendHandlerEmail($handlerId, array $options = ['limit' => NULL, 'batch-size' => NULL]) {
    $limit = $options['limit'];
    $batch_size = $options['batch-size'];
    $process_batch = $this->commandHelper->sendHandlerEmailCommand($handlerId, $limit, $batch_size);
//    print_r($process_batch);
    return new RowsOfFields($process_batch);

//    if ($process_batch === TRUE) {
//      drush_backend_batch_process();
//    }
  }


}
