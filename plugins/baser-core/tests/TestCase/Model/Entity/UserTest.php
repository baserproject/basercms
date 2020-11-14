<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Model\Entity;
use BaserCore\Model\Entity\User;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class UserTest
 * @package BaserCore\Test\TestCase\Model\Entity
 */
class UserTest extends BcTestCase
{

    /**
     * @var User
     */
    public $User;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->User = new User();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->User);
        parent::tearDown();
    }

    /**
     * Test _setPassword
     */
    public function testSetPassword() {
        $this->User->set('password', 'testtest');
        $this->assertNotEquals('testtest', $this->User->password);
    }

}
