<?php


namespace Drupal\Tests\nlc_prototype\Unit;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Tests\UnitTestCase;
use Drupal\user\UserAuth;

/**
 * Tests authentication with NLC one-time login links.
 *
 * @group nlc_prototype
 */
class NlcUserAuthTest extends UnitTestCase {

  /**
   * The mock user storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $userStorage;

  /**
   * The mocked password service.
   *
   * @var \Drupal\Core\Password\PasswordInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $passwordService;

  /**
   * The mock user.
   *
   * @var \Drupal\user\Entity\User|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $testUser;

  /**
   * The user auth object under test.
   *
   * @var \Drupal\user\UserAuth
   */
  protected $userAuth;

  /**
   * The test username.
   *
   * @var string
   */
  protected $username = 'test_user';

  /**
   * The test password.
   *
   * @var string
   */
  protected $password = 'password';

  /**
   * The test user email address.
   *
   * @var string
   */
  protected $email = 'test_user@example.com';

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    $this->userStorage = $this->getMock('Drupal\Core\Entity\EntityStorageInterface');

    /** @var \Drupal\Core\Entity\EntityTypeManagerInterface|\PHPUnit_Framework_MockObject_MockObject $entity_type_manager */
    $entity_type_manager = $this->createMock(EntityTypeManagerInterface::class);
    $entity_type_manager->expects($this->any())
      ->method('getStorage')
      ->with('user')
      ->will($this->returnValue($this->userStorage));

    $this->passwordService = $this->getMock('Drupal\Core\Password\PasswordInterface');

    $this->testUser = $this->getMockBuilder('Drupal\user\Entity\User')
      ->disableOriginalConstructor()
      ->setMethods(['id', 'setPassword', 'save', 'getPassword', 'setEmail', 'getEmail'])
      ->getMock();

    $this->userAuth = new UserAuth($entity_type_manager, $this->passwordService);
  }

}
