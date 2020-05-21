<?php

namespace Drupal\nlc_emails\Emails\Handlers;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\nlc_emails\Emails\AbstractNlcEmailHandlerHandler;
use Drupal\nlc_emails\Emails\Email;

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
  public function getMailKey(): string {
    return $this->emailHandlerMachineName();
  }

  /**
   * {@inheritDoc}
   */
  public function recipients(): array {
    $recipients = [];
    $criteria = [
      'contains' => [
//        'mail' => 'joe.baker@weareconvivio.com',
        'mail' => '@weareconvivio.com',
      ],
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
  public function recipientEmailsTracker(): array {
    $emails = [];
    $recipients = $this->recipients();
    foreach ($recipients as $recipient) {
      $datasource = "{$recipient->getEntityType()->id()}:{$recipient->bundle()}";
      $item_id = implode(self::DATASOURCE_ID_SEPARATOR, [$datasource, $recipient->id()]);
      $email_context = [
        'machine_name' => $this->emailHandlerMachineName(),
        'datasource' => $datasource,
        'item_id' => $item_id,
        'uid' => $recipient->id(),
        'email' => $recipient->getEmail(),
      ];
      $emails[$item_id] = new Email($email_context);
    }
    return $emails;
  }

  /**
   * {@inheritDoc}
   */
  public function handlerTrackerName(): string {
    return '\Drupal\nlc_emails\Tracker\NlcLibraryIntroEmailTracker';
  }

  /**
   * {@inheritDoc}
   */
  public function emailSubject(): string {
    return $this->t('Check out the Connect Library');
  }

  /**
   * {@inheritDoc}
   */
  public function emailBody(array $context): string {
    $body = sprintf('<p>Hello %s</p>', $context['name']);
    return $body;
//    $body = [
//      '#type' => 'inline_template',
//      '#template' => '<p>Hello {{ name }}</p>',
//      '#context' => $context,
//    ];
//    $build = render($body);
//    return \Drupal::service('renderer')
//      ->renderRoot($build);
  }

}
