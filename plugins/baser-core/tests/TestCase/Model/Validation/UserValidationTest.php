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

namespace BaserCore\Test\TestCase\Model\Validation;

use BaserCore\Model\Validation\UserValidation;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\UserGroupsScenario;
use BaserCore\Test\Scenario\UserScenario;
use BaserCore\Test\Scenario\UsersUserGroupsScenario;
use BaserCore\TestSuite\BcTestCase;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class UserValidationTest
 * @property UserValidation $UserValidation
 */
class UserValidationTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

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
        $this->loadFixtureScenario(UserScenario::class);
        $this->loadFixtureScenario(UserGroupsScenario::class);
        $this->loadFixtureScenario(UsersUserGroupsScenario::class);
        if($userId) {
            $this->loginAdmin($this->getRequest(), $userId);
        }
        $result = $this->UserValidation->willChangeSelfGroup($value, ['data' => ['id' => '2', 'login_user_id' => $userId]]);
        $this->assertEquals($expected, $result);
    }
    public static function willChangeSelfGroupDataProvider()
    {
        return [
            [1, ['_ids' => [2]], true],
            [2, ['_ids' => [2]], true],
            [2, ['_ids' => [1]], false],
            [2, ['_ids' => [1, 2]], false],
            [null, ['_ids' => [2]], false],
            // 行形式（user_groups[0][id]）で送信されても _ids と同じ判定になること
            [2, [['id' => 2]], true],
            [2, [['id' => 1]], false],
            [2, [['id' => 1], ['id' => 2]], false],
            [2, ['1'], false],
        ];
    }

    /**
     * willChangeSelfGroup - id が送信されない場合も自身の変更として判定すること
     *
     * 以前は id 未送信を「他人の編集」とみなして true を返していたため、
     * id を送らずに自身のグループを変更できた。
     */
    public function testWillChangeSelfGroup_withoutId()
    {
        $this->loadFixtureScenario(UserScenario::class);
        $this->loadFixtureScenario(UserGroupsScenario::class);
        $this->loadFixtureScenario(UsersUserGroupsScenario::class);
        $this->loginAdmin($this->getRequest(), 2);
        $this->assertFalse($this->UserValidation->willChangeSelfGroup(['_ids' => [1]], ['data' => ['login_user_id' => 2]]));
        $this->assertTrue($this->UserValidation->willChangeSelfGroup(['_ids' => [2]], ['data' => ['login_user_id' => 2]]));
    }
}
