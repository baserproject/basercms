<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Model\Entity;

use BaserCore\Model\Entity\PasswordRequest;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class PasswordRequest
 */
class PasswordRequestTest extends BcTestCase
{

    /**
     * @var PasswordRequest
     */
    public $PasswordRequest;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->PasswordRequest = new PasswordRequest();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->PasswordRequest);
        parent::tearDown();
    }

    /**
     * Test setRequestKey
     */
    public function testSetRequestKey()
    {
        $this->PasswordRequest->setRequestKey();
        $requestKey1 = $this->PasswordRequest->request_key;
        $this->PasswordRequest->setRequestKey();
        $requestKey2 = $this->PasswordRequest->request_key;

        $this->assertNotEquals($requestKey1, $requestKey2);
        $this->assertIsString($requestKey1);
        $this->assertGreaterThan(40, strlen($requestKey1));
    }

    /**
     * Test makeRequestKey
     */
    public function testMakeRequestKey()
    {
        $requestKey= $this->execPrivateMethod($this->PasswordRequest, 'makeRequestKey');
        $this->assertEquals('48', strlen($requestKey));
    }

}
