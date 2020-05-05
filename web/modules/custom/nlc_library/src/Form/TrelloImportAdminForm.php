<?php

namespace Drupal\nlc_library\Form;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\nlc_library\Model\ModelInterface;
use Drupal\nlc_library\Model\Trello\CardModel;
use Drupal\nlc_library\Model\Trello\ListModel;
use Drupal\taxonomy\Entity\Term;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TrelloImportAdminForm extends FormBase {

  private $trelloBoardId = 'X1OQEy5Q';

  private $trelloTopics = [];

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'nlc_library_trello_import_admin_form';
  }

  protected function getEditableConfigNames() {
    return [
      'nlc_library.trello'
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['parse_json'] = [
      '#type' => 'submit',
      '#value' => $this->t('Import Trello content from file'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->configFactory()->getEditable('nlc_library.trello');
    $config->set('last_import', \Drupal::time()->getRequestTime())
      ->save();

    $this->parseTrelloJson();
  }

  private function parseTrelloJson() {
    $moduleHandler = \Drupal::service('module_handler');
    $base_path = $moduleHandler->getModule('nlc_library')->getPath();
    $path = implode('/', [$base_path, 'resources', 'trello', $this->trelloBoardId]) . '.json';
    $json = file_get_contents($path);
    $data = json_decode($json);
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
    foreach ($data->cards as $card) {
      $cardModel = new CardModel($card);
      if ($cardTerm = $this->getTrelloTopicById($cardModel->getListId())) {
        $cardModel->setTopicTerm($cardTerm);
        $externalLink = $this->mergeExternalLinkNode($cardModel);
        dpm("{$externalLink->id()} | {$externalLink->label()}");
      }
    }
  }

  /**
   * @return array
   */
  public function getTrelloTopics(): array {
    return $this->trelloTopics;
  }

  /**
   * @param string $id
   *
   * @return \Drupal\taxonomy\TermInterface|bool
   */
  public function getTrelloTopicById($id) {
      return !empty($this->getTrelloTopics()[$id]) ? $this->getTrelloTopics()[$id] : false;
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
   * Get the topic Term entity for a list item.
   *
   * @param \Drupal\nlc_library\Model\Trello\ListModel $model
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  private function getListItemTopic(ListModel $model) {
    // Try to load by Trello ID
    $baseProperties = ['vid' => 'topics'];
    $properties = array_merge($baseProperties, ['field_trello_id' => $model->getId()]);
    $entities = $this->loadEntitiesFromModel($model, 'taxonomy_term', $properties);
    if (empty($entities)) {
      $properties = array_merge($baseProperties, ['name' => $model->getName()]);
      $entities = $this->loadEntitiesFromModel($model, 'taxonomy_term', $properties);
    }
    if (empty($entities)) {
      // There is not current term, so create one.
      $properties = array_merge($baseProperties, ['name' => $model->getName(), 'field_trello_id' => $model->getId()]);
      $term = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->create($properties);
      $term->save();
    }
    else {
      // Make sure the current term is up-to-date with the Trello values — name and ID.
      /** @var \Drupal\taxonomy\Entity\Term $entity */
      $term = current($entities);
      $term->setName($model->getName())
        ->set('field_trello_id', $model->getId())
        ->save();
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
          'format' => 'filtered_text',
        ],
        'field_trello_id' => $model->getId(),
        'field_topic' => $model->getTopicTerm(),
        'field_url' => $model->getFirstAttachment()->getUrl(),
        'created' => $model->getLastActivityDateTime()->format('U'),
        'changed' => $model->getLastActivityDateTime()->format('U'),
      ]);
      $entity = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->create($properties);
      $entity->save();
    }
    else {
      /** @var \Drupal\node\Entity\Node $entity */
      $entity = current($entities);
      $entity->setTitle($model->getName())
        ->set('body', ['value' => $model->getDescription(), 'format' => 'filtered_text'])
        ->set('field_trello_id', $model->getId())
        ->set('field_topic', $model->getTopicTerm())
        ->set('field_url', $model->getFirstAttachment() ? $model->getFirstAttachment()->getUrl() : '');
      if ($updated = $model->getLastActivityDateTime()) {
        $entity->set('changed', $updated->format('U'));
      }
      $entity->save();
    }
    return $entity;
  }

}
