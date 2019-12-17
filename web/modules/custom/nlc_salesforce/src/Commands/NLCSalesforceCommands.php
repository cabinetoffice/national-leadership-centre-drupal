<?php

namespace Drupal\nlc_salesforce\Commands;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Drupal\salesforce\Commands\SalesforceCommandsBase;
use OAuth\OAuth2\Token\StdOAuth2Token;

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
class NLCSalesforceCommands extends SalesforceCommandsBase {

  /**
   * Return list of authentication tokens.
   *
   * @command salesforce:list-providers
   * @aliases nsflp
   * @field-labels
   *   default: Default
   *   label: Label
   *   name: Name
   *   status: Token Status
   * @default-fields label,name,default,status
   *
   * @return \Consolidation\OutputFormatters\StructuredData\RowsOfFields
   *   The auth provider details.
   */
  public function listAuthProviders() {
    $rows = [];
    foreach($this->authMan->getProviders() as $provider) {

      $rows[] = [
        'default' => $this->authMan->getConfig()->id() == $provider->id() ? '✓' : '',
        'label' => $provider->label(),
        'name' => $provider->id(),
        'status' => $provider->getPlugin()->hasAccessToken() ? 'Authorized' : 'Missing',
      ];
    }

    return new RowsOfFields($rows);
  }

  /**
   * Refresh the named authentication token.
   *
   * @param string $providerName
   *   The name of the authentication provider.
   *
   * @command salesforce:refresh-token
   * @aliases nsfrt
   *
   * @return \Consolidation\OutputFormatters\StructuredData\RowsOfFields|null
   *   Describe result.
   *
   * @throws \OAuth\OAuth2\Service\Exception\MissingRefreshTokenException
   */
  public function refreshToken($providerName = '') {
    // If no provider name given, use the default.
    if (empty($providerName)) {
      $providerName = $this->authMan->getConfig()->id();
    }

    foreach($this->authMan->getProviders() as $provider) {
      if ($providerName == $provider->id()) {
        $auth = $provider->getPlugin();
        $token = $auth->hasAccessToken() ? $auth->getAccessToken() : new StdOAuth2Token();
        $auth->refreshAccessToken($token);
        return "Access token refreshed for $providerName";
      }
    }
  }
}
