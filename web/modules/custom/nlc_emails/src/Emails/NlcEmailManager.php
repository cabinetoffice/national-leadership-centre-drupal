<?php

namespace Drupal\nlc_emails\Emails;

class NlcEmailManager implements NlcEmailManagerInterface {

  /**
   * Array of NLC emails.
   *
   * @var \Drupal\nlc_emails\Emails\NlcEmailHandlerInterface[]
   */
  protected $nlcEmails = [];

  /**
   * {@inheritDoc}
   */
  public function addNlcEmailHandler(NlcEmailHandlerInterface $emailHandler): NlcEmailManager {
    $this->nlcEmails[$emailHandler->emailHandlerMachineName()] = $emailHandler;
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function getEmailHandlers() {
    return $this->nlcEmails;
  }

  /**
   * {@inheritDoc}
   */
  public function getEmailHandlerMachineNames(): array {
    return array_keys($this->nlcEmails);
  }

  /**
   * {@inheritDoc}
   */
  public function getEmailHandler($type) {
    return $this->getEmailHandlerMachineNames()[$type] ?? false;
  }

  /**
   * {@inheritDoc}
   */
  public function getEmailHandlersSummary(): array {
    $handlers = [];
    foreach ($this->getEmailHandlers() as $emailHandler) {
      $handlers[$emailHandler->emailHandlerMachineName()] = [
        'machine_name' => $emailHandler->emailHandlerMachineName(),
        'name' => $emailHandler->emailHandlerName(),
      ];
    }
    return $handlers;
  }

}
