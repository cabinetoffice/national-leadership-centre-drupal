<?php

namespace Drupal\nlc_network_individual\EventSubscriber;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\TypedData\Exception\MissingDataException;
use Drupal\neo4j_db_entity\EventSubscriber\AbstractUserUpdateSubscriber;

class NetworkIndividualUpdateSubscriber extends AbstractUserUpdateSubscriber {

  /**
   * {@inheritDoc}
   */
  protected function subscriberModelService() {
    return 'network_individual.model.user';
  }

  /**
   * {@inheritDoc}
   */
  protected function setAccountModelExtraFieldValues(): void {
    if (strpos($this->account()->get('name')->getString(), 'NI-') === 0) {
      $this->accountNode()->setSfRecordId($this->account()->get('name')->getString());
    }
    $this->setNameFieldValues();
  }

  /**
   * Set the model value for the Drupal name field.
   *
   * @return void
   */
  protected function setNameFieldValues(): void {
    try {
      // Try to get the field_name values for the
      /** @var \Drupal\name\Plugin\Field\FieldType\NameItem $accountNameField */
      $accountNameField = $this->account()->get('field_name')->get(0);
//      dpm($accountNameField);
      $this->accountNode()->setFirstName($accountNameField->get('given')->getValue());
      $this->accountNode()->setLastName($accountNameField->get('family')->getValue());
      $this->accountNode()->setTitle($accountNameField->get('title')->getValue());
      if (isset($this->account()->realname) && mb_strlen($this->account()->realname)) {
        $this->accountNode()->setFullName($this->account()->realname);
      }
    }
    catch (MissingDataException $e) {
      // Do something?
    }
  }

}
