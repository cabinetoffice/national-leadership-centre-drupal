<?php

namespace Drupal\nlc_emails\Utility;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CommandHelper implements LoggerAwareInterface {

  use LoggerAwareTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

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
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher.
   * @param string|callable $translation_function
   *   (optional) A callable for translating strings.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ModuleHandlerInterface $module_handler, EventDispatcherInterface $event_dispatcher, $translation_function = 'dt') {
    $this->entityTypeManager = $entity_type_manager;
    $this->moduleHandler = $module_handler;
    $this->eventDispatcher = $event_dispatcher;
    $this->translationFunction = $translation_function;
  }

  public function listHandlersCommand() {
    /** @var \Drupal\nlc_emails\Emails\NlcEmailManager $handlersManager */
    $handlersManager = \Drupal::service('nlc_emails.email_manager');

    return  $handlersManager->getEmailHandlersSummary();
  }

}
