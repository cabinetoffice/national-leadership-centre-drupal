<?php

namespace Drupal\nlc_prototype\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

class DirectoryWelcomeController extends ControllerBase {

  /**
   * Controller render array, with link back to access request form.
   *
   * @return array
   */
  public function build() {

    $build = [];

    $url = Url::fromRoute('nlc_prototype.directory.token_access');
    $link = Link::fromTextAndUrl($this->t('Start now'), $url);
    $link = $link->toRenderable();
    $link['#attributes'] = [
      'class' => ['button'],
    ];

    $networkUrl = Url::fromUri('https://www.nationalleadership.gov.uk/the-network/');
    $networkLink = Link::fromTextAndUrl($this->t('Find out more about our Network'), $networkUrl);
    $networkLink = $networkLink->toRenderable();
    $networkLink['#attributes'] = [
      'target' => '_blank',
    ];

    $build['intro'] = [
      '#type' => 'inline_template',
      '#context' => [
        'heading_one' => $this->t('You will be able to'),
        'item_one' => $this->t('Find other Network members’ contact details'),
        'item_two' => $this->t('Find out who else is attending your programme'),
        'text_one' => $this->t('You will only be able to access this service if you are a National Leadership Centre network member.'),
        'network_link' => $networkLink,
        'button' => $link,
        'heading_two' => $this->t('Before you start'),
        'text_two' => $this->t('You will need your work email address and access to your work email account to gain access to the directory.'),
      ],
      '#template' => '<h2>{{heading_one}}</h2>
     <ul>
      <li>{{item_one}}</li>
      <li>{{item_two}}</li>
     </ul>
     <p>{{text_one}}</p>
     <p>{{ network_link }}</p>
     <h2>{{heading_two}}</h2>
     <p>{{text_two}}</p>
     <p>{{ button }}</p>',
    ];

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
