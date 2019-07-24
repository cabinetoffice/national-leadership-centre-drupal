<?php

namespace Drupal\nlc_prototype\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    // Note that the second parameter of setRequirement() is a string.
    if ($route = $collection->get('view.directory.page_1')) {
      $route->setRequirement('_custom_access', 'Drupal\nlc_prototype\Access\DirectoryPrototypeAccess::access');
    }
  }
}
