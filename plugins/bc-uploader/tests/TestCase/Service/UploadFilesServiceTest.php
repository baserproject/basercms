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

namespace BcUploader\Test\TestCase\Service;

use BaserCore\Error\BcException;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Factory\UserFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcFile;
use BcUploader\Model\Table\UploaderFilesTable;
use BcUploader\Service\UploaderConfigsServiceInterface;
use BcUploader\Service\UploaderFilesService;
use BcUploader\Service\UploaderFilesServiceInterface;
use BcUploader\Test\Factory\UploaderConfigFactory;
use BcUploader\Test\Scenario\UploaderFilesScenario;
use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use BcUploader\Test\Factory\UploaderCategoryFactory;
use BcUploader\Test\Factory\UploaderFileFactory;

/**
 * UploadFilesServiceTest
 * @property UploaderFilesService $UploaderFilesService
 */
class UploadFilesServiceTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->UploaderFilesService = $this->getService(UploaderFilesServiceInterface::class);
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
        $this->truncateTable('uploader_categories');
        $this->truncateTable('uploader_files');
    }

    /**
     * test construct
     */
    public function test_construct()
    {
        $this->UploaderFilesService->__construct();
        $this->assertInstanceOf(UploaderFilesTable::class, $this->UploaderFilesService->UploaderFiles);
        $this->assertInstanceOf(UploaderConfigsServiceInterface::class, $this->UploaderFilesService->uploaderConfigsService);

    }

    /**
     * test getIndex
     */
    public function test_getIndex()
    {
        //準備
        $this->loadFixtureScenario(UploaderFilesScenario::class);

        //正常系実行: パラメータなしで
        $result = $this->UploaderFilesService->getIndex([])->all();
        $this->assertCount(6, $result);
        //正常系実行: numパラメータを入れる
        $result = $this->UploaderFilesService->getIndex(['num' => 2])->all();
        $this->assertCount(2, $result);
        //正常系実行: nameパラメータを入れる
        $result = $this->UploaderFilesService->getIndex(['name' => 'a'])->all();
        $this->assertCount(1, $result);
        //正常系実行: uploader_typeパラメータを入れる
        $result = $this->UploaderFilesService->getIndex(['uploader_type' => 'img'])->all();
        $this->assertCount(6, $result);

    }

    /**
     * test createAdminIndexConditions
     */
    public function test_createAdminIndexConditions()
    {
        $this->loadFixtureScenario(UploaderFilesScenario::class);
        //正常系実行
        $param = [
            'conditions' => [
                'alt' => 'a'
            ],
            'uploader_category_id' => 1,
            'uploader_type' => 'img',
            'name' => 'a',
        ];
        $result = $this->execPrivateMethod($this->UploaderFilesService, 'createAdminIndexConditions', [$param]);
        $this->assertIsArray($result);
        $this->assertEquals('a', $result['alt']);
        $this->assertEquals(1, $result['UploaderFiles.uploader_category_id']);
        $this->assertEquals([
            ['UploaderFiles.name LIKE' => '%.png'],
            ['UploaderFiles.name LIKE' => '%.jpg'],
            ['UploaderFiles.name LIKE' => '%.gif']
        ], $result['or']);
        $this->assertEquals([
            'or' => [
                ['UploaderFiles.name LIKE' => '%a%'],
                ['UploaderFiles.alt LIKE' => '%a%'],
            ]
        ], $result['and']);
        //uploader_typeを変えるケース
        $param = [
            'uploader_type' => 'etc',
        ];
        $result = $this->execPrivateMethod($this->UploaderFilesService, 'createAdminIndexConditions', [$param]);
        $this->assertEquals([
            ['UploaderFiles.name NOT LIKE' => '%.png'],
            ['UploaderFiles.name NOT LIKE' => '%.jpg'],
            ['UploaderFiles.name NOT LIKE' => '%.gif']
        ], $result['and']);

    }

    /**
     * test getControlSource
     */
    public function test_getControlSource()
    {
        //準備
        //フィクチャーからデーターを生成: UploaderCategory
        UploaderCategoryFactory::make(['id' => 1, 'name' => 'blog'])->persist();
        UploaderCategoryFactory::make(['id' => 2, 'name' => 'contact'])->persist();
        UploaderCategoryFactory::make(['id' => 3, 'name' => 'service'])->persist();
        //フィクチャーからデーターを生成: User
        UserFactory::make(['id' => 1, 'name' => 'test user1', 'nickname' => 'Nghiem1'])->persist();
        UserFactory::make(['id' => 2, 'name' => 'test user2', 'nickname' => 'Nghiem2'])->persist();
        UserFactory::make(['id' => 3, 'name' => 'test user3', 'nickname' => 'Nghiem3'])->persist();

        //正常系実行: user_idパラメータを入れる
        $result = $this->UploaderFilesService->getControlSource('user_id');
        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        $this->assertEquals('Nghiem1', $result[1]);
        //正常系実行: uploader_category_idパラメータを入れる
        $result = $this->UploaderFilesService->getControlSource('uploader_category_id');
        $this->assertCount(3, $result);
        $this->assertEquals('contact', $result[2]);
        //正常系実行: パラメータなし
        $result = $this->UploaderFilesService->getControlSource();
        $this->assertFalse($result);

        //異常系実行
        $result = $this->UploaderFilesService->getControlSource('test');
        $this->assertFalse($result);

    }

    /**
     * test get
     */
    public function test_get()
    {
        //準備
        //フィクチャーからデーターを生成: UploaderCategory
        UploaderFileFactory::make(['id' => 1, 'name' => 'social_new.jpg', 'atl' => 'social_new.jpg', 'uploader_category_id' => 1, 'user_id' => 1])->persist();
        UploaderFileFactory::make(['id' => 2, 'name' => 'widget-hero.jpg', 'atl' => 'widget-hero.jpg', 'uploader_category_id' => 1, 'user_id' => 1])->persist();
        //正常系実行
        $result = $this->UploaderFilesService->get(1);
        $this->assertEquals('social_new.jpg', $result->name);
        //異常系実行
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->UploaderFilesService->get(100);

    }

    /**
     * test delete
     */
    public function test_delete()
    {
        //準備
        //フィクチャーからデーターを生成: UploaderCategory
        $this->loadFixtureScenario(UploaderFilesScenario::class);
        //正常系実行
        $this->assertTrue($this->UploaderFilesService->delete(1));
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->UploaderFilesService->get(1);

    }

    /**
     * test delete not found
     */
    public function test_delete_not_found()
    {
        //準備
        //フィクチャーからデーターを生成: UploaderCategory
        UploaderFileFactory::make(['id' => 10, 'name' => 'social_new.jpg', 'atl' => 'social_new.jpg', 'uploader_category_id' => 1, 'user_id' => 1])->persist();
        UploaderFileFactory::make(['id' => 20, 'name' => 'widget-hero.jpg', 'atl' => 'widget-hero.jpg', 'uploader_category_id' => 1, 'user_id' => 1])->persist();
        //正常系実行
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->UploaderFilesService->delete(100);
    }

    /**
     * test create
     */
    public function test_create()
    {
        $this->markTestIncomplete('このテストは未確認です。');
        //準備
        $uploaderFilesTable = TableRegistry::getTableLocator()->get('BcUploader.UploaderFiles');
        $settings = $uploaderFilesTable->getSettings();
        $savePath = WWW_ROOT . 'files' . DS . $settings['saveDir'] . DS . 'test.txt';
        $tmpPath = TMP . 'tmp.txt';
        $File = new BcFile($tmpPath);
        $File->create();
        $File->write("hello");
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loginAdmin($this->getRequest());
        $postData = [
            'file' => [
                'name' => 'test.txt',
                'tmp_name' => $tmpPath,
                'type' => 'etc',
                'size' => 100,
            ],
            'publish_begin' => \Cake\I18n\DateTime::yesterday(),
            'publish_end' => \Cake\I18n\DateTime::tomorrow(),
        ];
        //正常系実行
        $result = $this->UploaderFilesService->create($postData);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('test.txt', $result->file['name']);
        //レコードは保存されたのを確認
        $rs = $this->UploaderFilesService->get(1);
        $this->assertEquals(1, $rs->id);
        //フィルは作成されたのを確認
        $this->assertFileExists($savePath);
        //ファイル削除
        unlink($savePath);
        //異常系実行
        $this->expectException(BcException::class);
        $this->UploaderFilesService->create([]);


    }

    /**
     * test update
     */
    public function test_update()
    {
        //準備
        //フィクチャーからデーターを生成
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(UploaderFilesScenario::class);
        $entity = $this->UploaderFilesService->get(1);
        $postData = [
            'name' => 'test.jpg',
        ];
        //正常系実行
        $entity = $this->UploaderFilesService->update($entity, $postData);
        $this->assertEquals('test.jpg', $entity->name);
    }

    /**
     * 異常系実行
     */
//    public function test_update_error()
//    {
//        //準備
//        UploaderFileFactory::make(['id' => 1, 'name' => 'social_new.jpg', 'atl' => 'social_new.jpg', 'uploader_category_id' => 1, 'user_id' => 1, 'publish_begin' => '2017-07-09 03:38:07', 'publish_end' => '2017-07-09 03:38:07'])->persist();
//        UploaderConfigFactory::make(['name' => 'use_permission', 'value' => true])->persist();
//        $entity = $this->UploaderFilesService->get(1);
//        $postData = [
//            'user_id' => 99,
//        ];
//        $this->expectException(BcException::class);
//        $this->UploaderFilesService->update($entity, $postData);
//    }

    /**
     * test isEditable
     */
    public function test_isEditable()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        //準備
        UploaderConfigFactory::make(['name' => 'use_permission', 'value' => true])->persist();
        // ログインしている状態、アップローダーファイルにuser_id が設定されていない
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loginAdmin($this->getRequest('/baser/admin'));
        $result = $this->UploaderFilesService->isEditable([]);
        $this->assertFalse($result);

        // ログインしていない状態
        $result = $this->UploaderFilesService->isEditable([]);
        $this->assertFalse($result);

        // ログインしている状態、アップローダーファイルにuser_id が設定されている
        $result = $this->UploaderFilesService->isEditable(['user_id' => 1]);
        $this->assertTrue($result);

        //異常系実行
        $postData = [
            'user_id' => 99
        ];
        $result = $this->UploaderFilesService->isEditable($postData);
        $this->assertFalse($result);
    }

    /**
     * test getNew
     */
    public function test_getNew()
    {
        //準備
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loginAdmin($this->getRequest());
        //正常系実行
        $result = $this->UploaderFilesService->getNew();
        $this->assertEquals(1, $result->user_id);

    }

}
