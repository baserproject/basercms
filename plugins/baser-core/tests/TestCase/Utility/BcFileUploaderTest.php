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
namespace BaserCore\Test\TestCase\Utility;

use ArrayObject;
use BaserCore\Model\Entity\Content;
use BaserCore\Test\Scenario\ContentsScenario;
use BaserCore\Utility\BcFile;
use BaserCore\Utility\BcFileUploader;
use BaserCore\Utility\BcFolder;
use Cake\ORM\Entity;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Laminas\Diactoros\UploadedFile;
use ReflectionClass;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Model\Table\ContentsTable;
use BaserCore\Model\Behavior\BcUploadBehavior;
use BaserCore\Service\ContentsServiceInterface;

/**
 * Class BcFileUploaderTest
 *
 * @property BcFileUploader $BcFileUploader
 * @property ContentsTable $ContentsTable
 * @property ContentsServiceInterface $ContentsService
 */
class BcFileUploaderTest extends BcTestCase
{

    /**
     * Trait
     */
    use BcContainerTrait;
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

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
        $this->BcFileUploader = $this->table->getBehavior('BcUpload')->BcFileUploader[$this->table->getAlias()];
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
        $this->savePath = $this->BcFileUploader->savePath;
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        session_unset();
        unset($this->table, $this->BcFileUploader, $this->savePath, $this->uploadedData, $this->eyecatchField);
        parent::tearDown();
    }

    /**
     * ファイル等が内包されたディレクトリも削除する
     *
     * testGetFieldBasename()で使用します
     *
     * @param string $dir 対象のディレクトリのパス
     * @return void
     */
    public function removeDir($dir)
    {
        if ($handle = opendir("$dir")) {
            while(false !== ($item = readdir($handle))) {
                if ($item != "." && $item != "..") {
                    if (is_dir("$dir/$item")) {
                        $this->removeDir("$dir/$item");
                    } else {
                        unlink("$dir/$item");
                    }
                }
            }
            closedir($handle);
            rmdir($dir);
        }
    }

    /**
     * testInitialize
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertNotEmpty($this->BcFileUploader->settings);
        $this->assertNotEmpty($this->BcFileUploader->savePath);
        $this->assertNotEmpty($this->BcFileUploader->existsCheckDirs);
        $this->assertNotEmpty($this->BcFileUploader->Session);
        // testフォルダがない場合作られるかテスト
        $this->BcFileUploader->initialize(['saveDir' => 'test'], $this->table);
        $this->assertFileExists("/var/www/html/webroot/files/test/");
        rmdir("/var/www/html/webroot/files/test/");
    }

    /**
     * testGetSettings
     *
     * @return void
     */
    public function testGetSettings()
    {
        $config = [
            'saveDir' => "contents",
            'fields' => [
                'eyecatch' => [
                    "type" => "image",
                    "namefield" => "id",
                    "nameadd" => true,
                    "nameformat" => "%08d",
                    "subdirDateFormat" => "Y/m",
                    "imagecopy" => [],
                ]
            ]
        ];
        $setting = $this->BcFileUploader->getSettings($config);
        $this->assertEquals("eyecatch", $setting['fields']['eyecatch']['name']);
        $this->assertEquals(false, $setting['fields']['eyecatch']['imageresize']);
        $this->assertEquals(true, $setting['fields']['eyecatch']['getUniqueFileName']);
    }

    /**
     * 保存先のフォルダを取得する
     */
    public function testGetSaveDir()
    {
        // NOTE: WWW_ROOTが/var/www/html/appではなく、/var/www/htmlであることに注意
        $result = $this->BcFileUploader->getSaveDir();
        $this->assertEquals("/var/www/html/webroot/files/contents/", $result);
    }

    /**
     * testGetExistsCheckDirs
     *
     * @return void
     */
    public function testGetExistsCheckDirs()
    {
        $result = $this->execPrivateMethod($this->BcFileUploader, "getExistsCheckDirs");
        $this->assertEquals("/var/www/html/webroot/files/contents/", $result[0]);
    }

    /**
     * リクエストされたデータを処理しやすいようにセットアップする
     */
    public function testSetupRequestData()
    {
        //テストファイルを作成
        $filePath = TMP . 'test_upload' . DS;
        (new BcFolder($filePath))->create();
        $testFile = $filePath . 'uploadTestFile.html';
        (new BcFile($testFile))->create();

        // upload=falseの場合のテスト
        $data = new ArrayObject([
            'eyecatch' => new UploadedFile(
                $testFile,
                0,
                UPLOAD_ERR_OK,
                "",
                "image/png",
            )
        ]);
        $data = $this->BcFileUploader->setupRequestData($data);
        $uploaded = $this->BcFileUploader->getUploadingFiles($data['_bc_upload_id']);
        $this->assertFalse($uploaded['eyecatch']['uploadable']);
        // upload=trueの場合のテスト
        $data = new ArrayObject([
            'eyecatch' => new UploadedFile(
                $testFile,
                0,
                UPLOAD_ERR_OK,
                'uploadTestFile.html',
                "image/png",
            )
        ]);
        $uploadedData = $this->BcFileUploader->setupRequestData($data);
        $uploaded = $this->BcFileUploader->getUploadingFiles($uploadedData['_bc_upload_id']);
        $this->assertTrue($uploaded['eyecatch']['uploadable']);
        $this->assertEquals("png", $uploaded['eyecatch']['ext']);
        //  新しいデータが送信されず、既存データを引き継ぐ場合
        $data = new ArrayObject([
            'eyecatch' => new UploadedFile(
                $testFile,
                0,
                UPLOAD_ERR_NO_FILE,
                'uploadTestFile.html',
                "image/png",
            ),
            'eyecatch_' => 'test.png',
        ]);
        $data = $this->BcFileUploader->setupRequestData($data);
        $uploaded = $this->BcFileUploader->getUploadingFiles($data['_bc_upload_id']);
        $this->assertFalse($uploaded['eyecatch']['uploadable']);
        $this->assertEquals("test.png", $data['eyecatch']);

        //不要なフォルダを削除
        (new BcFolder($filePath))->delete();
    }

    /**
     * 一時ファイルとして保存する
     * セッション時のテスト
     */
    public function testSaveTmpFiles()
    {
        //テストファイルを作成
        $filePath = TMP . 'test_upload' . DS;
        (new BcFolder($filePath))->create();
        $testFile = $filePath . 'uploadTestFile.html';
        (new BcFile($testFile))->create();
        $data = [
            'eyecatch' => new UploadedFile(
                $testFile,
                10,
                UPLOAD_ERR_OK,
                'uploadTestFile.html',
                "image/png",
            )
        ];
        $entity = $this->BcFileUploader->saveTmpFiles($data, 1);
        $tmpId = $this->BcFileUploader->tmpId;
        $this->assertEquals("00000001_eyecatch.png", $entity->eyecatch_tmp, 'saveTmpFiles()の返り値が正しくありません');
        $this->assertEquals(1, $tmpId, 'tmpIdが正しく設定されていません');
        //不要なフォルダを削除
        (new BcFolder($filePath))->delete();
    }

    /**
     * test saveTmpFile
     */
    public function testSaveTmpFile()
    {
        //テストファイルを作成
        $filePath = TMP . 'test_upload' . DS;
        (new BcFolder($filePath))->create();
        $testFile = $filePath . 'uploadTestFile.png';
        (new BcFile($testFile))->create();
        $data = [
            'error' => 0,
            'name' => 'uploadTestFile.png',
            'size' => 1,
            'tmp_name' => $testFile,
            'ext' => 'png',
            'type' => 'image/png'
        ];

        $entity = $this->table->patchEntity($this->table->newEmptyEntity(), $data);
        $this->BcFileUploader->tmpId = 1;
        $this->BcFileUploader->saveTmpFile($this->BcFileUploader->settings['fields']['eyecatch'], $data, $entity);
        $this->assertNotEmpty($_SESSION['Upload']['00000001_eyecatch_png']);
        //不要なフォルダを削除
        (new BcFolder($filePath))->delete();
    }

    /**
     * test getSaveTmpFileName
     */
    public function testGetSaveTmpFileName()
    {
        //テストファイルを作成
        $filePath = TMP . 'test_upload' . DS;
        (new BcFolder($filePath))->create();
        $testFile = $filePath . 'uploadTestFile.png';
        (new BcFile($testFile))->create();
        $data = [
            'error' => 0,
            'name' => 'uploadTestFile.png',
            'size' => 1,
            'tmp_name' => $testFile,
            'ext' => 'png'
        ];
        $entity = $this->table->patchEntity($this->table->newEmptyEntity(), $data);
        $this->BcFileUploader->tmpId = 1;
        $file = $this->BcFileUploader->getSaveTmpFileName($this->BcFileUploader->settings['fields']['eyecatch'], $data, $entity);
        $this->assertEquals('00000001_eyecatch.png', $file);
        $file = $this->BcFileUploader->getSaveTmpFileName(['name' => 'eyecatch'], $data, $entity);
        $this->assertEquals('1_eyecatch.png', $file);
        //不要なフォルダを削除
        (new BcFolder($filePath))->delete();
    }

    /**
     * test setupTmpData
     */
    public function testSetupTmpData()
    {
        //テストファイルを作成
        $filePath = TMP . 'test_upload' . DS;
        (new BcFolder($filePath))->create();
        $testFile = $filePath . 'uploadTestFile.png';
        (new BcFile($testFile))->create();
        $file = [
            'eyecatch' => new UploadedFile(
                $testFile,
                10,
                UPLOAD_ERR_OK,
                'uploadTestFile.html',
                "image/png",
            )
        ];
        $this->BcFileUploader->setupTmpData($file);
        $this->assertFalse(isset($file['eyecatch_tmp']));

        //不要なフォルダを削除
        (new BcFolder($filePath))->delete();
    }

    /**
     * testDeleteFiles
     *
     * @return void
     */
    public function testDeleteFiles()
    {
        $this->loadFixtureScenario(ContentsScenario::class);
        $this->BcFileUploader->settings['fields']['eyecatch'] = $this->eyecatchField;
        // 削除を実行
        $fileName = '00000006_eyecatch.gif';
        $file = [
            'eyecatch' => [
                "name" => $fileName,
                'delete' => 1,
                '_' => '00000006_eyecatch.gif'
            ]
        ];
        $bcUploadId = 1;
        $this->BcFileUploader->setUploadingFiles($file, $bcUploadId);
        $targetPath = $this->savePath . $fileName;
        touch($targetPath);
        $this->BcFileUploader->deleteFiles($this->ContentsService->get(6), new Content(['_bc_upload_id' => $bcUploadId]));
        $this->assertFileDoesNotExist($targetPath);
        touch($targetPath);
        $this->BcFileUploader->deleteFiles(new Content(), new Content(['_bc_upload_id' => $bcUploadId]));
        $this->assertFileExists($targetPath);
        @unlink($targetPath);
    }


    /**
     * 削除対象かチェックしながらファイルを削除する
     */
    public function testDeleteFileWhileChecking()
    {
        $this->loadFixtureScenario(ContentsScenario::class);
        $fileName = '00000006_eyecatch.gif';
        $file = [
            'eyecatch' => [
                "name" => $fileName,
                'delete' => 1,
                '_' => '00000006_eyecatch.gif'
            ]
        ];
        $targetPath = $this->savePath . $fileName;
        /* @var Content $entity */
        $entity = $this->ContentsService->get(6);
        // ダミーのファイルを生成
        touch($targetPath);
        $this->BcFileUploader->deleteFileWhileChecking(
            $this->eyecatchField,
            $file['eyecatch'],
            $entity,
            $this->ContentsService->get(6)
        );
        $this->assertEmpty($entity->eyecatch);
        $this->assertFileDoesNotExist($targetPath);
        @unlink($targetPath);
    }


    /**
     * ファイル群を保存する
     */
    public function testSaveFiles()
    {
        $this->eyecatchField['ext'] = 'png';
        $this->BcFileUploader->settings['fields']['eyecatch']  = $this->eyecatchField;
        $filePath = $this->savePath . $this->uploadedData['eyecatch']['name'];
        $tmp = $this->uploadedData['eyecatch']['tmp_name'];
        touch($tmp);
        $bcUploadId = 1;
        $this->BcFileUploader->setUploadingFiles($this->uploadedData, $bcUploadId);
        $this->BcFileUploader->saveFiles(new Content(['_bc_upload_id' => $bcUploadId]));
        $this->assertFileExists($filePath);
        unlink($filePath);
    }

    /**
     * 保存対象かチェックしながらファイルを保存する(tmpIdがない場合)
     */
    public function testSaveFileWhileChecking()
    {
        $this->eyecatchField['ext'] = 'png';
        $filePath = $this->savePath . $this->uploadedData['eyecatch']['name'];
        // nameが空の場合 新規画像なしでの保存など
        $this->BcFileUploader->saveFileWhileChecking(
            $this->eyecatchField,
            ["eyecatch" => ['name' => '']],
            new Content()
        );
        $this->assertFileDoesNotExist($filePath);
        // nameがある場合 新規画像保存の場合
        touch($this->uploadedData['eyecatch']['tmp_name']);
        $this->BcFileUploader->saveFileWhileChecking(
            $this->eyecatchField,
            $this->uploadedData['eyecatch'],
            new Content()
        );
        $this->assertFileExists($filePath);
        $this->assertFileDoesNotExist($this->uploadedData['eyecatch']['tmp_name']);
        unlink($filePath);
    }

    /**
     * saveFilesのテスト
     * ファイルをコピーする
     *
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider saveFilesCanCopyDataProvider
     */
    public function testSaveFilesCanCopy($imagecopy, $message)
    {

        // TODO 2020/07/08 ryuring PHP7.4 で、gd が標準インストールされないため、テストがエラーとなるためスキップ
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        return;

        $this->initTestSaveFiles(1, ['name' => 'copy.gif', 'type' => 'image']);

        // パス情報
        $targetPath = $this->savePath . 'copy.gif';

        // 初期化
        $this->BcFileUploader->settings['EditorTemplate']['fields']['image']['imagecopy'] = $imagecopy;

        // 保存を実行
        $this->EditorTemplate->saveFiles($this->EditorTemplate->data);
        $this->assertFileExists($targetPath, $message);

        // 生成されたファイルを削除
        @unlink($targetPath);
        $this->deleteDummyOnTestSaveFiles();

    }

    public static function saveFilesCanCopyDataProvider()
    {
        return [
            [
                [['width' => 40, 'height' => 6]],
                'saveFiles()でファイルをコピーできません'
            ],
            [
                [
                    ['width' => 40, 'height' => 6],
                    ['width' => 30, 'height' => 6]
                ],
                'saveFiles()でファイルをコピーできません'
            ],
        ];
    }

    /**
     * saveFilesのテスト
     * ファイルをリサイズする
     *
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider saveFilesCanResizeDataProvider
     */
    public function testSaveFilesCanResize($imageresize, $expected, $message)
    {

        // TODO 2020/07/08 ryuring PHP7.4 で、gd が標準インストールされないため、テストがエラーとなるためスキップ
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        return;

        $this->initTestSaveFiles();

        // パス情報
        $targetPath = $this->savePath . 'basename.gif';

        // 初期化
        $this->BcFileUploader->settings['EditorTemplate']['fields']['image']['imageresize'] = $imageresize;

        // 保存を実行
        $this->EditorTemplate->saveFiles($this->EditorTemplate->data);

        $result = $this->BcFileUploader->getImageSize($targetPath);
        $this->assertEquals($expected, $result, $message);

        // 生成されたファイルを削除
        @unlink($targetPath);
        $this->deleteDummyOnTestSaveFiles();

    }

    public static function saveFilesCanResizeDataProvider()
    {
        return [
            [['width' => 20, 'height' => 10, 'thumb' => false], ['width' => 20, 'height' => 2], 'saveFiles()でファイルをリサイズできません'],
            [['width' => 20, 'height' => 10, 'thumb' => true], ['width' => 20, 'height' => 10], 'saveFiles()でファイルをリサイズできません'],
        ];
    }


    /**
     * セッションに保存されたファイルデータをファイルとして保存する
     */
    public function testMoveFileSessionToTmp()
    {
        $tmpId = 1;
        $fieldName = 'fieldName';
        $tmp_name = 'basercms_tmp';
        $basename = 'basename';
        $ext = 'png';
        $namefield = 'hoge';

        // パス情報
        $tmpPath = $this->savePath . $tmp_name;

        // 初期化
        $this->eyecatchField = [
            'name' => $fieldName,
            'namefield' => $namefield,
        ];
        $this->BcFileUploader->tmpId = $tmpId;

        // ダミーファイルの作成
        $file = new BcFile($tmpPath);
        $file->create();
        $file->write('dummy');

        // UploadedFileオブジェクトの作成
        $uploadedFile = new UploadedFile(
            $tmpPath,
            filesize($tmpPath),
            UPLOAD_ERR_OK,
            $basename . '.' . $ext,
            'image/png'
        );

        $this->uploadedData['eyecatch'] = $uploadedFile;

        // セッションを設定
        $entity = $this->BcFileUploader->saveTmpFiles($this->uploadedData, $tmpId);

        // 本題
        $targetName = $entity->eyecatch_tmp;
        $targetPath = $this->savePath . str_replace(['.', '/'], ['_', '_'], $targetName);

        $bcUploadId = 1;
        $data = new ArrayObject([
            "{$fieldName}_tmp" => $entity->eyecatch_tmp,
            '_bc_upload_id' => $bcUploadId,
        ]);
        // セッションからファイルを保存
        $this->BcFileUploader->moveFileSessionToTmp($data, $fieldName);

        // 判定
        $this->assertFileExists($targetPath, 'セッションに保存されたファイルデータをファイルとして保存できません');
        $result = $this->BcFileUploader->getUploadingFiles($bcUploadId)[$fieldName];
        $expected = [
            'error' => UPLOAD_ERR_OK,
            'name' => $targetName,
            'tmp_name' => $targetPath,
            'size' => 5,
            'type' => 'image/png',
            'uploadable' => true,
            'ext' => 'png'
        ];
        $this->assertEquals($expected, $result, 'アップロードされたデータとしてデータを復元できません');

        // 生成されたファイルを削除
        @unlink($tmpPath);
        @unlink($targetPath);

    }

    /**
     * ファイルを保存する(tmpIdがない場合)
     *
     */
    public function testSaveFile()
    {
        $ext = 'png';
        $this->eyecatchField['ext'] = $ext;
        $targetPath = $this->savePath . $this->uploadedData['eyecatch']['name'];
        // ダミーファイルの作成
        touch($this->uploadedData['eyecatch']['tmp_name']);
        // ファイル保存を実行
        $this->BcFileUploader->saveFile($this->eyecatchField, $this->uploadedData['eyecatch']);
        $this->assertFileExists($targetPath);
        // 生成されたファイルを削除
        @unlink($this->uploadedData['eyecatch']['tmp_name']);
        @unlink($targetPath);
    }

    /**
     * 保存用ファイル名を取得する($tmpIdがない場合)
     */
    public function testGetSaveFileName()
    {
        $name = 'dummy.gif';
        $targetPath = $this->savePath . $name;
        touch($targetPath);
        $result = $this->BcFileUploader->getSaveFileName($this->eyecatchField, ['name' => $name, 'ext' => 'gif']);
        $this->assertEquals('dummy_1.gif', $result);
        @unlink($targetPath);
    }

    /**
     * 画像をExif情報を元に正しい確度に回転する
     */
    public function testRotateImage()
    {
        $this->assertTrue($this->BcFileUploader->rotateImage('test.png'));
    }

    /**
     * 画像をコピーする
     *
     * @param array $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider copyImageDataProvider
     */
    public function testCopyImage($prefix, $suffix, $message = null)
    {
        $imgPath = ROOT . '/plugins/bc-admin-third/webroot/img/';
        $fileName = 'baser.power';
        $this->eyecatchField['prefix'] = $prefix;
        $this->eyecatchField['suffix'] = $suffix;
        $this->eyecatchField['width'] = 100;
        $this->eyecatchField['height'] = 100;
        $uploadedFile = [
            'eyecatch' => [
                'name' => $fileName . '_copy' . '.' . $this->eyecatchField['ext'],
                'tmp_name' => $imgPath . $fileName . '.' . $this->eyecatchField['ext'],
                'ext' => $this->eyecatchField['ext'],
            ]
        ];
        // コピー先ファイルのパス
        $targetPath = $this->savePath . $this->eyecatchField['prefix'] . $fileName . '_copy' . $this->eyecatchField['suffix'] . '.' . $this->eyecatchField['ext'];
        // コピー実行
        $this->BcFileUploader->copyImage($this->eyecatchField, $uploadedFile['eyecatch']);
        $this->assertFileExists($targetPath, $message);
        // コピーしたファイルを削除
        @unlink($targetPath);
    }

    public static function copyImageDataProvider()
    {
        return [
            ['', '', '画像ファイルをコピーできません'],
            ['pre-', '-suf', '画像ファイルの名前にプレフィックスを付けてコピーできません'],
        ];
    }

    /**
     * 画像ファイルをコピーする
     * リサイズ可能
     *
     * @param int $width 横幅
     * @param int $height 高さ
     * @param boolean $$thumb サムネイルとしてコピーするか
     * @param array $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider resizeImageDataProvider
     */
    public function testResizeImage($width, $height, $thumb, $expected, $message = null)
    {

        $imgPath = ROOT . '/plugins/bc-admin-third/webroot/img/';
        $source = $imgPath . 'baser.power.gif';
        $distination = $imgPath . 'baser.power_copy.gif';
        // コピー実行
        $this->BcFileUploader->resizeImage($source, $distination, $width, $height, $thumb);
        if (!$width && !$height) {
            $this->assertFileExists($distination, $message);
        } else {
            $result = $this->BcFileUploader->getImageSize($distination);
            $this->assertEquals($expected, $result, $message);

        }

        // コピーした画像を削除
        @unlink($distination);

    }

    public static function resizeImageDataProvider()
    {
        return [
            [false, false, false, null, '画像ファイルをコピーできません'],
            [100, 100, false, ['width' => 98, 'height' => 13], '画像ファイルを正しくリサイズしてコピーできません'],
            [100, 100, true, ['width' => 100, 'height' => 100], '画像ファイルをサムネイルとしてコピーできません'],
        ];
    }

    /**
     * 画像のサイズを取得
     *
     * @param string $imgName 画像の名前
     * @param mixed $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider getImageSizeDataProvider
     */
    public function testGetImageSize($imgName, $expected, $message = null)
    {
        $imgPath = ROOT . '/plugins/bc-admin-third/webroot/img/' . $imgName;
        $result = $this->BcFileUploader->getImageSize($imgPath);
        $this->assertEquals($expected, $result, '画像のサイズを正しく取得できません');
    }

    public static function getImageSizeDataProvider()
    {
        return [
            ['baser.power.gif', ['width' => 98, 'height' => 13], '画像のサイズを正しく取得できません'],
        ];
    }

    /**
     * ファイルを削除する
     *
     * @param string $prefix 対象のファイルの接頭辞
     * @param string $suffix 対象のファイルの接尾辞
     * @param array $imagecopy
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider deleteFileDataProvider
     */
    public function testDeleteFile($prefix, $suffix, $imagecopy, $message)
    {
        $tmpPath = TMP;
        $fileName = 'dummy';
        $ext = 'gif';
        $this->eyecatchField['name'] = $fileName;
        $this->eyecatchField['imagecopy'] = $imagecopy;
        $this->eyecatchField['prefix'] = $prefix;
        $this->eyecatchField['suffix'] = $suffix;

        $targetPath = $this->savePath . $this->eyecatchField['prefix'] . $fileName . $this->eyecatchField['suffix'] . '.' . $ext;

        // ダミーのファイルを生成
        touch($targetPath);

        // copyのダミーファイルを生成
        if (is_array($this->eyecatchField['imagecopy'])) {
            copy(ROOT . '/plugins/bc-admin-third/webroot/img/baser.power.gif', $tmpPath . $fileName . '.' . $ext);
            $bcUploadId = 1;
            $uploaded = [
                'dummy' => [
                    'name' => $fileName . '.' . $ext,
                    'tmp_name' => $tmpPath . $fileName . '.' . $ext,
                    'ext' => $ext,
                    '_bc_upload_id' => $bcUploadId,
                ],
            ];
            $this->BcFileUploader->setUploadingFiles($uploaded, $bcUploadId);
            foreach($this->eyecatchField['imagecopy'] as $copy) {
                $copy['name'] = $fileName;
                $copy['ext'] = $this->eyecatchField['ext'];
                $this->BcFileUploader->copyImage($copy, $uploaded['dummy']);
            }
        }
        // 削除を実行
        $this->BcFileUploader->deleteFile($this->eyecatchField, $fileName . '.' . $ext);
        $this->assertFileDoesNotExist($targetPath, $message);
    }

    public static function deleteFileDataProvider()
    {
        return [
            [null, null, null, 'ファイルを削除できません'],
            ['pre', null, null, '接頭辞を指定した場合のファイル削除ができません'],
            [null, 'suf', null, '接尾辞を指定した場合のファイル削除ができません'],
            ['pre', 'suf', null, '接頭辞と接尾辞を指定した場合のファイル削除ができません'],
            [null, null, [
                'thumb' => ['suffix' => 'thumb', 'width' => '150', 'height' => '150']
            ], 'ファイルを複数削除できません'],
            [null, null, [
                'thumb' => ['suffix' => 'thumb', 'width' => '150', 'height' => '150'],
                'thumb_mobile' => ['suffix' => 'thumb_mobile', 'width' => '100', 'height' => '100'],
            ], 'ファイルを複数削除できません'],
        ];
    }

    /**
     * ファイル名をフィールド値ベースのファイル名に変更する
     */
    public function testRenameToBasenameField()
    {
        $this->loadFixtureScenario(ContentsScenario::class);
        $this->getRequest('/baser/admin/');
        touch($this->savePath . 'test.png');
        $entity = new Entity();
        $entity->id = 1;
        $setting = $this->BcFileUploader->settings['fields']['eyecatch'];
        $newFileName = $this->BcFileUploader->renameToBasenameField($setting, $this->uploadedData['eyecatch'], $entity, false);
        $this->assertEquals("00000001_eyecatch.png", $newFileName);
        $this->assertFileExists($this->savePath . DS . $newFileName);
        @unlink($this->savePath . 'test.png');
        @unlink($this->savePath . DS . $newFileName);
    }
    /**
     * ファイル名をフィールド値ベースのファイル名に変更する
     * testRenameToBasenameFields
     * @param string $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider renameToFieldBasenameDataProvider
     */
    public function testRenameToFieldBasename($oldName, $ext, $copy, $imagecopy, $message = null)
    {
        $this->loadFixtureScenario(ContentsScenario::class);
        $this->getRequest('/baser/admin/');
        // 初期化
        $entity = $this->table->get(1);
        $oldName = $oldName . '.' . $ext;
        $entity->eyecatch = $oldName;
        $entity->_bc_upload_id = 1;
        $this->BcFileUploader->setUploadingFiles(['eyecatch' => ['name' => $oldName, 'ext' => $ext]], $entity->_bc_upload_id);
        $setting = $this->BcFileUploader->settings['fields']['eyecatch'];

        if ($imagecopy) {
            $this->BcFileUploader->settings['fields']['eyecatch']['imagecopy'] = $imagecopy;
        }

        // パス情報
        $oldPath = $this->savePath . $oldName;
        $newPath = $this->savePath . "00000001_eyecatch" . '.' . $ext;

        // ダミーファイルの生成
        touch($oldPath);

        if ($imagecopy) {
            foreach($imagecopy as $copysetting) {
                $oldCopynames = $this->BcFileUploader->getFileName($copysetting, $oldName);
                touch($this->savePath . $oldCopynames);
            }
        }


        // テスト実行
        $this->BcFileUploader->renameToBasenameFields($entity, $copy);
        $this->assertFileExists($newPath, $message);


        // 生成されたファイルを削除
        @unlink($newPath);


        // ファイルを複数生成する場合テスト
        if ($copy) {
            $this->assertFileExists($oldPath, $message);
            @unlink($oldPath);
        }

        if ($imagecopy) {
            $newName = $this->BcFileUploader->getFileName($setting['imageresize'], "00000001_eyecatch" . '.' . $ext);

            foreach($imagecopy as $copysetting) {
                $newCopyname = $this->BcFileUploader->getFileName($copysetting, $newName);
                $this->assertFileExists($this->savePath . $newCopyname, $message);
                @unlink($this->savePath . $newCopyname);
            }
        }

    }

    public static function renameToFieldBasenameDataProvider()
    {
        return [
            ['oldName', 'gif', false, false, 'ファイル名をフィールド値ベースのファイル名に変更できません'],
            ['oldName', 'gif', true, false, 'ファイル名をフィールド値ベースのファイル名に変更してコピーができません'],
            ['oldName', 'gif', false, [
                ['prefix' => 'pre-', 'suffix' => '-suf'],
                ['prefix' => 'pre2-', 'suffix' => '-suf2'],
            ], '複数のファイルをフィールド値ベースのファイル名に変更できません'],
        ];
    }

    /**
     * 全フィールドのファイル名をフィールド値ベースのファイル名に変更する
     */
    public function testRenameToBasenameFields()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * フィールドベースのファイル名を取得する
     *
     * @param string $namefield namefieldパラメータの値
     * @param string $basename basenameパラメータの値
     * @param string $basename $Model->idの値
     * @param array $setting 設定する値
     * @param string $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider getFieldBasenameDataProvider
     */
    public function testGetFieldBasename($namefield, $basename, $id, $setting, $expected, $message = null)
    {
        // 初期化
        $entity = new Entity();
        if ($namefield) {
            $entity->{$namefield} = $basename;
        }
        $entity->id = $id;

        $issetSubdirDataFormat = isset($setting['subdirDateFormat']);
        if ($issetSubdirDataFormat) {
            $this->BcFileUploader->settings = [];
            $this->BcFileUploader->settings['subdirDateFormat'] = $setting['subdirDateFormat'];
        }

        $setting['namefield'] = $namefield;


        // テスト実行
        $result = $this->BcFileUploader->getFieldBasename($setting, ['ext' => 'ext'], $entity);


        if (!$issetSubdirDataFormat) {
            $this->assertEquals($expected, $result, $message);

        } else {
            $subDir = date($setting['subdirDateFormat']) . '/';

            $expected = $subDir . $expected;

            $this->assertEquals($expected, $result, $message);

            @$this->removeDir($this->savePath . $subDir);
        }

    }

    public static function getFieldBasenameDataProvider()
    {
        return [
            ['namefield', 'basename', 'modelId', ['name' => 'name'],
                'basename_name.ext', 'フィールドベースのファイル名を正しく取得できません'],
            [null, 'basename', 'modelId', [],
                false, 'namefieldを指定しなかった場合にfalseが返ってきません'],
            ['id', null, 'modelId', ['name' => 'name'],
                'modelId_name.ext', 'namefieldがidかつbasenameが指定されていない場合のファイル名を正しく取得できません'],
            ['id', null, null, [],
                false, 'namefieldがidかつbasenameとModelIdが指定されていない場合にfalseが返ってきません'],
            ['namefield', null, 'modelId', [],
                false, 'basenameが指定されていない場合にfalseが返ってきません'],
            ['namefield', 'basename', 'modelId', ['name' => 'name', 'nameformat' => 'ho-%s-ge'],
                'ho-basename-ge_name.ext', 'formatを指定した場合に正しくファイル名を取得できません'],
            ['namefield', 'basename', 'modelId', ['name' => 'name', 'nameadd' => false],
                'basename.ext', 'formatを指定した場合に正しくファイル名を取得できません'],
            ['namefield', 'basename', 'modelId', ['name' => 'name', 'subdirDateFormat' => 'Y-m'],
                'basename_name.ext', 'formatを指定した場合に正しくファイル名を取得できません'],
        ];
    }


    /**
     * ベースファイル名からプレフィックス付のファイル名を取得する
     *
     * @param string $prefix 対象のファイルの接頭辞
     * @param string $suffix 対象のファイルの接尾辞
     * @param string $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider getFileNameDataProvider
     */
    public function testGetFileName($prefix, $suffix, $expected, $message = null)
    {
        $setting = [
            'prefix' => $prefix,
            'suffix' => $suffix,
        ];
        $fileName = 'hoge.gif';

        $result = $this->BcFileUploader->getFileName($setting, $fileName);
        $this->assertEquals($expected, $result, $message);
    }

    public static function getFileNameDataProvider()
    {
        return [
            [null, null, 'hoge.gif', 'ベースファイル名からファイル名を取得できません'],
            ['pre-', null, 'pre-hoge.gif', 'ベースファイル名から接頭辞付きファイル名を取得できません'],
            [null, '-suf', 'hoge-suf.gif', 'ベースファイル名から接尾辞付きファイル名を取得できません'],
            ['pre-', '-suf', 'pre-hoge-suf.gif', 'ベースファイル名からプレフィックス付のファイル名を取得できません'],
        ];
    }

    /**
     * ファイル名からベースファイル名を取得する
     *
     * @param string $prefix 対象のファイルの接頭辞
     * @param string $suffix 対象のファイルの接尾辞
     * @param string $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider getBasenameDataProvider
     */
    public function testGetBasename($prefix, $suffix, $expected, $message = null)
    {
        $setting = [
            'prefix' => $prefix,
            'suffix' => $suffix,
        ];
        $fileName = 'pre-hoge-suf.gif';

        $result = $this->BcFileUploader->getBasename($setting, $fileName);
        $this->assertEquals($expected, $result, $message);
    }

    public static function getBasenameDataProvider()
    {
        return [
            [null, null, 'pre-hoge-suf', 'ファイル名からベースファイル名を正しく取得できません'],
            ['pre-', null, 'hoge-suf', 'ファイル名からベースファイル名を正しく取得できません'],
            [null, '-suf', 'pre-hoge', 'ファイル名からベースファイル名を正しく取得できません'],
            ['pre-', '-suf', 'hoge', 'ファイル名からベースファイル名を正しく取得できません'],
        ];
    }

    /**
     * 一意のファイル名を取得する
     *
     * @param string $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider getUniqueFileNameDataProvider
     */
    public function testGetUniqueFileName($fieldName, $fileName, $expected, $message = null)
    {
        $this->loadFixtureScenario(ContentsScenario::class);
        // eyecatchでtemplate1.gifをすでに持つデータとして更新し、テスト
        // BcUpload-beforeSaveを回避するため新規データ挿入時にremoveBehavior('BcUpload')を実行
        if ($fileName === 'template1.gif') {
            $table = $this->table->removeBehavior('BcUpload');
            $content = $table->find()->all()->last();
            $this->ContentsService->update($content, ['eyecatch' => 'template1.gif']);
        }
        $file = ['name' => $fileName, 'ext' => 'gif'];
        $setting = ['name' => $fieldName];
        touch($this->savePath . 'template1.gif');
        $result = $this->BcFileUploader->getUniqueFileName($setting, $file, new Content());
        $this->assertEquals($expected, $result, $message);
        @unlink($this->savePath . 'template1.gif');
    }

    public static function getUniqueFileNameDataProvider()
    {
        return [
            ['eyecatch', 'hoge.gif', 'hoge.gif', '一意のファイル名を正しく取得できません'],
            ['eyecatch', 'template.gif', 'template.gif', '一意のファイル名を正しく取得できません'],
            ['eyecatch', 'template1.gif', 'template1__2.gif', '一意のファイル名を正しく取得できません'],
        ];
    }

    /**
     * testIsFileExists
     * 重複ファイルがあるか確認する
     * @return void
     */
    public function testIsFileExists()
    {
        $fileName = 'test.txt';
        $this->assertFalse($this->BcFileUploader->isFileExists($fileName));
        $basePath = WWW_ROOT . 'files' . DS;
        $duplicate = "test/";
        // existsCheckDirsがある場合
        try {
            mkdir($basePath . $duplicate, 0777, false);
            touch($basePath . $duplicate . $fileName);
            $this->BcFileUploader->existsCheckDirs = [$duplicate];
            $this->assertTrue($this->BcFileUploader->isFileExists($fileName));
        } catch (\Exception $e) {
            $error = $e;
        } finally {
            if (file_exists($basePath . $duplicate . $fileName)) {
                unlink($basePath . $duplicate . $fileName);
                rmdir($basePath . $duplicate);
            }
            $this->BcFileUploader->existsCheckDirs = [];
        }
        // SavePathがある場合
        try {
            touch(WWW_ROOT . 'files/contents/' . $fileName);
            $this->savePath = WWW_ROOT . 'files/contents/';
            $this->assertTrue($this->BcFileUploader->isFileExists($fileName));
        } catch (\Exception $e) {
            $error = $e;
        } finally {
            if (file_exists(WWW_ROOT . 'files/contents/' . $fileName)) {
                unlink(WWW_ROOT . 'files/contents/' . $fileName);
            }
            $reflection = new ReflectionClass($this->BcFileUploader);
            $property = $reflection->getProperty('savePath');
            $property->setAccessible(true);
            $property->setValue($this->BcFileUploader, []);
        }
    }

    /**
     * 既に存在するデータのファイルを削除する
     */
    public function testDeleteExistingFiles()
    {
        $this->loadFixtureScenario(ContentsScenario::class);
        $fileName = '00000006_eyecatch';
        $targetPath = $this->savePath . $fileName . '.' . $this->eyecatchField['ext'];
        // ダミーのファイルを生成
        touch($targetPath);
        // アップロードされていなければ、returnで終了
        $this->BcFileUploader->setUploadingFiles([], 1);
        $this->BcFileUploader->deleteExistingFiles($this->ContentsService->get(6));
        $this->assertFileExists($targetPath);
        // アップロードされていれば削除処理
        $uploaded = [
            'name' => $fileName . '.' . $this->eyecatchField['ext'],
            'tmp_name' => TMP . $fileName . '.' . $this->eyecatchField['ext'],
            'uploadable' => true
        ];
        $bcUploadId = 1;
        $this->BcFileUploader->setUploadingFiles(['eyecatch' => $uploaded], $bcUploadId);
        $this->BcFileUploader->settings['fields']['eyecatch'] = $this->eyecatchField;
        $content = $this->ContentsService->get(6);
        $content->_bc_upload_id = $bcUploadId;
        $this->BcFileUploader->deleteExistingFiles($content);
        $this->assertFileDoesNotExist($targetPath);
        @unlink($targetPath);
    }

    /**
     * test deleteExistingFile
     */
    public function testDeleteExistingFile()
    {
        $this->loadFixtureScenario(ContentsScenario::class);
        $fileName = '00000006_eyecatch';
        $targetPath = $this->savePath . $fileName . '.' . $this->eyecatchField['ext'];
        touch($targetPath);
        $uploaded = [
            'name' => $fileName . '.' . $this->eyecatchField['ext'],
            'tmp_name' => TMP . $fileName . '.' . $this->eyecatchField['ext'],
        ];
        $entity = $this->ContentsService->get(6);
        $this->BcFileUploader->deleteExistingFile('eyecatch', $uploaded, $entity);
        $this->assertFileDoesNotExist($targetPath);
        touch($targetPath);
        $this->BcFileUploader->deleteExistingFile('eyecatch', [], $entity);
        $this->assertFileExists($targetPath);
        unlink($targetPath);
    }

    /**
     * 画像をコピーする
     * @param array $size 画像サイズ
     * @param bool $copied 画像がコピーされるかどうか
     * @return void
     * @dataProvider copyImagesDataProvider
     */
    public function testCopyImages($size): void
    {
        $this->eyecatchField['imagecopy'] = ['thumb' => $size];
        $this->savePath = ROOT . '/plugins/bc-admin-third/webroot/img/';
        $this->BcFileUploader->savePath = $this->savePath;
        $fileName = 'baser.power';
        $uploadedFile = [
            'eyecatch' => [
                'name' => $fileName . '.' . $this->eyecatchField['ext'],
                'tmp_name' => $this->savePath . $fileName . '.' . $this->eyecatchField['ext'],
                'ext' => $this->eyecatchField['ext']
            ]
        ];
        // コピー先ファイルのパス
        $targetPath = $this->savePath . $fileName . '_copy' . '.' . $this->eyecatchField['ext'];
        // コピー実行
        $this->BcFileUploader->copyImages($this->eyecatchField, $uploadedFile['eyecatch']);
        $this->assertFileExists($targetPath);
        // コピーしたファイルを削除
        @unlink($targetPath);
    }

    public static function copyImagesDataProvider()
    {
        return [
            // コピー画像が元画像より大きい場合はスキップして作成しない
            [['width' => 300, 'height' => 300, 'suffix' => '_copy']],
            // コピーが生成される場合
            [['width' => 20, 'height' => 20, 'suffix' => '_copy']],
        ];
    }

    /**
     * testSetAndGetUploadedFile
     *
     * @return void
     */
    public function testSetAndGetUploadedFile()
    {
        $this->BcFileUploader->setUploadingFiles($this->uploadedData, 1);
        $this->assertEquals($this->uploadedData, $this->BcFileUploader->getUploadingFiles(1));
    }

    /**
     * test isUploaded
     */
    public function testIsUploadable()
    {
        $this->assertFalse($this->BcFileUploader->isUploadable('image', 'image/jpg', ['name' => '', 'tmp_name' => '']));
        $this->assertTrue($this->BcFileUploader->isUploadable('image', 'image/jpg', ['name' => 'test.jpg', 'tmp_name' => 'test.jpg', 'error' => 0]));
        $this->assertTrue($this->BcFileUploader->isUploadable('all', 'image/jpg', ['name' => 'test.jpg', 'tmp_name' => 'test.jpg', 'error' => 0]));
        $this->assertFalse($this->BcFileUploader->isUploadable('zip', '', ['name' => 'test.jpg', 'tmp_name' => 'test.jpg', 'error' => 0]));
        $this->assertTrue($this->BcFileUploader->isUploadable(['zip', 'jpg'], '', ['name' => 'test.jpg', 'tmp_name' => 'test.jpg', 'error' => 0]));
    }

    /**
     * test isUploaded
     */
    public function testIsUploadedAndReset()
    {
        $this->assertFalse($this->BcFileUploader->isUploaded());
        touch($this->uploadedData['eyecatch']['tmp_name']);
        $this->BcFileUploader->saveFileWhileChecking($this->eyecatchField, $this->uploadedData['eyecatch'], new Content());
        $this->assertTrue($this->BcFileUploader->isUploaded());
        $this->BcFileUploader->resetUploaded();
        $this->assertFalse($this->BcFileUploader->isUploaded());
    }
}
