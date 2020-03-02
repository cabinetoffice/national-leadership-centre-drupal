<?php

namespace Drupal\nlc_prototype\SalesforceMapping;

use Drupal\Core\Session\AccountProxyInterface;

class NetworkIndividualMapping {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var AccountProxyInterface
   */
  protected $account;

  public function __construct(AccountProxyInterface $account) {
    // Use the full current user.
    $this->account = \Drupal\user\Entity\User::load($account->id());;
  }

  /**
   * @return \Drupal\salesforce_mapping\Entity\MappedObjectInterface|boolean
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getAccountSfMappedObject() {
    /** @var \Drupal\salesforce_mapping\MappedObjectStorage $mapped_object_storage */
    $mapped_object_storage = &drupal_static(__FUNCTION__);

    if (!isset($mapped_object_storage)) {
      $mapped_object_storage = \Drupal::service('entity_type.manager')
        ->getStorage('salesforce_mapped_object');
    }

    $sfEntityObjects = $this->getMappedSfObjects();
    if (is_array($sfEntityObjects)) {
      $sfEntity = current($sfEntityObjects);
      // We're performing a check to see if we're dealing with a Salesforce Mapped
      // Object.  The reason we do this instead of just checking to see if we're
      // dealing with a Commerce Product Variation is that we could very well have
      // product variations that are not being managed by the SFDC integration. If
      // this is the case, we do not want to be programmatically manipulating these
      // objects.
      if ($sfEntity->getEntityTypeId() == 'salesforce_mapped_object') {
        return $sfEntity;
      }
    }
    return FALSE;
  }

  /**
   * Helper function to fetch existing MappedObject or create a new one.
   *
   * @return \Drupal\salesforce_mapping\Entity\MappedObject[]
   *   The Mapped Objects corresponding to the given entity.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  private function getMappedSfObjects() {
    // @TODO this probably belongs in a service
    return $this
      ->entityTypeManager()
      ->getStorage('salesforce_mapped_object')
      ->loadByEntity($this->account);
  }

  /**
   * Retrieves the entity type manager.
   *
   * @return \Drupal\Core\Entity\EntityTypeManagerInterface
   *   The entity type manager.
   */
  protected function entityTypeManager() {
    if (!isset($this->entityTypeManager)) {
      $this->entityTypeManager = $this->container()->get('entity_type.manager');
    }
    return $this->entityTypeManager;
  }

  /**
   * Returns the service container.
   *
   * This method is marked private to prevent sub-classes from retrieving
   * services from the container through it. Instead,
   * \Drupal\Core\DependencyInjection\ContainerInjectionInterface should be used
   * for injecting services.
   *
   * @return \Symfony\Component\DependencyInjection\ContainerInterface
   *   The service container.
   */
  private function container() {
    return \Drupal::getContainer();
  }
}
