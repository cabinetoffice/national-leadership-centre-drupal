<?php


namespace Drupal\nlc_emails\Tracker;

class NlcLibraryIntroEmailTracker extends AbstractTrackerBase {

  public function trackEmailsInserted(array $ids) {
    // TODO: Implement trackEmailsInserted() method.
  }

  public function trackEmailsSent(array $ids) {
    // TODO: Implement trackEmailsSent() method.
  }

  public function getRemainingItems($limit = -1, $machine_name = NULL) {
    // TODO: Implement getRemainingItems() method.
  }

  /**
   * @inheritDoc
   */
  public function getTotalItemsCount(?string $machine_name = NULL) {
    // TODO: Implement getTotalItemsCount() method.
  }

  /**
   * {@inheritDoc}
   */
  public function getSentItemsCount(?string $machine_name = NULL) {
    // TODO: Implement getSentItemsCount() method.
  }

  /**
   * {@inheritDoc}
   */
  public function getRemainingItemsCount(?string $machine_name = NULL) {
    // TODO: Implement getRemainingItemsCount() method.
  }

}
