<?php

namespace Drupal\nlc_library\Form;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TrelloImportAdminForm extends FormBase {

  private $trelloBoardId = 'X1OQEy5Q';

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
    foreach ($data->lists as $list) {
      dpm(sprintf('list: %s | id: %s', $list->name, $list->id));
    }
  }

}
