<?php


namespace Drupal\nlc_emails\Emails;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Render\RenderableInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\nlc_emails\Tracker\TrackerInterface;

interface NlcEmailHandlerInterface {

  /**
   * String used to separate a datasource prefix from the rest of an identifier.
   *
   * Internal field identifiers of datasource-dependent fields in the Search API
   * consist of two parts: the ID of the datasource to which the field belongs;
   * and the property path to the field, with properties separated by colons.
   * The two parts are concatenated using this character as a separator to form
   * the complete field identifier. (In the case of datasource-independent
   * fields, the identifier doesn't contain the separator.)
   *
   * Likewise, internal item IDs consist of the datasource ID and the item ID
   * within that datasource, separated by this character.
   */
  const DATASOURCE_ID_SEPARATOR = '/';

  /**
   * Get the entity type manager.
   *
   * @return \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  public function getEntityTypeManager(): EntityTypeManagerInterface;

  /**
   * Set the entity type manager.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *  The entity type manager.
   */
  public function setEntityTypeManager(EntityTypeManagerInterface $entityTypeManager): void;

  /**
   * A machine name for the email handler.
   *
   * @return string
   */
  public function emailHandlerMachineName(): string;

  /**
   * The email handler name.
   *
   * @return string
   */
  public function emailHandlerName(): string;

  /**
   * An associative array of email recipients.
   *
   * @return \Drupal\user\UserInterface[]
   */
  public function recipients(): array;

  /**
   * An associative array of emails to be inserted into the email tracker.
   *
   * @return array
   */
  public function recipientEmailsTracker(): array;

  /**
   * Sends a set amount of emails.
   *
   * Will fetch the emails to be sent from the handler's tracker and send them to
   * sendSpecificItems(). It will then mark all emails sent successfully as such in
   * the handler tracker.
   *
   * @param int $limit
   *   (optional) The maximum number of emails to send, or -1 to send all
   *   items.
   *
   * @return int
   *   The number of emails successfully sent.
   */
  public function sendEmails($limit): int;

  /**
   * Sends some emails on this handler.
   *
   * Will return the IDs of emails that were marked as sent – that is, emails
   * that were either rejected from sending (by a processor or alter hook) or
   * were successfully sent.
   *
   * @param array $emails
   *   An array of emails to be sent.
   *
   * @return string[]
   *   The IDs of all emails that should be marked as sent.
   *
   * @throws \Drupal\nlc_emails\NlcEmailsException
   *   Thrown if any error occurred whilst sending emails.
   */
  public function sendSpecificEmails(array $emails);

  /**
   * Retrieves an option.
   *
   * @param string $name
   *   The name of an option.
   * @param mixed $default
   *   The value return if the option wasn't set.
   *
   * @return mixed
   *   The value of the option.
   *
   * @see getOptions()
   */
  public function getOption($name, $default = NULL);

  /**
   * Retrieves an array of all options.
   *
   * The following options are known:
   * - cron_limit: The maximum number of items to be indexed per cron batch.
   * - index_directly: Boolean setting whether entities are indexed immediately
   *   after they are created or updated.
   *
   * @return array
   *   An associative array of option values, keyed by the option name.
   */
  public function getOptions();

  /**
   * Sets an option.
   *
   * @param string $name
   *   The name of an option.
   * @param mixed $option
   *   The new option.
   *
   * @return $this
   */
  public function setOption($name, $option);

  /**
   * Sets the handler's options.
   *
   * @param array $options
   *   The new handler options.
   *
   * @return $this
   */
  public function setOptions(array $options);

  /**
   * The class name of the tracker for this email handler.
   *
   * @return string
   */
  public function handlerTrackerName(): string;

  /**
   * The email subject line.
   *
   * @return string
   */
  public function emailSubject(): string;

  /**
   * The email body.
   *
   * @param array $context
   *   A context array for an email template.
   *
   * @return \Drupal\Core\Render\RendererInterface
   */
  public function emailBody(array $context): RendererInterface;

  /**
   * Determines whether the tracker is valid.
   *
   * @return bool
   *   TRUE if the tracker is valid, otherwise FALSE.
   */
  public function hasValidTracker(): bool;

  /**
   * Retrieves the tracker plugin.
   *
   * @return \Drupal\nlc_emails\Tracker\TrackerInterface
   *   The email handler's tracker plugin.
   *
   * @throws \Drupal\nlc_emails\NlcEmailsException
   *   Thrown if the tracker couldn't be instantiated.
   */
  public function getTrackerInstance();

  /**
   * Sets the tracker the index uses.
   *
   * @param \Drupal\nlc_emails\Tracker\TrackerInterface $tracker
   *   The new tracker for the index.
   *
   * @return $this
   */
  public function setTracker(TrackerInterface $tracker);

}
