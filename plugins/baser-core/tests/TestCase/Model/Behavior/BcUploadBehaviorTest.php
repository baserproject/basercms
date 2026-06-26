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
use BaserCore\Test\Scenario\ContentsScenario;
use Cake\ORM\Entity;
use Cake\Validation\Validator;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Model\Table\ContentsTable;
use BaserCore\Model\Behavior\BcUploadBehavior;
use BaserCore\Service\ContentsServiceInterface;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Laminas\Diactoros\UploadedFile;
use function Laminas\Diactoros\normalizeUploadedFiles;

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
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

    /**
     * Trait
     */
    use BcContainerTrait;

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

        $srcPath = '/var/www/html/webroot/img/basercms.png';
        $targetPath = '/tmp/testBcUpload.png';
        copy($srcPath, $targetPath);
        $this->uploadedData = normalizeUploadedFiles([
            'eyecatch' => [
                "tmp_name" => $targetPath,
                "error" => 0,
                "name" => "test.png",
                "type" => "image/png",
                "size" => 100,
                'delete' => 0,
                '_' => 'test.png',
                'uploadable' => true,
                'ext' => 'png'
            ]
        ]);

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
        $data = new ArrayObject($this->uploadedData);
        $this->table->dispatchEvent('Model.beforeMarshal', ['data' => $data, 'options' => new ArrayObject()]);
        // setupRequestDataが実行されてるか確認
        $this->assertNotNull(
            $this->BcUploadBehavior
                ->BcFileUploader[$this->table->getAlias()]
                ->getUploadingFiles($data['_bc_upload_id'])
        );
    }

    /**
     * Build Validator
     */
    public function testBuildValidator()
    {
        $validator = new Validator();
        $this->table->dispatchEvent('Model.buildValidator',
            ['validator' => $validator, 'name' => 'test']);
        $rules = $this->table->getValidator()->field('eyecatch');
        $this->assertNotNull($rules['checkFilePath']);
    }


    /**
     * After save
     *
     */
    public function testAfterSave()
    {
        $this->loadFixtureScenario(ContentsScenario::class);
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
        $bcUploadId = 1;
        $this->BcUploadBehavior->BcFileUploader[$this->table->getAlias()]->setUploadingFiles($uploadedFile, $bcUploadId);
        $this->BcUploadBehavior->BcFileUploader[$this->table->getAlias()]->settings['fields']['eyecatch'] = $this->eyecatchField;
        // 新規保存の場合
        $entity = new Entity(['id' => 6, 'eyecatch' => 'baser.power.gif', '_bc_upload_id' => $bcUploadId]);
        $this->table->dispatchEvent('Model.afterSave', ['entity' => $entity, 'options' => new ArrayObject()]);
        $this->assertFileExists($this->savePath . 'baser.power.gif');
        // 削除の場合
        $deleteData = new ArrayObject(['id' => 6, 'eyecatch_delete' => true]);
        $this->table->dispatchEvent('Model.beforeMarshal', ['data' => $deleteData, 'options' => new ArrayObject()]);
        $entity->set('_bc_upload_id', $deleteData['_bc_upload_id']);
        $this->BcUploadBehavior->oldEntity[$this->table->getAlias()][$deleteData['_bc_upload_id']] = new Entity([
            'id' => 6,
            'eyecatch' => 'baser.power.gif',
            '_bc_upload_id' => $deleteData['_bc_upload_id']
        ]);
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
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
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
        $this->loadFixtureScenario(ContentsScenario::class);
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
        $this->loadFixtureScenario(ContentsScenario::class);
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
        $this->loadFixtureScenario(ContentsScenario::class);
        $this->getRequest('/baser/admin/');
        // 初期化
        $entity = $this->table->get(1);
        $entity->eyecatch = $filename;
        $entity->_bc_upload_id = 1;
        $uploader = $this->BcUploadBehavior->getFileUploader();
        $uploader->setUploadingFiles(['eyecatch' => ['name' => $filename, 'ext' => 'txt']], $entity->_bc_upload_id);

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
    public static function renameToBasenameFieldsDataProvider(): array
    {
        return [
            // copyがfalseの場合、ファイルネームを変更する
            ['test.txt', false, ['00000001_eyecatch.txt']],
            // copyがtrueの場合、ファイルをコピーした後ファイルネームを変更する。コピー元ファイルはそのまま残す。
            ['test.txt', true, ['test.txt', '00000001_eyecatch.txt']],
        ];
    }

    /**
     * test afterMarshal calls rollbackFile
     * afterMarshalイベントでrollbackFileが呼ばれる
     */
    public function testAfterMarshalCallsRollbackFile()
    {
        $this->loadFixtureScenario(ContentsScenario::class);
        $table = $this->table;

        // アップロードファイルを準備
        $tmpFile = '/tmp/test_upload_' . time() . '.jpg';
        file_put_contents($tmpFile, 'test image data');

        $uploadedFiles = normalizeUploadedFiles([
            'eyecatch' => [
                'name' => 'test.jpg',
                'tmp_name' => $tmpFile,
                'type' => 'image/jpeg',
                'size' => 100,
                'error' => 0
            ]
        ]);

        $data = [
            'id' => 1,
            'name' => 'test',
            'eyecatch' => $uploadedFiles['eyecatch']
        ];

        $entity = $table->newEntity($data);
        $entity->setError('name', ['name field error']); // 他フィールドでバリデーションエラー

        // eyecatch_tmpが設定されていることを確認（rollbackFileが実行された証拠）
        $this->assertNotEmpty($entity->get('eyecatch_tmp'), 'rollbackFileが実行されていません');

        @unlink($tmpFile);
    }

    /**
     * test beforeMarshal adds accessible fields
     * beforeMarshalで_tmpと_deleteがaccessibleFieldsに追加される
     */
    public function testBeforeMarshalAddsAccessibleFields()
    {
        $this->loadFixtureScenario(ContentsScenario::class);
        $table = $this->table;

        $data = [
            'id' => 1,
            'name' => 'test',
            'eyecatch_tmp' => 'tmp_file.jpg',
            'eyecatch_delete' => '1'
        ];

        $entity = $table->newEntity($data);

        // _tmpと_deleteフィールドがアクセス可能になっていることを確認
        $this->assertTrue($entity->isAccessible('eyecatch_tmp'), 'eyecatch_tmpがaccessibleではありません');
        $this->assertTrue($entity->isAccessible('eyecatch_delete'), 'eyecatch_deleteがaccessibleではありません');

        // 値が設定されていることを確認
        $this->assertEquals('tmp_file.jpg', $entity->get('eyecatch_tmp'));
        $this->assertEquals('1', $entity->get('eyecatch_delete'));
    }

    /**
     * test afterMarshal does not rollback on success
     * バリデーションエラーがない場合はrollbackFileを呼ばない（eyecatch_tmpが設定されない）
     */
    public function testAfterMarshalNoRollbackOnSuccess()
    {
        $this->loadFixtureScenario(ContentsScenario::class);

        // エラーなしのエンティティを直接作成（バリデーションをスキップ）
        $entity = new Entity(['id' => 1, 'name' => 'valid_name', '_bc_upload_id' => 'test_no_err_id']);
        $this->assertFalse($entity->hasErrors(), 'エラーなしであるべきです');

        // afterMarshal を手動でディスパッチ
        $result = $this->table->dispatchEvent('Model.afterMarshal', [
            'entity' => $entity,
            'options' => new ArrayObject(),
        ]);
        $updatedEntity = $result->getData('entity');

        // エラーなしの場合はrollbackFileが呼ばれないため、eyecatch_tmpが設定されていないことを確認
        $this->assertEmpty($updatedEntity->get('eyecatch_tmp'), 'エラーなしの場合はeyecatch_tmpが設定されるべきではありません');
    }
}
