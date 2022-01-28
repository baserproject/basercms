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
namespace BaserCore\Test\TestCase\Model\Behavior;

use ArrayObject;
use Cake\ORM\Entity;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Model\Table\ContentsTable;
use BaserCore\Model\Behavior\BcUploadBehavior;
use BaserCore\Service\ContentServiceInterface;

/**
 * Class BcUploadBehaviorTest
 *
 * @property BcUploadBehavior $BcUploadBehavior
 * @property ContentsTable $ContentsTable
 * @property ContentServiceInterface $ContentService
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
        $this->ContentService = $this->getService(ContentServiceInterface::class);
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
        $result = $this->table->dispatchEvent('Model.beforeMarshal', ['data' => new ArrayObject($this->uploadedData), 'options' => new ArrayObject()]);
        // setupRequestDataが実行されてるか確認
        $this->assertNotNull($this->BcUploadBehavior->BcFileUploader[$this->table->getAlias()]->getUploadingFiles());
        // 保存前にeyecatchをオブジェクトではなく、stringに変換してるか確認
        $this->assertEquals("test.png", $result->getData('data')['eyecatch']);
    }

    /**
     * Before save
     */
    public function testBeforeSave()
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
        $return = $this->table->dispatchEvent('Model.beforeSave', ['entity' => $entity, 'options' => new ArrayObject()]);
        $this->assertTrue($return->getResult());
        $this->assertFileExists($this->savePath . 'baser.power.gif');
        // 削除の場合
        $uploadedFile = [
            'eyecatch' => [
                'name' => '',
                'tmp_name' => '',
                'ext' => $this->eyecatchField['ext'],
                'uploadable' => false,
                'delete' => 1
            ]
        ];
        copy($imgPath . $fileName . '.' . $this->eyecatchField['ext'], $this->savePath . '00000006_eyecatch.gif');
        $this->BcUploadBehavior->BcFileUploader[$this->table->getAlias()]->setUploadingFiles($uploadedFile);
        $return = $this->table->dispatchEvent('Model.beforeSave', ['entity' => $entity, 'options' => new ArrayObject()]);
        $this->assertTrue($return->getResult());
        $this->assertEmpty($return->getData('entity')->eyecatch);
        $this->assertFileNotExists($this->savePath . '00000006_eyecatch.gif');
        unlink($this->savePath . 'baser.power.gif');
    }

    /**
     * After save
     *
     * @return boolean
     */
    public function testAfterSave()
    {
        $this->getRequest('/baser/admin/');
        touch($this->savePath . 'test.png');
        $this->BcUploadBehavior->BcFileUploader[$this->table->getAlias()]->setUploadingFiles(['eyecatch' => ['name' => 'test.png', 'ext' => 'png']]);
        $this->BcUploadBehavior->BcFileUploader[$this->table->getAlias()]->uploaded = true;
        $entity = $this->table->get(1);
        $entity->eyecatch = 'test.png';
        $return = $this->table->dispatchEvent('Model.afterSave', ['entity' => $entity, 'options' => new ArrayObject()]);
        $this->assertNull($return->getResult());
        $this->assertEquals($return->getData('entity')->eyecatch, "00000001_eyecatch.png");
        unlink($this->savePath . "00000001_eyecatch.png");
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
        $this->assertEquals("00000001_eyecatch.png", $entity->eyecatch_tmp, 'saveTmpFiles()の返り値が正しくありません');
        $this->assertEquals(1, $tmpId, 'tmpIdが正しく設定されていません');
        @unlink($this->uploadedData['tmp_name']);
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
        $trash = $this->ContentService->getIndex(['withTrash' => true, 'deleted_date!' => null])->first();
        $trash->eyecatch = 'test.png';
        $this->ContentService->update($trash, ['eyecatch' => 'test.png']);
        $this->table->dispatchEvent('Model.beforeDelete', ['entity' => $trash, 'options' => new ArrayObject()]);
        $this->assertFileNotExists($filePath);
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
        $this->assertEmpty($settings);
    }

    /**
     * test getSaveDir
     */
    public function testGetSaveDir()
    {
        $dir = $this->BcUploadBehavior->getSaveDir();
        $this->assertEquals('/var/www/html/webroot/files/contents/', $dir);
    }

}
