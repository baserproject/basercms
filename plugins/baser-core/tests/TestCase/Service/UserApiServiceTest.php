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

namespace BaserCore\Test\TestCase\Service;

use Authentication\Authenticator\Result;
use BaserCore\Service\UserApiService;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class UserApiServiceTest
 * @package BaserCore\Test\TestCase\Service
 * @property UserApiService $UserApi
 */
class UserApiServiceTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
    ];

    /**
     * @var UserApiService|null
     */
    public $UserApi = null;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->UserApi = new UserApiService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->UserApi);
        parent::tearDown();
    }

    /**
     * Test getLoginToken
     */
    public function testGetLoginToken()
    {
        $result = $this->UserApi->getLoginToken(new Result($this->UserApi->get(1), Result::SUCCESS));
        $this->assertArrayHasKey('token', $result);
        $this->assertEquals(3, count(explode('.', $result['token'])));
        $result = $this->UserApi->getLoginToken(new Result(null, Result::FAILURE_CREDENTIALS_INVALID));
        $this->assertEquals([], $result);
    }

}
