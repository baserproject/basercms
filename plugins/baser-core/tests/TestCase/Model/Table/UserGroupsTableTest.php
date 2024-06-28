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

namespace BaserCore\Test\TestCase\Model\Table;

use BaserCore\Model\Table\UserGroupsTable;
use BaserCore\Test\Factory\UserGroupFactory;
use BaserCore\Test\Scenario\UserGroupsScenario;
use BaserCore\TestSuite\BcTestCase;
use Cake\Validation\Validator;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class UserGroupsTableTest
 * @property UserGroupsTable $UserGroups
 */
class UserGroupsTableTest extends BcTestCase
{

    /**
     * Test subject
     *
     * @var UserGroupsTable
     */
    public $UserGroups;

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->UserGroups = $this->getTableLocator()->get('BaserCore.UserGroups');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->UserGroups);
        parent::tearDown();
    }

    /**
     * Test initialize
     */
    public function testInitialize()
    {
        $this->assertEquals('user_groups', $this->UserGroups->getTable());
        $this->assertEquals('title', $this->UserGroups->getDisplayField());
        $this->assertEquals('id', $this->UserGroups->getPrimaryKey());
        $this->assertIsBool($this->UserGroups->hasBehavior('Timestamp'));
        $this->assertEquals('Users', $this->UserGroups->getAssociation('Users')->getName());
    }

    /**
     * Test validationDefault
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->loadFixtureScenario(UserGroupsScenario::class);
        $validator = $this->UserGroups->validationDefault(new Validator());
        $fields = [];
        foreach($validator->getIterator() as $key => $value) {
            $fields[] = $key;
        }
        $this->assertEquals(['id', 'name', 'title', 'auth_prefix', 'use_move_contents'], $fields);
        $userGroups = $this->UserGroups->get(2);

    }

    /**
     * test validationDefault with title duplicate
     */
    public function test_validationDefaultTitleDuplicate()
    {
        UserGroupFactory::make(['title' => '一般ユーザー'])->persist();
        $validator = $this->UserGroups->getValidator('default');
        $errors = $validator->validate([
            'title' => '一般ユーザー'
        ]);
        $this->assertEquals('既に登録のある表示名です。', current($errors['title']));
    }

    /**
     * Test copy
     *
     * @return void
     */
    public function testCopy()
    {
        $this->loadFixtureScenario(UserGroupsScenario::class);
        $copied = $this->UserGroups->copy(3);
        $originalUserGroup = $this->UserGroups->get(3);
        $query = $this->UserGroups->find()->where(['name' => $originalUserGroup->name . '_copy']);
        $this->assertEquals(1, count($query->toArray()));
        $this->assertEquals(4, $copied->id);
    }

    /**
     * Test getAuthPrefix
     *
     * @return void
     */
    public function testGetAuthPrefix()
    {
        $this->loadFixtureScenario(UserGroupsScenario::class);
        $result = $this->UserGroups->getAuthPrefix(1);
        $this->assertEquals('Admin,Api/Admin', $result);

        $result = $this->UserGroups->getAuthPrefix(999);
        $this->assertEquals(null, $result);
    }

}
