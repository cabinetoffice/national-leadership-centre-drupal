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
   * @OGM\Relationship(relationshipEntity="Drupal\nlc_network_individual\Model\Relationship\NetworkIndividualVisitOfRelationshipModel", type="visitOf", direction="INCOMING", mappedBy="person")
   */
  protected $visitOf;


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
    $this->setEntityId($entity->id());
    try {
      $this->entitySalesforceMappedObjects = $this->getSalesforceMappedObjects($this->entity);
      if (!empty($this->getEntitySalesforceMappedObjectsIds())) {
        $this->setSfId(current($this->getEntitySalesforceMappedObjectsIds()));
      }
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
    return empty($ids) ? ['entityId' => $this->getEntityId()] : ['sf_id' => current($ids)];
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

  /**
   * @param \Drupal\neo4j_db\Model\Relationship\RelationshipModelInterface $visitOf
   */
  public function setVisitOf($visitOf): void {
    $this->visitOf = $visitOf;
  }

  /**
   * @return string
   */
  public function getFirstName(): string {
    return $this->firstName;
  }

  /**
   * @param string $firstName
   */
  public function setFirstName(string $firstName): void {
    $this->firstName = $firstName;
  }

  /**
   * @return string
   */
  public function getLastName(): string {
    return $this->lastName;
  }

  /**
   * @param string $lastName
   */
  public function setLastName(string $lastName): void {
    $this->lastName = $lastName;
  }

  /**
   * @return string
   */
  public function getFullName(): string {
    return $this->fullName;
  }

  /**
   * @param string $fullName
   */
  public function setFullName(string $fullName): void {
    $this->fullName = $fullName;
  }

  /**
   * @return string
   */
  public function getTitle(): string {
    return $this->title;
  }

  /**
   * @param string $title
   */
  public function setTitle(string $title): void {
    $this->title = $title;
  }

  /**
   * @return string
   */
  public function getTwitterAccount(): string {
    return $this->twitter_account;
  }

  /**
   * @param string $twitter_account
   */
  public function setTwitterAccount(string $twitter_account): void {
    $this->twitter_account = $twitter_account;
  }

  /**
   * @return string
   */
  public function getLinkedinAccount(): string {
    return $this->linkedin_account;
  }

  /**
   * @param string $linkedin_account
   */
  public function setLinkedinAccount(string $linkedin_account): void {
    $this->linkedin_account = $linkedin_account;
  }

  /**
   * @return string
   */
  public function getSfId(): string {
    return $this->sf_id;
  }

  /**
   * @param string $sf_id
   */
  public function setSfId(string $sf_id): void {
    $this->sf_id = $sf_id;
  }

  /**
   * @return string
   */
  public function getSfRecordId(): string {
    return $this->sf_record_id;
  }

  /**
   * @param string $sf_record_id
   */
  public function setSfRecordId(string $sf_record_id): void {
    $this->sf_record_id = $sf_record_id;
  }

}
