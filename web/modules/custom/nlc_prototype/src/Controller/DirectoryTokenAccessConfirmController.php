<?php

namespace Drupal\nlc_prototype\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\TempStore\PrivateTempStore;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DirectoryTokenAccessConfirmController extends ControllerBase {

  /**
   * @var PrivateTempStoreFactory
   */
  private $privateTempStoreFactory;

  /**
   * @var PrivateTempStore
   */
  protected $store;

  public function __construct(PrivateTempStoreFactory $privateTempStoreFactory) {
    $this->privateTempStoreFactory = $privateTempStoreFactory;
    $this->store = $this->privateTempStoreFactory->get('directory_token_access_data');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('tempstore.private')
    );
  }

  /**
   * Controller render array, with link back to access request form.
   *
   * @return array
   */
  public function build() {

    $build = [];
    $format = filter_default_format();
    $build['intro'] = [
      '#type' => 'processed_text',
      '#text' => $this->t('You will receive a secure link to your email address.'),
      '#format' => $format,
    ];

    $url = Url::fromRoute('nlc_prototype.directory.token_access');
    $build['form_link'] = [
      '#type' => 'link',
      '#title' => $this->t('Go to form'),
      '#url' => $url,
    ];
    $this->deleteStore();
    return $build;
  }

  /**
   * Access check for the access form confirm page.
   *
   * @return \Drupal\Core\Access\AccessResult
   */
  public function access() {
    return AccessResult::allowedIf($this->store->get('email'));
  }


  /**
   * Helper method that removes all the keys from the store collection.
   */
  protected function deleteStore() {
    $keys = ['email'];
    foreach ($keys as $key) {
      $this->store->delete($key);
    }
  }
}
