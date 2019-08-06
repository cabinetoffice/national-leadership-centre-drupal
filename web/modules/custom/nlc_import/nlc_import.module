<?php

use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Implements hook_help().
 */
function nlc_import_help($route_name, RouteMatchInterface $route_match) {
  switch ($route_name) {
    // Main module help for the custom_migrate module.
    case 'help.page.nlc_import':
      $output = '';
      $output .= '<h3>' . t('About') . '</h3>';
      $output .= '<p>' . t('NLC Delegate Migrate') . '</p>';
      return $output;

    default:
  }
}

/**
 * Implements hook_migration_plugins_alter().
 */
function nlc_import_migration_plugins_alter(&$definitions) {
  $definitions['migrate_csv']['source']['path'] = drupal_get_path('module', 'nlc_import') . $definitions['migrate_csv']['source']['path'];
}