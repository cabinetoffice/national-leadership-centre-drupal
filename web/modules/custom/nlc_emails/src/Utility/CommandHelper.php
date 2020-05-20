<?php

namespace Drupal\nlc_emails\Utility;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\nlc_emails\ConsoleException;
use Drupal\nlc_emails\Emails\NlcEmailManagerInterface;
use Drupal\nlc_emails\NlcEmailsException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CommandHelper implements LoggerAwareInterface {

  use LoggerAwareTrait;

  use StringTranslationTrait;

  /**
   * The NLC email manager.
   *
   * @var \Drupal\nlc_emails\Emails\NlcEmailManager
   */
  protected $emailManager;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The event dispatcher.
   *
   * @var \Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher|null
   */
  protected $eventDispatcher;

  /**
   * A callable for translating strings.
   *
   * @var callable
   */
  protected $translationFunction;

  /**
   * Constructs a CommandHelper object.
   *
   * @param \Drupal\nlc_emails\Emails\NlcEmailManagerInterface $email_manager
   *   The email manager.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher.
   * @param string|callable $translation_function
   *   (optional) A callable for translating strings.
   */
  public function __construct(NlcEmailManagerInterface $email_manager, ModuleHandlerInterface $module_handler, EventDispatcherInterface $event_dispatcher, $translation_function = 'dt') {
    $this->emailManager = $email_manager;
    $this->moduleHandler = $module_handler;
    $this->eventDispatcher = $event_dispatcher;
    $this->translationFunction = $translation_function;
  }

  /**
   * List all available email sending handlers.
   *
   * @return array
   *   An associative array, keyed by email handler machine name, each value an associative
   *   array with the following keys:
   *   - machine_name: Handler machine name
   *   - name: Human-readable name
   */
  public function listHandlersCommand() {
    /** @var \Drupal\nlc_emails\Emails\NlcEmailManager $handlersManager */
    $handlersManager = \Drupal::service('nlc_emails.email_manager');

    return  $handlersManager->getEmailHandlersSummary();
  }

  /**
   * @param string $handlerId
   *   Machine name of the email send handler.
   * @param int|null $limit
   *   (optional) The maximum number of emails to send, or NULL to send all
   *   items.
   * @param int|null $batchSize
   *   (optional) The maximum number of emails to process per batch, or NULL to
   *   send all emails at once.
   *
   * @return array|bool
   *   TRUE if sending for the email handler was queued, FALSE otherwise.
   *
   * @throws \Drupal\nlc_emails\ConsoleException
   *   Thrown if an email sending batch process could not be created.
   * @throws \Drupal\nlc_emails\NlcEmailsException
   *   Thrown if one of the handler had an invalid tracker set.
   */
  public function sendHandlerEmailCommand($handlerId, $limit = null, $batchSize = null) {
    $handler = $this->emailManager->getEmailHandler($handlerId);

    $batchSet = false;

    // If we pass NULL, it would be used as "no items". -1 is the correct way
    // to index all items.
    $current_limit = $limit ?: -1;

    $currentBatchSize = $batchSize ?? 50;

    $arguments = [
      '@handler' => $handler->emailHandlerMachineName(),
      '@limit' => $current_limit,
      '@batch_size' => $currentBatchSize,
    ];
    $this->logger->info($this->t("Sending a maximum number of @limit emails (@batch_size emails per batch run) for the email handler '@handler'.", $arguments));

    // Create the batch.
    try {
      EmailBatchHelper::create($handler, $currentBatchSize, $current_limit);
      $batchSet = TRUE;
    }
    catch (NlcEmailsException $e) {
      throw new ConsoleException($this->t("Couldn't create a batch, please check the batch size and limit parameters."));
    }

    return $batchSet;

  }

}
