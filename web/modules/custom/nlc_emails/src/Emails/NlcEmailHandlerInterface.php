<?php


namespace Drupal\nlc_emails\Emails;

interface NlcEmailHandlerInterface {

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

}
