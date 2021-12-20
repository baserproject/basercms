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

namespace BaserCore\Test\TestCase\Model\Validation;

use BaserCore\Model\Validation\UserValidation;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class UserValidationTest
 * @package BaserCore\Test\TestCase\Model\Validation
 * @property UserValidation $UserValidation
 */
class UserValidationTest extends BcTestCase
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
        'plugin.BaserCore.LoginStores',
    ];

    /**
     * Test subject
     *
     * @var UserValidation
     */
    public $UserValidation;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->UserValidation = new UserValidation();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->UserValidation);
        parent::tearDown();
    }

    /**
     * willChangeSelfGroup
     *
     * @param int $userId ユーザーID
     * @param string $alias バリデーション対象値
     * @param array $expected 期待値
     * @dataProvider willChangeSelfGroupDataProvider
     */
    public function testWillChangeSelfGroup($userId, $value, $expected)
    {
        if($userId) {
            $this->loginAdmin($this->getRequest(), $userId);
        }
        $result = $this->UserValidation->willChangeSelfGroup($value, ['data' => ['id' => '2', 'login_user_id' => $userId]]);
        $this->assertEquals($expected, $result);
    }
    public function willChangeSelfGroupDataProvider()
    {
        return [
            [1, ['_ids' => [2]], true],
            [2, ['_ids' => [2]], true],
            [2, ['_ids' => [1]], false],
            [2, ['_ids' => [1, 2]], false],
            [null, ['_ids' => [2]], false],
        ];
    }
}
