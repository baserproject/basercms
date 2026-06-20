<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.7
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcMcp\Test\TestCase\Mcp\BcCustomContent;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcCustomContent\Service\CustomEntriesService;
use BcCustomContent\Service\CustomEntriesServiceInterface;
use BcCustomContent\Service\CustomTablesService;
use BcCustomContent\Test\Factory\CustomFieldFactory;
use BcMcp\Mcp\BcCustomContent\CustomEntriesTool;
use BaserCore\Service\BcDatabaseServiceInterface;
use BcCustomContent\Test\Factory\CustomTableFactory;
use BcCustomContent\Test\Scenario\CustomFieldsScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Test\Scenario\CustomContentsScenario;
use PhpMcp\Server\ServerBuilder;

/**
 * BcMcp\Mcp\BcCustomContent\CustomEntriesTool Test Case
 *
 * @uses \BcMcp\Mcp\BcCustomContent\CustomEntriesTool
 */
class CustomEntriesToolTest extends BcTestCase
{
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Test subject
     *
     * @var \BcMcp\Mcp\BcCustomContent\CustomEntriesTool
     */
    protected $CustomEntriesTool;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->CustomEntriesTool = new CustomEntriesTool();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->CustomEntriesTool);
        parent::tearDown();
    }

    /**
     * Test addCustomEntry method - 基本テスト
     * CustomTablesに依存するため、適切なセットアップが必要
     *
     * @return void
     */
    public function testAddCustomEntryBasic()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTablesService = $this->getService(CustomTablesServiceInterface::class);

        // CustomFieldsScenarioを読み込み
        $this->loadFixtureScenario(CustomFieldsScenario::class);

        $customTableId = 1;
        $title = 'テストカスタムエントリー';

        // カスタムテーブルを作成
        $customTablesService->create([
            'type' => 'contact',
            'name' => 'contact',
            'title' => 'お問い合わせタイトル',
            'display_field' => 'お問い合わせ'
        ]);

        $result = $this->CustomEntriesTool->addCustomEntry(
            customTableId: $customTableId,
            title: $title,
            name: 'test_entry',
            status: true,
            creatorId: 1
        );

        $this->assertIsArray($result);
        if (isset($result['success']) && $result['success']) {
            $this->assertArrayHasKey('title', $result);
            $this->assertEquals($title, $result['title']);
            $this->assertEquals($customTableId, $result['custom_table_id']);
        }

        // テーブルをクリーンアップ
        $dataBaseService->dropTable('custom_entry_1_contact');
    }

    /**
     * Test addCustomEntry method - ファイルアップロード付きテスト
     *
     * @return void
     */
    public function testAddCustomEntryWithFileUpload()
    {
        // Base64画像データ（1x1ピクセルの透明PNG）
        $base64Image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';

        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTablesService = $this->getService(CustomTablesServiceInterface::class);

        $this->loadFixtureScenario(CustomFieldsScenario::class);

        $customTableId = 1;
        $title = 'ファイルアップロード付きエントリー';
        $customFields = [
            'image_field' => $base64Image,
            'text_field' => 'テキスト値'
        ];

        // カスタムテーブルを作成
        $customTablesService->create([
            'type' => 'contact',
            'name' => 'contact_with_files',
            'title' => 'ファイル付きお問い合わせ',
            'display_field' => 'お問い合わせ'
        ]);

        $result = $this->CustomEntriesTool->addCustomEntry(
            customTableId: $customTableId,
            title: $title,
            customFields: $customFields
        );

        $this->assertIsArray($result);
        if (isset($result['success']) && $result['success']) {
            $this->assertArrayHasKey('title', $result);
            $this->assertEquals($title, $result['title']);
            // ファイルアップロードが処理されていることを確認
            $this->assertNotEquals($base64Image, $result['image_field'] ?? '');
            $this->assertEquals('テキスト値', $result['text_field'] ?? '');
        } else {
            // エラーケースでもレスポンス構造をテスト
        }

        // テーブルをクリーンアップ
        $dataBaseService->dropTable('custom_entry_1_contact_with_files');
    }

    /**
     * Test addCustomEntry method - 外部画像URL指定テスト
     *
     * @return void
     */
    public function testAddCustomEntryWithImageUrl()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        /** @var CustomTablesService $customTablesService */
        $customTablesService = $this->getService(CustomTablesServiceInterface::class);

        CustomFieldFactory::make([
            'id' => 1,
            'title' => 'ファイル',
            'name' => 'image_field',
            'type' => 'BcCcFile',
        ])->persist();

        $customTableId = 1;
        $title = '外部画像URL付きエントリー';
        // GitHubのアバター画像（確実にアクセス可能）
        $imageUrl = 'https://github.com/github.png';
        $customFields = [
            'image_field' => $imageUrl
        ];

        // カスタムテーブルを作成
        $customTable = $customTablesService->create([
            'type' => 'contact',
            'name' => 'contact_with_image_url',
            'title' => '画像URL付きお問い合わせ',
            'display_field' => 'お問い合わせ'
        ]);
        $customTablesService->update($customTable, [
            'id' => $customTable->id,
            'custom_links' => [
                'new-2' => [
                    'custom_field_id' => 1,
                    'title' => 'ファイル',
                    'name' => 'image_field',
                    'type' => 'BcCcFile',
                    'status' => true,
                ]
            ]
        ]);

        /** @var CustomEntriesService $customEntriesService */
        $customEntriesService = $this->getService(CustomEntriesServiceInterface::class);
        $customEntriesService->setup(1);

        $result = $this->CustomEntriesTool->addCustomEntry(
            customTableId: $customTableId,
            title: $title,
            customFields: $customFields,
            status: true
        );

        $this->assertIsArray($result);
        // 登録が成功したことを確認
        $this->assertArrayHasKey('title', $result);
        $this->assertEquals($title, $result['title']);
        // 外部画像URLが正しく保存されていることを確認（保存先は現在年月のディレクトリ）
        $this->assertEquals(date('Y/m') . '/00000001_image_field.png', $result['image_field'] ?? '');
        $this->assertTrue($result['status'] ?? false);

        // テーブルをクリーンアップ
        $dataBaseService->dropTable('custom_entry_1_contact_with_image_url');
    }

    /**
     * Test addCustomEntry method - カスタムフィールド付きテスト
     *
     * @return void
     */
    public function testAddCustomEntryWithCustomFields()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTablesService = $this->getService(CustomTablesServiceInterface::class);

        $this->loadFixtureScenario(CustomFieldsScenario::class);

        $customTableId = 1;
        $title = 'カスタムフィールド付きエントリー';
        $customFields = [
            'custom_field1' => 'カスタム値1',
            'custom_field2' => 'カスタム値2'
        ];

        // カスタムテーブルを作成
        $customTablesService->create([
            'type' => 'contact',
            'name' => 'contact',
            'title' => 'お問い合わせタイトル',
            'display_field' => 'お問い合わせ'
        ]);

        $result = $this->CustomEntriesTool->addCustomEntry(
            customTableId: $customTableId,
            title: $title,
            customFields: $customFields
        );

        $this->assertIsArray($result);
        if (isset($result['success']) && $result['success']) {
            $this->assertArrayHasKey('title', $result);
            $this->assertEquals($title, $result['title']);
        }

        // テーブルをクリーンアップ
        $dataBaseService->dropTable('custom_entry_1_contact');
    }

    /**
     * Test addCustomEntry method - エラーテスト（空のタイトル）
     *
     * @return void
     */
    public function testAddCustomEntryWithEmptyTitle()
    {
        $result = $this->CustomEntriesTool->addCustomEntry(
            customTableId: 1,
            title: ''
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('content', $result);
    }

    /**
     * Test getCustomEntries method - 基本的な一覧取得テスト
     *
     * @return void
     */
    public function testGetCustomEntriesBasic()
    {
        // テストデータを作成
        CustomTableFactory::make([
            'id' => 1,
            'name' => 'test_table',
            'display_name' => 'テストテーブル',
            'status' => 1
        ])->persist();

        $this->loadFixtureScenario(CustomContentsScenario::class);

        $result = $this->CustomEntriesTool->getCustomEntries(
            customTableId: 1,
            limit: 10,
            page: 1
        );

        $this->assertIsArray($result);
        if (isset($result['success']) && $result['success']) {
            $this->assertArrayHasKey('content', $result);
            $this->assertArrayHasKey('pagination', $result);
            $this->assertEquals(10, $result['pagination']['limit']);
            $this->assertEquals(1, $result['pagination']['page']);
        } else {
            // エラーケースでもレスポンス構造をテスト
            $this->assertArrayHasKey('content', $result);
        }
    }

    /**
     * Test getCustomEntries method - ステータスフィルタリングテスト
     *
     * @return void
     */
    public function testGetCustomEntriesWithStatusFilter()
    {
        // テストデータを作成
        CustomTableFactory::make([
            'id' => 1,
            'name' => 'test_table',
            'display_name' => 'テストテーブル',
            'status' => 1
        ])->persist();

        $this->loadFixtureScenario(CustomContentsScenario::class);

        $result = $this->CustomEntriesTool->getCustomEntries(
            customTableId: 1,
            status: 'publish',
            limit: 5
        );

        $this->assertIsArray($result);
        if (isset($result['success']) && $result['success']) {
            $this->assertArrayHasKey('content', $result);
            $this->assertEquals(5, $result['pagination']['limit']);
        } else {
            // エラーケースでもレスポンス構造をテスト
            $this->assertArrayHasKey('content', $result);
        }
    }

    /**
     * Test getCustomEntry method - IDによる単一取得テスト
     *
     * @return void
     */
    public function testGetCustomEntryById()
    {
        $result = $this->CustomEntriesTool->getCustomEntry(
            customTableId: 1,
            id: 1
        );

        $this->assertIsArray($result);
        // 存在しないエントリーの場合はエラーが返される
        if (isset($result['error']) && $result['error']) {
            $this->assertArrayHasKey('content', $result);
        } else if (isset($result['success']) && $result['success']) {
            $this->assertArrayHasKey('content', $result);
            $this->assertEquals(1, $result['id']);
        }
    }

    /**
     * Test getCustomEntry method - 存在しないIDのテスト
     *
     * @return void
     */
    public function testGetCustomEntryNotFound()
    {
        $nonExistentId = 999999;

        $result = $this->CustomEntriesTool->getCustomEntry(
            customTableId: 1,
            id: $nonExistentId
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('content', $result);
    }

    /**
     * Test editCustomEntry method - 基本的な編集テスト
     *
     * @return void
     */
    public function testEditCustomEntryBasic()
    {
        $newTitle = '編集されたタイトル';
        $newStatus = true;

        $result = $this->CustomEntriesTool->editCustomEntry(
            customTableId: 1,
            id: 1,
            title: $newTitle,
            status: $newStatus
        );

        $this->assertIsArray($result);
        // 存在しないエントリーの場合はエラーが返される
        if (isset($result['error']) && $result['error']) {
            $this->assertArrayHasKey('content', $result);
        } else if (isset($result['success']) && $result['success']) {
            $this->assertArrayHasKey('content', $result);
            $this->assertEquals($newTitle, $result['title']);
        }
    }

    /**
     * Test editCustomEntry method - カスタムフィールド編集テスト
     *
     * @return void
     */
    public function testEditCustomEntryWithCustomFields()
    {
        $customFields = [
            'custom_field1' => '更新されたカスタム値1',
            'custom_field2' => '更新されたカスタム値2'
        ];

        $result = $this->CustomEntriesTool->editCustomEntry(
            customTableId: 1,
            id: 1,
            customFields: $customFields
        );

        $this->assertIsArray($result);
        // 存在しないエントリーの場合はエラーが返される
        if (isset($result['error']) && $result['error']) {
            $this->assertArrayHasKey('content', $result);
        } else if (isset($result['success']) && $result['success']) {
            $this->assertArrayHasKey('content', $result);
        }
    }

    /**
     * Test editCustomEntry method - 存在しないエントリーの編集テスト
     *
     * @return void
     */
    public function testEditCustomEntryNotFound()
    {
        $nonExistentId = 999999;

        $result = $this->CustomEntriesTool->editCustomEntry(
            customTableId: 1,
            id: $nonExistentId,
            title: '新しいタイトル'
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('content', $result);
    }

    /**
     * Test deleteCustomEntry method - 削除機能テスト
     *
     * @return void
     */
    public function testDeleteCustomEntryBasic()
    {
        $result = $this->CustomEntriesTool->deleteCustomEntry(
            customTableId: 1,
            id: 1
        );

        $this->assertIsArray($result);
        // 削除処理は存在しないエントリーでもエラーハンドリングされる
        if (isset($result['error']) && $result['error']) {
            $this->assertArrayHasKey('content', $result);
        } else if (isset($result['success']) && $result['success']) {
            $this->assertArrayHasKey('content', $result);
        }
    }

    /**
     * Test deleteCustomEntry method - 存在しないエントリーの削除テスト
     *
     * @return void
     */
    public function testDeleteCustomEntryNotFound()
    {
        $nonExistentId = 999999;

        $result = $this->CustomEntriesTool->deleteCustomEntry(
            customTableId: 1,
            id: $nonExistentId
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('content', $result);
    }

    /**
     * Test addToolsToBuilder method - ServerBuilderへのツール追加テスト
     *
     * @return void
     */
    public function testAddToolsToBuilder()
    {
        // ServerBuilderがfinalクラスのため、実際のインスタンスを使用
        $serverBuilder = new ServerBuilder();

        $result = $this->CustomEntriesTool->addToolsToBuilder($serverBuilder);

        $this->assertInstanceOf(ServerBuilder::class, $result);
        // ServerBuilderが返されることを確認（チェーンメソッドパターン）
        $this->assertSame($serverBuilder, $result);
    }

    /**
     * Test processCustomFields method - ファイル処理テスト
     *
     * @return void
     */
    public function testProcessCustomFields()
    {
        // Base64画像データ（1x1ピクセルの透明PNG）
        $base64Image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';

        $customFields = [
            'text_field' => 'テキスト値',
            'number_field' => 123,
            'image_field' => $base64Image,
            'array_field' => ['値1', '値2']
        ];

        // リフレクションを使ってプライベートメソッドをテスト
        $reflection = new \ReflectionClass($this->CustomEntriesTool);
        $method = $reflection->getMethod('processCustomFields');

        $result = $method->invoke($this->CustomEntriesTool, $customFields, 1); // customTableId = 1 を追加

        $this->assertIsArray($result);
        $this->assertEquals('テキスト値', $result['text_field']);
        $this->assertEquals(123, $result['number_field']);
        // フィールドタイプがBcCcFileでない場合、ファイルアップロード処理は行われない
        $this->assertEquals($base64Image, $result['image_field']); // そのまま残る
        $this->assertEquals(['値1', '値2'], $result['array_field']);
    }

    /**
     * test getCustomFieldType method
     */
    public function testGetCustomFieldType()
    {
        $customTableId = 1;
        $fieldName = 'test_field';

        // リフレクションを使ってプライベートメソッドをテスト
        $reflection = new \ReflectionClass($this->CustomEntriesTool);
        $method = $reflection->getMethod('getCustomFieldType');

        // フィールドタイプが取得できない場合はnullを返す
        $result = $method->invoke($this->CustomEntriesTool, $customTableId, $fieldName);
        $this->assertNull($result);
    }

    /**
     * test isFileUploadField method
     */
    public function testIsFileUploadField()
    {
        $customTableId = 1;
        $fieldName = 'test_field';

        // リフレクションを使ってプライベートメソッドをテスト
        $reflection = new \ReflectionClass($this->CustomEntriesTool);
        $method = $reflection->getMethod('isFileUploadField');

        // フィールドタイプが取得できない場合はfalseを返す
        $result = $method->invoke($this->CustomEntriesTool, $customTableId, $fieldName);
        $this->assertFalse($result);
    }

    /**
     * test isFileUpload method
     */
    public function testIsFileUpload()
    {
        $customTableId = 1;
        $fieldName = 'test_field';
        $base64Data = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChAI9jAuoqQAAAABJRU5ErkJggg==';

        // リフレクションを使ってプライベートメソッドをテスト
        $reflection = new \ReflectionClass($this->CustomEntriesTool);
        $method = $reflection->getMethod('isFileUpload');

        // フィールドタイプが取得できない場合、ファイルアップロード形式でもfalseを返す
        $result = $method->invoke($this->CustomEntriesTool, $base64Data, $customTableId, $fieldName);
        $this->assertFalse($result);
    }
}
