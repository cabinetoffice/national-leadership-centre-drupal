<?php

namespace Drupal\nlc_prototype\Controller;

use Drupal\Component\Utility\Crypt;
use Drupal\Component\Utility\UserAgent;
use Drupal\Console\Bootstrap\Drupal;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Config\Config;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\TempStore\PrivateTempStore;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Drupal\Core\Url;
use Drupal\user\UserDataInterface;
use Drupal\user\UserInterface;
use Drupal\user\UserStorageInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class DirectorywelcomeController extends ControllerBase {

  /**
   * Controller render array, with link back to access request form.
   *
   * @return array
   */
  public function build() {

    $build = [];
    $build['intro'] = [
      '#type' => 'inline_template',
      '#context' => [
        'heading_one' => 'You will be able to',
        'item_one' => 'Find other Network members’ contact details',
        'item_two' => 'Find out who else is attending your programme',
        'text_one' => 'You will only be able to access this service if you are an NLC member, or have taken part in any NLC Programmes.',
        'link_text' => 'Start now',
        'url' => '/directory/access',
        'heading_two' => 'Before you start',
        'text_two' => 'You will need to use the same email address that is registered with the NLC.',
      ],
      '#template' => '<h2>{% trans %} {{heading_one}} {% endtrans %}</h2><ul><li>{% trans %} {{item_one}} {% endtrans %}</li>
      <li>{% trans %} {{item_two}} {% endtrans %}</li></ul><p>{% trans %} {{text_one}} {% endtrans %}</p>
      <p><a class="button" href="{{url}}">{% trans %} {{link_text}} {% endtrans %}</a></p><h2>{% trans %} {{heading_two}} {% endtrans %}</h2>
      <p>{% trans %} {{text_two}} {% endtrans %}</p>',
    ];

    // $this->deleteStore();
    return $build;
  }

  /**
   * Access check for form.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   */
  public function access() {
    $account = $this->currentUser();
    if ($account->isAuthenticated()) {
      return AccessResult::forbidden();
    }
    return AccessResult::allowed();
  }

}
