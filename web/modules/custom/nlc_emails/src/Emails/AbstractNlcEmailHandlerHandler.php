<?php


namespace Drupal\nlc_emails\Emails;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\nlc_emails\LoggerTrait;
use Drupal\nlc_emails\NlcEmailsException;
use Drupal\nlc_emails\Tracker\TrackerInterface;
use Drupal\nlc_emails\Utility\Utility;
use Drupal\user\Entity\User;

abstract class AbstractNlcEmailHandlerHandler implements NlcEmailHandlerInterface {

  use StringTranslationTrait;
  use LoggerTrait;

  /**
   * Constructor for the AbstractNlcEmailHandlerHandler.
   */
  public function __construct() {
  }


  /**
   * The entity type manager
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * An array of options configuring this email handler.
   *
   * @var array
   *
   * @see getOptions()
   */
  protected $options = [];

  /**
   * The tracker plugin instance.
   *
   * @var \Drupal\nlc_emails\Tracker\TrackerInterface|null
   *
   * @see getTrackerInstance()
   */
  protected $trackerInstance;

  /**
   * {@inheritDoc}
   */
  public function getEntityTypeManager(): EntityTypeManagerInterface {
    return $this->entityTypeManager;
  }


  /**
   * {@inheritDoc}
   */
  public function setEntityTypeManager(EntityTypeManagerInterface $entityTypeManager): void {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * @param array $criteria
   *
   * @return \Drupal\user\UserInterface[]
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getUsersByCriteria($criteria) {
    $baseProperties = [];
    $properties = array_merge($baseProperties, $criteria);
    return $this->loadUsersByProperties($properties);
  }

  /**
   * @param array $properties
   *
   * @return \Drupal\user\UserInterface[]
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function loadUsersByProperties($properties) {
    $query = \Drupal::entityTypeManager()->getStorage('user')->getQuery();
    foreach ($properties as $type => $property) {
      foreach ($property as $field => $value) {
        $query->condition($field, $value, strtoupper($type));
      }
    }
    $result = $query->execute();
    /** @var \Drupal\user\UserInterface[] $users */
    $users = User::loadMultiple($result);
    return $users;
  }

  /**
   * {@inheritdoc}
   */
  public function getOption($name, $default = NULL) {
    return $this->options[$name] ?? $default;
  }
  /**
   * {@inheritdoc}
   */
  public function getOptions() {
    return $this->options;
  }

  /**
   * {@inheritdoc}
   */
  public function setOption($name, $option) {
    $this->options[$name] = $option;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOptions(array $options) {
    $this->options = $options;
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function hasValidTracker(): bool {
    return isset($this->trackerInstance) && $this->trackerInstance instanceof TrackerInterface;
  }

  /**
   * {@inheritDoc}
   */
  public function getTrackerInstance() {
    return $this->trackerInstance;
  }

  /**
   * {@inheritdoc}
   */
  public function setTracker(TrackerInterface $tracker) {
    $this->trackerInstance = $tracker;
    return $this;
  }

  public function sendEmails($limit): int {
    print_r($limit);
    if ($this->hasValidTracker()) {
      $tracker = $this->getTrackerInstance();
      $next_set = $tracker->getRemainingItems($limit, $this->emailHandlerMachineName());
      print_r($next_set);
      if (!$next_set) {
        return 0;
      }
      $items = $this->loadEmailsMultiple($next_set);
      if (!$items) {
        return 0;
      }
      try {
        return count($this->sendSpecificEmails($items));
      }
      catch (NlcEmailsException $e) {
        $variables['%handler'] = $this->emailHandlerMachineName();
        $this->logException($e, '%type while trying to send emails on handler %handler: @message in %function (line %line of %file)', $variables);
      }
    }
    return 0;
  }

  /**
   * {@inheritDoc}
   */
  public function sendSpecificEmails(array $email_objects) {
    if (!$email_objects) {
      return [];
    }
    /** @var \Drupal\nlc_emails\Emails\Email[] $emails */
    $emails = [];
//    foreach ($email_objects)

  }

  /**
   * {@inheritdoc}
   */
  public function loadEmailsMultiple(array $item_ids) {
    // Group the requested items by datasource. This will also later be used to
    // determine whether all items were loaded successfully.
    $items_by_datasource = [];
    foreach ($item_ids as $item_id) {
      [$datasource, $raw_id] = Utility::splitCombinedId($item_id);
      $items_by_datasource[$datasource][$raw_id] = $item_id;
    }

    // Load the items from the datasources and keep track of which were
    // successfully retrieved.
    $items = [];
    foreach ($items_by_datasource as $datasource => $raw_ids) {
      try {
        $datasource_items = $datasource->loadMultiple(array_keys($raw_ids));
        foreach ($datasource_items as $raw_id => $item) {
          $id = $raw_ids[$raw_id];
          $items[$id] = $item;
          // Remember that we successfully loaded this item.
          unset($items_by_machine_name[$machine_name][$raw_id]);
        }
      }
      catch (NlcEmailsException $e) {
        $this->logException($e);
        // If the complete datasource could not be loaded, don't report all its
        // individual requested items as missing.
        unset($items_by_machine_name[$machine_name]);
      }
    }

    // Check whether there are requested items that couldn't be loaded.
    $items_by_machine_name = array_filter($items_by_machine_name);
    if ($items_by_machine_name) {
      // Extract the second-level values of the two-dimensional array (that is,
      // the combined item IDs) and log a warning reporting their absence.
      $missing_ids = array_reduce(array_map('array_values', $items_by_machine_name), 'array_merge', []);
      $args['%index'] = $this->label();
      $args['@items'] = '"' . implode('", "', $missing_ids) . '"';
      $this->getLogger()->warning('Could not load the following items on index %index: @items.', $args);
      // Also remove those items from tracking so we don't keep trying to load
      // them.
      foreach ($items_by_machine_name as $machine_name => $raw_ids) {
        $this->trackItemsDeleted($machine_name, array_keys($raw_ids));
      }
    }

    // Return the loaded items.
    return $items;
  }

}
