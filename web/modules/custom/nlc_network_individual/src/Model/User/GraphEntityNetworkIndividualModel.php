<?php

namespace Drupal\nlc_network_individual\Model\User;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Entity\EntityInterface;
use Drupal\neo4j_db_entity\Model\User\GraphEntityUserUserModel;
use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * Class GraphEntityNetworkIndividualModel
 *
 * @package Drupal\nlc_network_individual\Model\User
 *
 * @OGM\Node(label="Person")
 */
class GraphEntityNetworkIndividualModel extends GraphEntityUserUserModel {

  protected $entityType = 'user';

  protected $bundle = 'user';

  protected $type = 'NetworkIndividual';

  /**
   * @var string
   *
   * @OGM\Property(type="string")
   */
  protected $firstName;

  /**
   * @var string
   *
   * @OGM\Property(type="string")
   */
  protected $lastName;

  /**
   * @var string
   *
   * @OGM\Property(type="string")
   */
  protected $fullName;

  /**
   * @var string
   *
   * @OGM\Property(type="string")
   */
  protected $sf_id;

  /**
   * @var string
   *
   * @OGM\Property(type="string")
   */
  protected $sf_record_id;

  /**
   * @var string
   *
   * @OGM\Property(type="string")
   */
  protected $title;

  /**
   * @var string
   *
   * @OGM\Property(type="string")
   */
  protected $twitter_account;

  /**
   * @var string
   *
   * @OGM\Property(type="string")
   */
  protected $linkedin_account;

  /**
   * The Mapped Objects corresponding to the given entity.
   *
   * @var \Drupal\salesforce_mapping\Entity\MappedObject[]
   */
  protected $entitySalesforceMappedObjects = [];

  public function getGraphEntity() {

  }

  /**
   * {@inheritDoc}
   *
   */
  public function setEntity(EntityInterface $entity): void {
    $this->entity = $entity;
    try {
      $this->entitySalesforceMappedObjects = $this->getSalesforceMappedObjects($this->entity);
      $this->setFindOneByCriteria($this->baseFindOneByCriteria());
    }
    catch (InvalidPluginDefinitionException | PluginNotFoundException $e) {
      // Do something?
    }
  }

  /**
   * {@inheritDoc}
   */
  public function baseFindOneByCriteria() {
    $ids = $this->getEntitySalesforceMappedObjectsIds();
    return ['sf_id' => current($ids)];
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
   * Helper function to fetch existing MappedObject or create a new one.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to be mapped.
   *
   * @return \Drupal\salesforce_mapping\Entity\MappedObject[]
   *   The Mapped Objects corresponding to the given entity.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getSalesforceMappedObjects(EntityInterface $entity) {
    return $this
      ->entityTypeManager()
      ->getStorage('salesforce_mapped_object')
      ->loadByEntity($entity);
  }

  /**
   * @return \Drupal\salesforce_mapping\Entity\MappedObject[]
   */
  public function getEntitySalesforceMappedObjects(): array {
    return $this->entitySalesforceMappedObjects;
  }

  /**
   * @return array
   *
   */
  public function getEntitySalesforceMappedObjectsIds(): array {
    $ids = [];
    if (!empty($this->getEntitySalesforceMappedObjects())) {
      foreach ($this->entitySalesforceMappedObjects as $key => $salesforceMappedObject) {
        $ids[$key] = $salesforceMappedObject->sfid();
      }
    }
    return $ids;
  }



}
