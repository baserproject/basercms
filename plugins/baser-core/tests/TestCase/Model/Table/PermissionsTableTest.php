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

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Model\Table\PermissionsTable;

/**
 * BaserCore\Model\Table\PermissionsTable Test Case
 *
 * @property PermissionsTable $Permissions
 */
class PermissionsTableTest extends BcTestCase
{

    /**
     * Test subject
     *
     * @var PermissionsTable
     */
    public $Permissions;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Permissions',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
    ];

        /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Permissions = $this->getTableLocator()->get('BaserCore.Permissions');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test initialize
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertEquals('permissions', $this->Permissions->getTable());
        $this->assertEquals('id', $this->Permissions->getPrimaryKey());
        $this->assertTrue($this->Permissions->hasBehavior('Timestamp'));
        $this->assertEquals('UserGroups', $this->Permissions->getAssociation('UserGroups')->getName());

    }

    /**
     * Test validationDefault
     *
     * @return void
     * @dataProvider validationDefaultDataProvider
     */
    public function testValidationDefault($fields, $messages)
    {
        $permission = $this->Permissions->newEntity($fields);
        $this->assertSame($messages, $permission->getErrors());
    }
    public function validationDefaultDataProvider()
    {
        $maxName = str_repeat("a", 255);
        $maxUrl = '/' . str_repeat("a", 254);

        return [
            // 正常
            [
                [
                    'name' => 'test',
                    'user_group_id' => '1',
                    'url' => '/test'
                ], []
            ],
            // 空の場合
            [
                [
                    'name' => '',
                    'user_group_id' => '',
                    'url' => ''
                ], [
                    'name' => ['_empty' => '設定名を入力してください。'],
                    'user_group_id' => ['_empty' => 'ユーザーグループを選択してください。'],
                    'url' => ['_empty' => '設定URLを入力してください。'],
                ]
            ],
            // 文字数が超過する場合&&user_group_idフィールドが存在しない場合&&checkUrl失敗
            [
                [
                    'name' => $maxName . 'a',
                    'url' => $maxUrl . 'a'
                ], [
                    'name' => ['maxLength' => '設定名は255文字以内で入力してください。'],
                    'user_group_id' => ['_required' => 'This field is required'],
                    'url' => [
                        'maxLength' => '設定URLは255文字以内で入力してください。'
                    ],
                ]
            ],
            // URL形式正常
            [
                [
                    'name' => 'test',
                    'user_group_id' => '1',
                    'url' => '/baser/admin/baser-core/users/edit/{loginUserId}'
                ], []
            ], [
                [
                    'name' => 'test',
                    'user_group_id' => '1',
                    'url' => '/baser/api/admin/baser-core/contents/view/*.json'
                ], []
            ],
            // URL形式異常
            [
                [
                    'name' => 'test',
                    'user_group_id' => '1',
                    'url' => 'test'
                ], [
                    'url' => [
                        'regex' => '設定URLはスラッシュから始まるURLを入力してください。'
                    ],
                ]
            ], [
                [
                    'name' => 'test',
                    'user_group_id' => '1',
                    'url' => '/test?test=1'
                ], [
                    'url' => [
                        'nameAlphaNumericPlus' => '設定URLに使用できない文字列が含まれています。',
                    ],
                ]
            ],
        ];
    }

    /**
     * testValidationPlain
     *
     * @return void
     */
    public function testValidationPlain()
    {
        $permission = $this->Permissions->newEntity(['user_group_id' => 2], ['validate' => 'plain']);
        $this->assertFalse($permission->hasErrors());
    }
    /**
     * validate
     */
    public function test必須チェック()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->Permissions->create([
            'Permission' => [
                'name' => '',
                'url' => '',
            ]
        ]);
        $this->assertFalse($this->Permissions->validates());
        $this->assertArrayHasKey('name', $this->Permissions->validationErrors);
        $this->assertEquals('設定名を入力してください。', current($this->Permissions->validationErrors['name']));
        $this->assertArrayHasKey('user_group_id', $this->Permissions->validationErrors);
        $this->assertEquals('ユーザーグループを選択してください。', current($this->Permissions->validationErrors['user_group_id']));
        $this->assertArrayHasKey('url', $this->Permissions->validationErrors);
        $this->assertEquals('設定URLを入力してください。', current($this->Permissions->validationErrors['url']));
    }

    public function test桁数チェック正常系()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->Permissions->create([
            'Permission' => [
                'name' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
                'user_group_id' => '1',
                'url' => '/admin/12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
            ]
        ]);
        $this->assertTrue($this->Permissions->validates());
    }

    public function test桁数チェック異常系()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->Permissions->create([
            'Permission' => [
                'name' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
                'user_group_id' => '1',
                'url' => '/admin/1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
            ]
        ]);
        $this->assertFalse($this->Permissions->validates());
        $this->assertArrayHasKey('name', $this->Permissions->validationErrors);
        $this->assertEquals('設定名は255文字以内で入力してください。', current($this->Permissions->validationErrors['name']));
        $this->assertArrayHasKey('url', $this->Permissions->validationErrors);
        $this->assertEquals('設定URLは255文字以内で入力してください。', current($this->Permissions->validationErrors['url']));
    }

    /**
     * beforeSave
     *
     * @param array $url saveするurl
     * @param array $expectedUrl 期待するurl
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider beforeSaveDataProvider
     */
    public function testBeforeSave($url, $expected, $message = null)
    {
        $data = [
            'no' => 100,
            'sort' => 100,
            'name' => 'パーミッションテスト',
            'user_group_id' => '100',
            'auth' => true,
            'method' => 'ALL',
            'status' => true,
        ];
        $permission = $this->Permissions->newEntity($data);
        $permission->url = $url;
        $result = $this->Permissions->save($permission);
        $this->assertEquals($expected, $result->url, $message);
    }

    public function beforeSaveDataProvider()
    {
        return [
            ['hoge', '/hoge', 'urlが絶対パスになっていません'],
            ['/hoge', '/hoge', 'urlが絶対パスになっていません'],
        ];
    }

    /**
     * アクセスルールをコピーする
     *
     * @param int $id
     * @param array $data
     * @param array $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider copyDataProvider
     */
    public function testCopy($id, $data, $expected, $message = null)
    {
        $record = $this->Permissions->copy($id, $data);
        $result = $expected ? $record->name : $record;
        $this->assertEquals($expected, $result, $message);
    }

    public function copyDataProvider()
    {
        return [
            // id指定の場合
            [1, [], 'システム管理', 'id指定でデータをコピーできません'],
            // フィールド指定の場合
            [
                null,
                [
                    'user_group_id' => '3',
                    'name' => 'hoge',
                    'url' => '/baser/admin/*',
                    'auth' => '1',
                    'status' => '1'
                ],
                'hoge',
                'data指定でデータをコピーできません'
            ],
            // 最低限必要なフィールドがない場合
            [
                null,
                ['user_group_id' => '', 'name' => ''],
                false,
                'コピーできないデータです'
            ],
        ];
    }

    /**
     * GetTargetPermissions
     * SetTargetPermissions
     *
     * @return void
     */
    public function testGetTargetPermissionsAndSetTargetPermissions(): void
    {
        $this->Permissions->setTargetPermissions([2, 3]);
        $data = $this->Permissions->getTargetPermissions([2, 3]);
        $this->assertNotEmpty($data[2]);
        $this->assertNotEmpty($data[2][0]);
    }
}
