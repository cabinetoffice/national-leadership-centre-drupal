<?php

namespace Drupal\nlc_library\Commands;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Drupal\nlc_library\Model\ModelInterface;
use Drupal\nlc_library\Model\Trello\CustomFieldItemModel;
use Drupal\nlc_library\Model\Trello\CustomFieldModel;
use Drupal\nlc_library\Model\Trello\TrelloTermModelInterface;
use Drupal\nlc_library\Model\Trello\CardModel;
use Drupal\nlc_library\Model\Trello\LabelModel;
use Drupal\nlc_library\Model\Trello\ListModel;
use Drush\Commands\DrushCommands;
use Symfony\Component\Console\Helper\TableCell;

/**
 * A Drush commandfile.
 *
 * In addition to this file, you need a drush.services.yml
 * in root of your module, and a composer.json file that provides the name
 * of the services file to use.
 *
 * See these files for an example of injecting Drupal services:
 *   - http://cgit.drupalcode.org/devel/tree/src/Commands/DevelCommands.php
 *   - http://cgit.drupalcode.org/devel/tree/drush.services.yml
 */
class NlcLibraryCommands extends DrushCommands {

  private $trelloBoardId = 'X1OQEy5Q';

  /**
   * @var array
   */
  private $trelloTopics = [];

  /**
   * @var array
   */
  private $trelloLabels = [];

  /**
   * @var \Drupal\nlc_library\Model\Trello\CustomFieldModel[]
   */
  private $trelloCustomFields;

  public function __construct() {
  }

  /**
   * Import library items from a local Trello board export JSON file.
   *
   * @command nlc_library:trello_import
   * @aliases nlcl-ti
   * @field-labels
   *   title: Title
   *   nid: NID
   *   topic: Topic
   *
   * @return \Consolidation\OutputFormatters\StructuredData\RowsOfFields
   *   The objects.
   */
  public function importTrelloFromJson() {
    $moduleHandler = \Drupal::service('module_handler');
    $base_path = getcwd() . '/' . $moduleHandler->getModule('nlc_library')->getPath();
    $path = implode('/', [$base_path, 'resources', 'trello', $this->trelloBoardId]) . '.json';
    $json = file_get_contents($path);
    $data = json_decode($json);
    $success = [];
    // Make sure we have all the topics in Connect.
    foreach ($data->lists as $list) {
      $listModel = new ListModel($list);
      if ($listModel->shouldExcludeId()) {
        continue;
      }
      try {
        $term = $this->getListItemTopic($listModel);
        $this->trelloTopics[$listModel->getId()] = $term;
      }
      catch (\Exception $e) {
        // Do something?
      }
    }
    // Make sure we have all the Trello labels in Connect.
    foreach ($data->labels as $label) {
      $labelModel = new LabelModel($label);
      try {
        if ($labelTerm = $this->getLLabelItemLabel($labelModel)) {
          $this->trelloLabels[$labelModel->getId()] = $labelTerm;
        }
      }
      catch (\Exception $e) {
        // Do something?
      }
    }
    // Make sure we have a model for all the Trello custom fields
    foreach ($data->customFields as $customField) {
      $customField = new CustomFieldModel($customField);
      $this->trelloCustomFields[$customField->getId()] = $customField;
    }
    foreach ($data->cards as $card) {
      $cardModel = new CardModel($card, $this->trelloCustomFields);
      if ($cardTerm = $this->getTrelloTopicById($cardModel->getListId())) {
        $cardModel->setTopicTerm($cardTerm);
        if (!empty($cardModel->getLabelIds())) {
          $labelId = current($cardModel->getLabelIds());
          if ($labelTerm = $this->getTrelloLabelById($labelId)) {
            $cardModel->setLabelTerm($labelTerm);
          }
        }
        /** @var \Drupal\Core\Entity\EntityInterface $externalLink */
        if ($externalLink = $this->mergeExternalLinkNode($cardModel)) {
//          $success[$externalLink->id()][] = $externalLink;
          $success[$externalLink->id()]['title']  = new TableCell($externalLink->label());
          $success[$externalLink->id()]['nid'] = new TableCell($externalLink->id());
          $success[$externalLink->id()]['topic'] = new TableCell($cardTerm->getName());
        }
      }
    }
    return new RowsOfFields($success);
  }

  /**
   * @return array
   */
  private function getTrelloTopics(): array {
    return $this->trelloTopics;
  }

  /**
   * @return array
   */
  private function getTrelloLabels(): array {
    return $this->trelloLabels;
  }

  /**
   * @param string $id
   *
   * @return \Drupal\taxonomy\TermInterface|bool
   */
  private function getTrelloTopicById($id) {
    return !empty($this->getTrelloTopics()[$id]) ? $this->getTrelloTopics()[$id] : false;
  }

  /**
   * @param string $id
   *
   * @return \Drupal\taxonomy\TermInterface|bool
   */
  private function getTrelloLabelById($id) {
    return !empty($this->getTrelloLabels()[$id]) ? $this->getTrelloLabels()[$id] : false;
  }

  /**
   * @param \Drupal\nlc_library\Model\ModelInterface $model
   * @param string $entityType
   * @param array $properties
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  private function loadEntitiesFromModel(ModelInterface $model, $entityType, array $properties) {
    return \Drupal::entityTypeManager()->getStorage($entityType)->loadByProperties($properties);
  }

  /**
   * @param \Drupal\nlc_library\Model\Trello\ListModel $model
   *
   * @return \Drupal\Core\Entity\EntityInterface
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  private function getListItemTopic(ListModel $model) {
    return $this->getTermItem($model);
  }

  /**
   * @param \Drupal\nlc_library\Model\Trello\LabelModel $model
   *
   * @return \Drupal\Core\Entity\EntityInterface|bool
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  private function getLLabelItemLabel(LabelModel $model) {
     return $model->getName() ? $this->getTermItem($model) : false;
  }

  /**
   * Get the topic Term entity for a list item.
   *
   * @param \Drupal\nlc_library\Model\Trello\TrelloTermModelInterface $model
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  private function getTermItem(TrelloTermModelInterface $model) {
    // Try to load by Trello ID
    $baseProperties = ['vid' => $model->vocabulary()];
    $properties = array_merge($baseProperties, ['field_trello_id' => $model->getId()]);
    $entities = $this->loadEntitiesFromModel($model, 'taxonomy_term', $properties);
    if (empty($entities)) {
      $properties = array_merge($baseProperties, ['name' => $model->getName()]);
      $entities = $this->loadEntitiesFromModel($model, 'taxonomy_term', $properties);
    }
    if (empty($entities)) {
      // There is not current term, so create one.
      foreach ($model->getProperties() as $field => $method) {
        $properties[$field] = $model->$method();
      }
      $properties = array_merge($baseProperties, $properties);
      $term = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->create($properties);
      $term->save();
    }
    else {
      // Make sure the current term is up-to-date with the Trello values — name and ID.
      /** @var \Drupal\taxonomy\Entity\Term $entity */
      $term = current($entities);
      $term->setName($model->getName());
      foreach ($model->getProperties() as $field => $method) {
        $term->set($field, $model->$method());
      }
      $term->save();
    }
    return $term;
  }

  /**
   * @param \Drupal\nlc_library\Model\Trello\CardModel $model
   *
   * @return \Drupal\Core\Entity\EntityInterface|\Drupal\node\Entity\Node
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  private function mergeExternalLinkNode(CardModel $model) {
    $customFields = [
      'field_read_time',
      'field_published_on',
    ];
    $baseProperties = ['type' => 'external_link'];
    $properties = array_merge($baseProperties, ['field_trello_id' => $model->getId()]);
    $entities = $this->loadEntitiesFromModel($model, 'node', $properties);
    if (empty($entities)) {
      // Cannot load by Trello ID, so try to load by title (i.e. Trello name)
      $properties = array_merge($baseProperties, ['title' => $model->getName()]);
      $entities = $this->loadEntitiesFromModel($model, 'node', $properties);
    }
    if (empty($entities)) {
      // There is no current external_link node item, so create one.
      $properties = array_merge($baseProperties, [
        'title' => $model->getName(),
        'body' => [
          'value' => $model->getDescription(),
          'format' => 'markdown',
        ],
        'field_trello_id' => $model->getId(),
        'field_topic' => $model->getTopicTerm(),
        'field_label' => $model->getLabelTerm(),
        'field_url' => $model->getFirstAttachment()->getUrl(),
        'created' => $model->getLastActivityDateTime()->format('U'),
        'changed' => $model->getLastActivityDateTime()->format('U'),
      ]);
      foreach ($customFields as $customField) {
        if ($model->getCustomFieldItem($customField) instanceof CustomFieldItemModel) {
          $properties[$customField] = $model->getCustomFieldItem($customField)->getValue();
        }
      }
      $entity = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->create($properties);
      $entity->save();
    }
    else {
      /** @var \Drupal\node\Entity\Node $entity */
      $entity = current($entities);
      $entity->setTitle($model->getName())
        ->set('body', ['value' => $model->getDescription(), 'format' => 'markdown'])
        ->set('field_trello_id', $model->getId())
        ->set('field_topic', $model->getTopicTerm())
        ->set('field_label', $model->getLabelTerm())
        ->set('field_url', $model->getFirstAttachment() ? $model->getFirstAttachment()->getUrl() : '');
      foreach ($customFields as $customField) {
        if ($model->getCustomFieldItem($customField) instanceof CustomFieldItemModel) {
          $entity->set($customField, $model->getCustomFieldItem($customField)
            ->getValue());
        }
      }
      if ($updated = $model->getLastActivityDateTime()) {
        $entity->set('changed', $updated->format('U'));
      }
      $entity->save();
    }
    return $entity;
  }

}
