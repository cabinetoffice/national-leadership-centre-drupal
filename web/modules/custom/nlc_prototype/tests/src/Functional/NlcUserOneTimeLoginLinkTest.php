<?php

namespace Drupal\Tests\ncl_prototype\Functional;

use Drupal\Core\Database\Database;
use Drupal\Core\Test\AssertMailTrait;
use Drupal\Core\Url;
use Drupal\Tests\system\Functional\Cache\PageCacheTagsTestBase;
use Drupal\user\Entity\User;

/**
 * Ensure that one-time login link methods work as expected.
 *
 * @group nlc_prototype
 */
class NlcUserOneTimeLoginLinkTest extends PageCacheTagsTestBase {

  use AssertMailTrait {
    getMails as drupalGetMails;
  }

  /**
   * The user object to test password resetting.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $account;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Create a user.
    $account = $this->drupalCreateUser();

    // Activate user by logging in.
    $this->drupalLogin($account);

    $this->account = User::load($account->id());
    $this->account->passRaw = $account->passRaw;
    $this->drupalLogout();

    // Set the last login time that is used to generate the one-time link so
    // that it is definitely over a second ago.
    $account->login = REQUEST_TIME - mt_rand(10, 100000);
    Database::getConnection()->update('users_field_data')
      ->fields(['login' => $account->getLastLoginTime()])
      ->condition('uid', $account->id())
      ->execute();
  }

  /**
   * Tests one-time login link functionality.
   */
  public function testOneTimeLoginLink() {
    // Verify that accessing the site without having the session
    // variables set results in an access denied message.
    $this->drupalGet('');
    $this->assertResponse(403);

    // Verify that accessing the one-time login link form without having the
    // session variables results in an access granted message.
    $this->drupalGet(Url::fromRoute('nlc_prototype.directory.token_access'));
    $this->assertResponse(200);

    // Try to generate a link for an invalid account.
    $this->drupalGet(Url::fromRoute('nlc_prototype.directory.token_access'));

    $edit = ['email' => $this->randomMachineName(16) . '@example.com'];
    $this->drupalPostForm(NULL, $edit, t('Submit'));
    $this->assertText(t('Your email address does not currently have access to the directory. Please check your email address is correct. If it is, please contact NLC@cabinetoffice.gov.uk for more information.'), 'Validation error message shown when trying to request password for invalid account.');
    $this->assertEqual(count($this->drupalGetMails(['id' => 'nlc_onetime_login_link'])), 0, 'No email was sent when requesting a one-time login link for an invalid account.');
  }

}
