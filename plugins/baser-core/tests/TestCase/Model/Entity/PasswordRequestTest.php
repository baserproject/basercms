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

use BaserCore\Model\Entity\PasswordRequest;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class PasswordRequest
 * @package BaserCore\Test\TestCase\Model\Entity
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
    public function testRequestKey() {
        $this->PasswordRequest->setRequestKey();
        $requestKey1 = $this->PasswordRequest->request_key;
        $this->PasswordRequest->setRequestKey();
        $requestKey2 = $this->PasswordRequest->request_key;

        $this->assertNotEquals($requestKey1, $requestKey2);
        $this->assertIsString($requestKey1);
        $this->assertGreaterThan(40, strlen($requestKey1));
    }

}
