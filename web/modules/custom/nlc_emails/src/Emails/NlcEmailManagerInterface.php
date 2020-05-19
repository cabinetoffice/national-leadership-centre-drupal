<?php

namespace Drupal\nlc_emails\Emails;

interface NlcEmailManagerInterface {

  /**
   * Appends an NLC email handler to the handler list.
   *
   * @param \Drupal\nlc_emails\Emails\NlcEmailHandlerInterface $emailHandler
   *
   * @return \Drupal\nlc_emails\Emails\NlcEmailManager
   */
  public function addNlcEmailHandler(NlcEmailHandlerInterface $emailHandler): NlcEmailManager ;

  /**
   * Get the list of email handlers.
   *
   * @return \Drupal\nlc_emails\Emails\NlcEmailHandlerInterface[]
   */
  public function getEmailHandlers();

  /**
   * Get an array of the declared email handler machine names.
   *
   * @return array
   */
  public function getEmailHandlerMachineNames(): array ;

  /**
   * Get an email handler by type.
   *
   * @param string $type
   *   The email handler type.
   *
   * @return \Drupal\nlc_emails\Emails\NlcEmailHandlerInterface|bool
   *   The email handler of the given type, or false if it does not exist.
   */
  public function getEmailHandler($type);

  /**
   * Array of available email handler names, keyed by machine name.
   *
   * @return array
   */
  public function getEmailHandlersSummary(): array ;

}
