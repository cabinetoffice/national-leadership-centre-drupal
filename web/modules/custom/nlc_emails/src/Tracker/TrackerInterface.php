<?php

namespace Drupal\nlc_emails\Tracker;

interface TrackerInterface {

  /**
   * Inserts new items into the tracking system for this handler.
   *
   * @param string[] $ids
   *   The item IDs of the new email handler items.
   */
  public function trackEmailsInserted(array $ids);

  /**
   * Marks emails as sent for this handler.
   *
   * @param string[] $ids
   *   An array of item IDs.
   */
  public function trackEmailsSent(array $ids);

  /**
   * Retrieves a list of email IDs that need to be sent.
   *
   * @param int $limit
   *   (optional) The maximum number of items to return. Or a negative value to
   *   return all remaining items.
   * @param string|null $machine_name
   *   (optional) If specified, only items of the handler machine_name with that
   *   ID are retrieved.
   *
   * @return string[]
   *   The IDs of items that still need to be indexed.
   */
  public function getRemainingItems($limit = -1, $machine_name = NULL);

  /**
   * Retrieves the total number of items that are being tracked for this email handler.
   *
   * @param string|null $machine_name
   *   (optional) The handler machine name to filter the total number of items by.
   *
   * @return int
   *   The total number of emails to be sent by this handler.
   */
  public function getTotalItemsCount(?string $machine_name = NULL);

  /**
   * Retrieves the number of sent emails for this handler.
   *
   * @param string|null $machine_name
   *   (optional) The handler machine name to filter the total number of sent items by.
   *
   * @return int
   *   The number of emails that have been sent in their latest state for this
   *   handler.
   */
  public function getSentItemsCount(?string $machine_name = NULL);

  /**
   * Retrieves the total number of pending items for this email handler.
   *
   * @param string|null $machine_name
   *   (optional) The datasource to filter the total number of pending items by.
   *
   * @return int
   *   The total number of emails that still need to be sent for this handler.
   */
  public function getRemainingItemsCount(?string $machine_name = NULL);

}
