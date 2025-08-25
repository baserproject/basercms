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

namespace BcCustomContent\Test\TestCase\Service;

use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcCustomContent\Service\CustomFieldsServiceInterface;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Test\Scenario\CustomFieldsScenario;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\Routing\Router;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * CustomFieldsServiceTest
 */
class CustomFieldsServiceTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Test subject
     *
     * @var $CustomFieldsService
     */
    public $CustomFieldsService;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->CustomFieldsService = $this->getService(CustomFieldsServiceInterface::class);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->CustomFieldsService);
        parent::tearDown();
    }

    /**
     * test __construct
     */
    public function test__construct()
    {
        $this->assertTrue(isset($this->CustomFieldsService->CustomFields));
        $this->assertTrue(isset($this->CustomFieldsService->CustomEntries));
    }

    /**
     * test getNew
     */
    public function test_getNew()
    {
        //テストメソッドを呼ぶ
        $result = $this->CustomFieldsService->getNew();
        //戻る値を確認
        $this->assertTrue($result->status);
        $this->assertEquals('', $result->placeholder);
        $this->assertEquals('BcCcText', $result->type);
        $this->assertEquals('', $result->source);
        $this->assertEquals('', $result->auto_convert);
    }

    /**
     * test get
     */
    public function test_get()
    {
        //データを生成
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //対象メソッドをコール
        $rs = $this->CustomFieldsService->get(1);
        //戻る値を確認
        $this->assertEquals(1, $rs->id);
        $this->assertEquals('recruit_category', $rs->name);
        $this->assertEquals('求人分類', $rs->title);

        //存在しないIDを指定した場合、
        $this->expectException(RecordNotFoundException::class);
        $this->expectExceptionMessage('Record not found in table `custom_fields`');
        $this->CustomFieldsService->get(111);
    }

    /**
     * test getIndex
     */
    public function test_getIndex()
    {
        //データを生成
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //対象メソッドをコール
        $rs = $this->CustomFieldsService->getIndex()->toArray();
        //戻る値を確認
        $this->assertCount(2, $rs);
        $this->assertEquals('求人分類', $rs[0]->title);
        $this->assertEquals('この仕事の特徴', $rs[1]->title);
    }

    /**
     * test create
     */
    public function test_create()
    {
        //Postデータを準備
        $data = [
            'title' => '求人分類',
            'name' => 'recruit_category',
            'type' => 'BcCcRelated',
            'status' => 1,
            'default_value' => '新卒採用',
        ];
        //正常系をテスト
        $rs = $this->CustomFieldsService->create($data);
        //戻る値を確認
        $this->assertEquals($rs->title, '求人分類');
        $this->assertEquals($rs->default_value, '新卒採用');

        //異常系をテスト
        $this->expectException(PersistenceFailedException::class);
        $this->expectExceptionMessage('Entity save failure. Found the following errors (title._empty: "項目見出しを入力してください。")');
        $this->CustomFieldsService->create(['title' => null]);
    }

    /**
     * test update
     */
    public function test_update()
    {
        //データを生成
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        $customField = $this->CustomFieldsService->get(1);
        $customField->title = 'test edit title';
        $customField->validate = ['EMAIL_CONFIRM'];
        $customField->meta = ['BcCustomContent' => ['email_confirm' => 'aa']];

        $request = $this->getRequest('/')->withData('validate', ['EMAIL_CONFIRM', 'FILE_EXT', 'MAX_FILE_SIZE']);
        Router::setRequest($request);

        //正常系をテスト
        $rs = $this->CustomFieldsService->update($customField, $customField->toArray());
        //戻る値を確認
        $this->assertEquals($rs->title, 'test edit title');

        //異常系をテスト
        $customField->title = null;
        $customField->meta = ['BcCustomContent' => ['email_confirm' => 'aa']];
        $customField->validate = ['EMAIL_CONFIRM'];
        $this->expectException(PersistenceFailedException::class);
        $this->expectExceptionMessage('Entity save failure. Found the following errors (title._empty: "項目見出しを入力してください。")');
        $this->CustomFieldsService->update($customField, $customField->toArray());
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        //サービスクラス
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTableService = $this->getService(CustomTablesServiceInterface::class);

        //データを生成
        $customTableService->create([
            'id' => 1,
            'name' => 'recruit',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);
        $dataBaseService->addColumn('custom_entry_1_recruit', 'recruit_category', 'text');
        $this->loadFixtureScenario(CustomFieldsScenario::class);

        //対象メソッドをコール
        $rs = $this->CustomFieldsService->delete(1);
        //戻る値を確認
        $this->assertTrue($rs);
        //カラムrecruit_categoryが削除されたか確認すること
        $this->assertFalse($dataBaseService->columnExists('custom_entry_1_recruit', 'recruit_category'));
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit');
        //削除したカスタムフィールドが存在しないか確認すること
        $this->expectException(RecordNotFoundException::class);
        $this->CustomFieldsService->get(1);
    }

    /**
     * test getList
     */
    public function test_getList()
    {
        //データを生成
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //対象メソッドをコール
        $rs = $this->CustomFieldsService->getList();
        //戻る値を確認
        $this->assertEquals('求人分類', $rs[1]);
        $this->assertEquals('この仕事の特徴', $rs[2]);
    }

    /**
     * test getFieldTypes
     */
    public function test_getFieldTypes()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        //対象メソッドをコール
        $rs = $this->CustomFieldsService->getFieldTypes();
        //戻る値を確認
        $this->assertEquals($rs['基本'], [
            'BcCcEmail' => 'Eメール',
            'BcCcHidden' => '隠しフィールド',
            'BcCcPassword' => 'パスワード',
            'BcCcTel' => '電話番号',
            'BcCcText' => 'テキスト',
            'BcCcTextarea' => 'テキストエリア',
        ]);
        $this->assertEquals($rs['日付'], [
            'BcCcDate' => '日付（年月日）',
            'BcCcDateTime' => '日付（年月日時間）',
        ]);
        $this->assertEquals($rs['選択'], [
            'BcCcCheckbox' => 'チェックボックス',
            'BcCcMultiple' => 'マルチチェックボックス',
            'BcCcPref' => '都道府県リスト',
            'BcCcRadio' => 'ラジオボタン',
            'BcCcRelated' => '関連データ',
            'BcCcSelect' => 'セレクトボックス',
        ]);
        $this->assertEquals($rs['コンテンツ'], [
            'BcCcFile' => 'ファイル',
            'BcCcWysiwyg' => 'Wysiwyg エディタ',
        ]);
        $this->assertEquals($rs['その他'], [
            'group' => 'グループ',
            'BcCcAutoZip' => '自動補完郵便番号',
        ]);
    }

    /**
     * test createIndexConditions
     */
    public function test_createIndexConditions()
    {
        $service = $this->CustomFieldsService;

        // statusのみ
        $conditions = ['status' => 1];
        $query = $service->CustomFields->find();
        $query = $service->createIndexConditions($query, $conditions);
        $sql = $query->sql();
        $this->assertStringContainsString('status =', $sql);

        // titleのみ
        $conditions = ['title' => 'テスト'];
        $query = $service->CustomFields->find();
        $query = $service->createIndexConditions($query, $conditions);
        $sql = $query->sql();
        $this->assertStringContainsString('title LIKE', $sql);

        // typeのみ
        $conditions = ['type' => 'BcCcText'];
        $query = $service->CustomFields->find();
        $query = $service->createIndexConditions($query, $conditions);
        $sql = $query->sql();
        $this->assertStringContainsString('field_type =', $sql);

        // nameのみ
        $conditions = ['name' => 'test_name'];
        $query = $service->CustomFields->find();
        $query = $service->createIndexConditions($query, $conditions);
        $sql = $query->sql();
        $this->assertStringContainsString('name =', $sql);

        // status + title + type + name の複合条件
        $conditions = [
            'status' => 1,
            'title' => '複合',
            'type' => 'BcCcText',
            'name' => '複合名'
        ];
        $query = $service->CustomFields->find();
        $query = $service->createIndexConditions($query, $conditions);
        $sql = $query->sql();
        $this->assertStringContainsString('status =', $sql);
        $this->assertStringContainsString('title LIKE', $sql);
        $this->assertStringContainsString('field_type =', $sql);
        $this->assertStringContainsString('name =', $sql);
    }
}
