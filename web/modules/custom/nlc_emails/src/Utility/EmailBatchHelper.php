<?php


namespace Drupal\nlc_emails\Utility;

use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\nlc_emails\Emails\AbstractNlcEmailHandlerHandler;
use Drupal\nlc_emails\Emails\NlcEmailHandlerInterface;
use Drupal\nlc_emails\NlcEmailsException;

class EmailBatchHelper {

  /**
   * The translation manager service.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface
   */
  protected static $translationManager;

  /**
   * Gets the translation manager.
   *
   * @return \Drupal\Core\StringTranslation\TranslationInterface
   *   The translation manager.
   */
  protected static function getStringTranslation() {
    if (!static::$translationManager) {
      static::$translationManager = \Drupal::service('string_translation');
    }
    return static::$translationManager;
  }

  /**
   * Sets the translation manager.
   *
   * @param \Drupal\Core\StringTranslation\TranslationInterface $translation_manager
   *   The new translation manager.
   */
  public static function setStringTranslation(TranslationInterface $translation_manager) {
    static::$translationManager = $translation_manager;
  }

  /**
   * Translates a string to the current language or to a given language.
   *
   * @see \Drupal\Core\StringTranslation\TranslationInterface::translate()
   */
  protected static function t($string, array $args = [], array $options = []) {
    return static::getStringTranslation()->translate($string, $args, $options);
  }

  /**
   * Formats a string containing a count of items.
   *
   * @see \Drupal\Core\StringTranslation\TranslationInterface::formatPlural()
   */
  protected static function formatPlural($count, $singular, $plural, array $args = [], array $options = []) {
    return static::getStringTranslation()->formatPlural($count, $singular, $plural, $args, $options);
  }

  /**
   * @param \Drupal\nlc_emails\Emails\NlcEmailHandlerInterface $handler
   * @param null $batch_size
   * @param int $limit
   *
   * @throws \Drupal\nlc_emails\NlcEmailsException
   */
  public static function create(NlcEmailHandlerInterface $handler, $batch_size = NULL, $limit = -1) {
    $batch_size = $batch_size ?? 50;

    // Check if indexing items is allowed.
    if ($batch_size !== 0 && $limit !== 0) {
      // Create a tracker for the email handler, and add it to the handler.
      $handlerTrackerName = $handler->handlerTrackerName();
      $handlerTracker = new $handlerTrackerName($handler);
      $handler->setTracker($handlerTracker);
      $emails = $handler->recipientEmailsTracker();
      $handler->getTrackerInstance()->trackEmailsInserted($emails);
      // Set the batch definition.
      $batch_definition = [
        'operations' => [
          [[__CLASS__, 'process'], [$handler, $batch_size, $limit]],
        ],
        'finished' => [__CLASS__, 'finish'],
        'progress_message' => static::t('Completed about @percentage% of the email sending operation (@current of @total).'),
      ];
      // Schedule the batch.
      batch_set($batch_definition);
    }
    else {
      $args = [
        '%batch_size' => $batch_size,
        '%limit' => $limit,
        '%handler' => $handler->emailHandlerMachineName(),
      ];
      $message = self::t("Failed to create a batch with batch size '%batch_size' and limit '%limit' for email handler '%handler'.", $args);
      throw new NlcEmailsException($message);
    }
  }

  /**
   * Processes an email batch operation.
   *
   * @param \Drupal\nlc_emails\Emails\AbstractNlcEmailHandlerHandler $handler
   *   The email handler on which emails should be sent.
   * @param int $batch_size
   *   The maximum number of emails to send per batch pass.
   * @param int $limit
   *   The maximum number of emails to send in total, or -1 to send all items.
   * @param array|\ArrayAccess $context
   *   The context of the current batch, as defined in the @link batch Batch
   *   operations @endlink documentation.
   */
  public static function process(AbstractNlcEmailHandlerHandler $handler, $batch_size, $limit, &$context) {
    // Check if the sandbox should be initialized.
    if (!isset($context['sandbox']['limit'])) {
      // Initialize the sandbox with data which is shared among the batch runs.
      $context['sandbox']['limit'] = $limit;
      $context['sandbox']['batch_size'] = $batch_size;
    }
    // Check if the results should be initialized.
    if (!isset($context['results']['sent'])) {
      // Initialize the results with data which is shared among the batch runs.
      $context['results']['sent'] = 0;
      $context['results']['not sent'] = 0;
    }
    // Get the remaining item count. When no valid tracker is available then
    // the value will be set to zero which will cause the batch process to
    // stop.
    $remaining_item_count = ($handler->hasValidTracker() ? $handler->getTrackerInstance()->getRemainingItemsCount() : 0);

    // Check if an explicit limit needs to be used.
    if ($context['sandbox']['limit'] > -1) {
      // Calculate the remaining amount of items that can be indexed. Note that
      // a minimum is taking between the allowed number of items and the
      // remaining item count to prevent incorrect reporting of not indexed
      // items.
      $actual_limit = min($context['sandbox']['limit'] - $context['results']['indexed'], $remaining_item_count);
    }
    else {
      // Use the remaining item count as actual limit.
      $actual_limit = $remaining_item_count;
    }

    // Store original count of items to be indexed to show progress properly.
    if (empty($context['sandbox']['original_item_count'])) {
      $context['sandbox']['original_item_count'] = $actual_limit;
    }

    // Determine the number of items to send for this run.
    $to_send_limit = min($actual_limit, $context['sandbox']['batch_size']);

    // Catch any exception that may occur whilst sending emails.
    try {
      print_r($to_send_limit);
      $sent = $handler->sendEmails($to_send_limit);
      // Increment the sent result and progress.
      $context['results']['sent'] += $sent;

      // Display progress message.
      if ($sent > 0) {
        $context['message'] = static::formatPlural($context['results']['sent'], 'Successfully sent 1 email.', 'Successfully sent @count emails.');
      }
      // Everything has been indexed?
      if ($sent === 0 || $context['results']['sent'] >= $context['sandbox']['original_item_count']) {
        $context['finished'] = 1;
        $context['results']['not sent'] = $context['sandbox']['original_item_count'] - $context['results']['sent'];
      }
      else {
        $context['finished'] = ($context['results']['sent'] / $context['sandbox']['original_item_count']);
      }

    }
    catch (\Exception $e) {
      // Log exception to watchdog and abort the batch job.
      watchdog_exception('nlc_emails', $e);
      $context['message'] = static::t('An error occurred whilst sending emails: @message', ['@message' => $e->getMessage()]);
      $context['finished'] = 1;
      $context['results']['not sent'] = $context['sandbox']['original_item_count'] - $context['results']['sent'];
    }

  }

  /**
   * Finishes an index batch.
   */
  public static function finish($success, $results, $operations) {
    // Check if the batch job was successful.
    if ($success) {
      // Display the number of items indexed.
      if (!empty($results['indexed'])) {
        // Build the indexed message.
        $indexed_message = static::formatPlural($results['indexed'], 'Successfully sent 1 email.', 'Successfully sent @count emails.');
        // Notify user about indexed items.
        \Drupal::messenger()->addStatus($indexed_message);
        // Display the number of items not indexed.
        if (!empty($results['not indexed'])) {
          // Build the not indexed message.
          $not_indexed_message = static::formatPlural($results['not indexed'], '1 email could not be sent. Check the logs for details.', '@count emails could not be sent. Check the logs for details.');
          // Notify user about not indexed items.
          \Drupal::messenger()->addWarning($not_indexed_message);
        }
      }
      else {
        // Notify user about failure to index items.
        \Drupal::messenger()->addError(static::t("Couldn't send emails. Check the logs for details."));
      }
    }
    else {
      // Notify user about batch job failure.
      \Drupal::messenger()->addError(static::t('An error occurred while trying to send emails. Check the logs for details.'));
    }
  }

}
