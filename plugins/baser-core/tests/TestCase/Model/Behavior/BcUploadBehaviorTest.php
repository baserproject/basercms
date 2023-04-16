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
namespace BaserCore\Test\TestCase\Model\Behavior;

use ArrayObject;
use Cake\ORM\Entity;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Model\Table\ContentsTable;
use BaserCore\Model\Behavior\BcUploadBehavior;
use BaserCore\Service\ContentsServiceInterface;

/**
 * Class BcUploadBehaviorTest
 *
 * @property BcUploadBehavior $BcUploadBehavior
 * @property ContentsTable $ContentsTable
 * @property ContentsServiceInterface $ContentsService
 */
class BcUploadBehaviorTest extends BcTestCase
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Pages',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.ContentFolders',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.SiteConfigs',
    ];


    /**
     * @var ContentsTable|BcUploadBehavior
     */
    public $table;

    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->table = $this->getTableLocator()->get('BaserCore.Contents');
        $this->table->setPrimaryKey(['id']);
        $this->table->addBehavior('BaserCore.BcUpload');
        $this->BcUploadBehavior = $this->table->getBehavior('BcUpload');
        $this->ContentsService = $this->getService(ContentsServiceInterface::class);
        $this->uploadedData = [
            'eyecatch' => [
                "tmp_name" => "/tmp/testBcUpload.png",
                "error" => 0,
                "name" => "test.png",
                "type" => "image/png",
                "size" => 100,
                'delete' => 0,
                '_' => 'test.png',
                'uploadable' => true,
                'ext' => 'png'
            ]
        ];
        $this->eyecatchField = [
            'name' => 'eyecatch',
            'ext' => 'gif',
            'upload' => true,
            'type' => 'image',
            'getUniqueFileName' => true,
        ];
        $this->savePath = $this->BcUploadBehavior->BcFileUploader[$this->table->getAlias()]->savePath;
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        session_unset();
        unset($this->table, $this->BcUploadBehavior, $this->savePath, $this->uploadedData, $this->eyecatchField);
        parent::tearDown();
    }

    /**
     * testInitialize
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertTrue(isset($this->BcUploadBehavior->BcFileUploader));
    }

    /**
     * Before Validate
     */
    public function testBeforeMarshal()
    {
        $this->table->dispatchEvent('Model.beforeMarshal', ['data' => new ArrayObject($this->uploadedData), 'options' => new ArrayObject()]);
        // setupRequestDataが実行されてるか確認
        $this->assertNotNull($this->BcUploadBehavior->BcFileUploader[$this->table->getAlias()]->getUploadingFiles());
    }

    /**
     * After save
     *
     * @return boolean
     */
    public function testAfterSave()
    {
       // 画像を新規追加する場合
        $imgPath = ROOT . '/plugins/bc-admin-third/webroot/img/';
        $fileName = 'baser.power';
        $this->eyecatchField['width'] = 100;
        $this->eyecatchField['height'] = 100;
        $tmp = '/tmp/baser.power.gif';
        copy($imgPath . $fileName . '.' . $this->eyecatchField['ext'], $tmp);
        $uploadedFile = [
            'eyecatch' => [
                'name' => $fileName . '.' . $this->eyecatchField['ext'],
                'tmp_name' => $tmp,
                'ext' => $this->eyecatchField['ext'],
                'uploadable' => true
            ]
        ];
        $this->BcUploadBehavior->BcFileUploader[$this->table->getAlias()]->setUploadingFiles($uploadedFile);
        $this->BcUploadBehavior->BcFileUploader[$this->table->getAlias()]->settings['fields']['eyecatch'] = $this->eyecatchField;
        // 新規保存の場合
        $entity = new Entity(['id' => 6, 'eyecatch' => 'baser.power.gif']);
        $this->table->dispatchEvent('Model.afterSave', ['entity' => $entity, 'options' => new ArrayObject()]);
        $this->assertFileExists($this->savePath . 'baser.power.gif');
        // 削除の場合
        $this->table->dispatchEvent('Model.beforeMarshal', ['data' => new ArrayObject(['id' => 6, 'eyecatch_delete' => true]), 'options' => new ArrayObject()]);
        $return = $this->table->dispatchEvent('Model.afterSave', ['entity' => $entity, 'options' => new ArrayObject()]);
        $this->assertEquals('', $return->getData('entity')->eyecatch);
        $this->assertFileDoesNotExist($this->savePath . 'baser.power.gif');
    }

    /**
     * 一時ファイルとして保存する
     * セッション時のテスト
     */
    public function testSaveTmpFiles()
    {
        touch($this->uploadedData['eyecatch']['tmp_name']);
        $entity = $this->BcUploadBehavior->saveTmpFiles($this->uploadedData, 1);
        $tmpId = $this->BcUploadBehavior->BcFileUploader[$this->table->getAlias()]->tmpId;
        $this->assertEquals("00000001_eyecatch.png", $entity['eyecatch_tmp'], 'saveTmpFiles()の返り値が正しくありません');
        $this->assertEquals(1, $tmpId, 'tmpIdが正しく設定されていません');
        @unlink($this->uploadedData['eyecatch']['tmp_name']);
    }

    /**
     * Before delete
     * 画像ファイルの削除を行う
     * 削除に失敗してもデータの削除は行う
     *
     * @return void
     */
    public function testBeforeDelete()
    {
        $this->BcUploadBehavior->BcFileUploader[$this->table->getAlias()]->setupRequestData([]);
        $filePath = $this->savePath . 'test.png';
        touch($filePath);
        $trash = $this->ContentsService->getIndex(['withTrash' => true, 'deleted_date!' => null])->first();
        $trash->eyecatch = 'test.png';
        $this->ContentsService->update($trash, ['eyecatch' => 'test.png']);
        $this->table->dispatchEvent('Model.beforeDelete', ['entity' => $trash, 'options' => new ArrayObject()]);
        $this->assertFileDoesNotExist($filePath);
        @unlink($filePath);
    }

    /**
     * test getSettings
     */
    public function testGetSettingsAndSetSettings()
    {
        $settings = $this->BcUploadBehavior->getSettings();
        $this->assertIsArray($settings);
        $this->BcUploadBehavior->setSettings([]);
        $settings = $this->BcUploadBehavior->getSettings();
        $this->assertEquals([
            'saveDir' => '',
            'existsCheckDirs' => [],
            'fields' => []
        ], $settings);
    }

    /**
     * test getSaveDir
     */
    public function testGetSaveDir()
    {
        $dir = $this->BcUploadBehavior->getSaveDir();
        $this->assertEquals('/var/www/html/webroot/files/contents/', $dir);
    }

    /**
     * test getFileUploader
     */
    public function testGetFileUploader()
    {
        $fileUploader = $this->BcUploadBehavior->getFileUploader();
        $this->assertEquals('BaserCore\Utility\BcFileUploader', get_class($fileUploader));
    }

    /**
     * test getOldEntity
     */
    public function testGetOldEntity()
    {
        $result = $this->BcUploadBehavior->getOldEntity(1);
        $this->assertEmpty($result->name);
    }

    /**
     * test renameToBasenameFields
     *
     * @param $filename
     * @param $copy
     * @param $fileList
     * @dataProvider renameToBasenameFieldsDataProvider
     */
    public function testRenameToBasenameFields($filename, $copy, $fileList)
    {
        $this->getRequest('/baser/admin/');
        // 初期化
        $entity = $this->table->get(1);
        $entity->eyecatch = $filename;
        $uploader = $this->BcUploadBehavior->getFileUploader();
        $uploader->setUploadingFiles(['eyecatch' => ['name' => $filename, 'ext' => 'txt']]);

        // ダミーファイルの生成
        touch($this->savePath . $filename);

        // テスト実行
        $this->BcUploadBehavior->renameToBasenameFields($entity, $copy);
        foreach ($fileList as $file) {
            $this->assertFileExists($path = $this->savePath . $file);
            @unlink($path);
        }
    }

    /**
     * @return array[]
     */
    public function renameToBasenameFieldsDataProvider(): array
    {
        return [
            // copyがfalseの場合、ファイルネームを変更する
            ['test.txt', false, ['00000001_eyecatch.txt']],
            // copyがtrueの場合、ファイルをコピーした後ファイルネームを変更する。コピー元ファイルはそのまま残す。
            ['test.txt', true, ['test.txt', '00000001_eyecatch.txt']],
        ];
    }

}
