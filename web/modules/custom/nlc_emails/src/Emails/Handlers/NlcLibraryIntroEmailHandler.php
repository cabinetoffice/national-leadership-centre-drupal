<?php

namespace Drupal\nlc_emails\Emails\Handlers;

use Drupal\nlc_emails\Emails\AbstractNlcEmailHandlerHandler;

/**
 * An email handler for sending an email intro to the Library.
 *
 * @package Drupal\nlc_emails\Emails\Handlers
 */
class NlcLibraryIntroEmailHandler extends AbstractNlcEmailHandlerHandler {

  /**
   * {@inheritDoc}
   */
  public function emailHandlerMachineName(): string {
    return 'nlc_library_intro';
  }

  /**
   * {@inheritDoc}
   */
  public function emailHandlerName(): string {
    return 'NLC Library introduction email';
  }

  /**
   * {@inheritDoc}
   */
  public function recipients(): array {
    $recipients = [];
    $criteria = [
      'contains' => [
        'mail' => '@weareconvivio',
      ]
    ];
    try {
      $recipients = $this->getUsersByCriteria($criteria);
    }
    catch (\Exception $exception) {
      // Do something?
    }
    return $recipients;
  }

  /**
   * {@inheritDoc}
   */
  public function handlerTracker(): string {
    return '\Drupal\nlc_emails\Tracker\NlcLibraryIntroEmailTracker';
  }

  /**
   * {@inheritDoc}
   */
  public function sendEmails() {
    // TODO: Implement sendEmails() method.
  }

  /**
   * {@inheritDoc}
   */
  public function sendSpecificEmails(array $emails) {
    // TODO: Implement sendSpecificEmails() method.
  }

}
