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

namespace BcThemeFile\Test\TestCase\Service;

use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Error\BcException;
use BaserCore\Error\BcFormFailedException;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcFile;
use BaserCore\Utility\BcFolder;
use BcThemeFile\Service\ThemeFilesService;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Laminas\Diactoros\UploadedFile;

/**
 * ThemeFilesServiceTest
 */
class ThemeFilesServiceTest extends BcTestCase
{

    public $ThemeFileService = null;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ThemeFileService = new ThemeFilesService();
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test __construct
     */
    public function test__construct()
    {
        $this->assertTrue(isset($this->ThemeFileService->ThemeFileForm));
    }

    /**
     * test getNew
     */
    public function test_getNew()
    {
        $file = '/var/www/html/plugins/BcThemeSample/templates/layout/';
        //テスト対象メソッドをコール
        $rs = $this->ThemeFileService->getNew($file, 'layout');
        //戻る値を確認
        $this->assertEquals($rs['fullpath'], $file);
        $this->assertEquals($rs['parent'], $file);
        $this->assertEquals($rs['name'], 'layout');
        $this->assertEquals($rs['base_name'], '');
        $this->assertEquals($rs['ext'], 'php');
        $this->assertEquals($rs['type'], 'file');
        $this->assertEquals($rs['path'], null);
        $this->assertEquals($rs['contents'], '');
    }

    /**
     * test get
     */
    public function test_get()
    {
        $filePath = '/var/www/html/plugins/BcThemeSample/templates/layout/default.php';

        //テスト対象メソッドをコール
        $rs = $this->ThemeFileService->get($filePath);

        //戻る値を確認
        $this->assertEquals($filePath, $rs['fullpath']);
        $this->assertEquals('/var/www/html/plugins/BcThemeSample/templates/layout/', $rs['parent']);
        $this->assertEquals('default.php', $rs['name']);
        $this->assertEquals('default', $rs['base_name']);
        $this->assertEquals('php', $rs['ext']);
        $this->assertEquals('text', $rs['type']);
        $this->assertEquals(null, $rs['path']);
    }

    /**
     * test getForm
     */
    public function test_getForm()
    {
        //準備
        $filePath = '/var/www/html/plugins/BcThemeSample/templates/layout/default.php';
        $data = $this->ThemeFileService->get($filePath);
        //テスト対象メソッドをコール
        $rs = $this->ThemeFileService->getForm($data->toArray());
        //戻る値を確認
        $this->assertEquals(
            '/var/www/html/plugins/BcThemeSample/templates/layout/default.php',
            $rs->getData('fullpath')
        );
    }

    /**
     * test create
     */
    public function test_create()
    {
        //ポストデータを生成
        $fullpath = BASER_PLUGINS . 'BcThemeSample' . '/templates/layout/';
        $postData = [
            'fullpath' => $fullpath,
            'parent' => $fullpath,
            'base_name' => 'test',
            'ext' => 'php',
            'contents' => "<?php echo 'test' ?>"
        ];
        //正常系テスト
        $rs = $this->ThemeFileService->create($postData);
        //戻る値を確認
        $this->assertEquals($rs->getData('fullpath'), $fullpath . 'test.php');
        //実際にファイルが作成されいてるか確認すること
        $this->assertTrue(file_exists($fullpath . 'test.php'));
        //fileの中身を確認する事
        $this->assertEquals(file_get_contents($fullpath . 'test.php'), "<?php echo 'test' ?>");

        //作成されたファイルを削除
        unlink($fullpath . 'test.php');

        //異常系テスト・ファイル名を入力しない
        $postData['base_name'] = '';
        $this->expectException(BcFormFailedException::class);
        $this->expectExceptionMessage('ファイルの作成に失敗しました。');
        $this->ThemeFileService->create($postData);
    }

    /**
     * test create - テーマディレクトリ外への書き込みを拒否する（GHSA-2pj4-v76f-wjvx）
     *
     * Admin web 経路は POST の fullpath を直接書き込み先に使い getFullpath() を通らないため、
     * sink(create) で必ずテーマディレクトリ配下を検証し、外部パスは BcException で拒否すること。
     *
     * @dataProvider provideOutsideThemeDir
     */
    public function test_create_rejectsOutsideThemeDir(string $fullpath): void
    {
        $this->expectException('BaserCore\Error\BcException');
        $this->expectExceptionMessage('パスにテーマディレクトリ外への参照が含まれています。');
        $this->ThemeFileService->create([
            'fullpath' => $fullpath,
            'parent' => $fullpath,
            'base_name' => 'pwned',
            'ext' => 'php',
            'contents' => "<?php /* poc */ ?>",
        ]);
        // 書き込まれていないことを保証（万一通った場合の後始末も兼ねる）
        $this->assertFileDoesNotExist($fullpath . 'pwned.php');
    }

    /**
     * test delete - テーマディレクトリ外のファイル削除を拒否する（GHSA-2pj4-v76f-wjvx）
     * @dataProvider provideOutsideThemeDir
     */
    public function test_delete_rejectsOutsideThemeDir(string $fullpath): void
    {
        $this->expectException('BaserCore\Error\BcException');
        $this->ThemeFileService->delete($fullpath . 'dummy.php');
    }

    public static function provideOutsideThemeDir(): array
    {
        return [
            'tmp直下' => ['/tmp/'],
            'アプリwebroot' => ['/var/www/html/webroot/'],
            'config' => ['/var/www/html/config/'],
        ];
    }

    /**
     * test update - URL エンコードした ../ でテーマディレクトリ外を更新できない（GHSA-q7g3-qwrc-mp89）
     */
    public function test_update_rejectsEncodedTraversal(): void
    {
        $victim = TMP . 'q7g3_victim.php';
        (new BcFile($victim))->write('original');
        $parent = BASER_PLUGINS . 'BcThemeSample' . '/templates/layout/';
        try {
            $this->ThemeFileService->update([
                'fullpath' => $parent . '%2e%2e%2f%2e%2e%2f%2e%2e%2f%2e%2e%2ftmp%2fq7g3_victim.php',
                'parent' => $parent,
                'base_name' => 'q7g3_victim',
                'ext' => 'php',
                'contents' => 'changed',
            ]);
            $this->fail('例外が発生しませんでした。');
        } catch (BcException $e) {
            $this->assertEquals('パスにテーマディレクトリ外への参照が含まれています。', $e->getMessage());
        } finally {
            $contents = file_get_contents($victim);
            unlink($victim);
        }
        $this->assertEquals('original', $contents);
    }

    /**
     * test update - 名前変更先（base_name）でテーマディレクトリ外へ出られない（GHSA-q7g3-qwrc-mp89）
     */
    public function test_update_rejectsTraversalInBaseName(): void
    {
        $parent = BASER_PLUGINS . 'BcThemeSample' . '/templates/layout/';
        (new BcFile($parent . 'q7g3_rename.php'))->create();
        try {
            $this->ThemeFileService->update([
                'fullpath' => $parent . 'q7g3_rename.php',
                'parent' => $parent,
                'base_name' => '%2e%2e%2f%2e%2e%2f%2e%2e%2f%2e%2e%2ftmp%2fq7g3_renamed',
                'ext' => 'php',
                'contents' => 'changed',
            ]);
            $this->fail('例外が発生しませんでした。');
        } catch (\Exception $e) {
            // assertWithinThemeDir（BcException）かフォーム検証（BcFormFailedException）のどちらかで止まればよい
            $this->assertTrue($e instanceof BcException || $e instanceof BcFormFailedException);
        } finally {
            if (is_file($parent . 'q7g3_rename.php')) unlink($parent . 'q7g3_rename.php');
        }
        $this->assertFileDoesNotExist(TMP . 'q7g3_renamed.php');
    }

    /**
     * test copy - URL エンコードした ../ でテーマディレクトリ外のファイルを複製できない（GHSA-q7g3-qwrc-mp89）
     */
    public function test_copy_rejectsEncodedTraversal(): void
    {
        $this->expectException(BcException::class);
        $this->expectExceptionMessage('パスにテーマディレクトリ外への参照が含まれています。');
        $this->ThemeFileService->copy(
            BASER_PLUGINS . 'BcThemeSample' . '/templates/layout/' . '%2e%2e%2f%2e%2e%2f%2e%2e%2f%2e%2e%2fconfig%2fapp_local.php'
        );
    }

    /**
     * test update
     */
    public function test_update()
    {
        //POSTデータを生成
        $fullpath = BASER_PLUGINS . 'BcThemeSample' . '/templates/layout/';
        (new BcFile($fullpath . 'test.php'))->create();
        $data = [
            'fullpath' => $fullpath . 'test.php',
            'parent' => $fullpath,
            'base_name' => 'test_update',
            'ext' => 'php',
            'contents' => "<?php echo 'test' ?>"
        ];
        //正常系テスト
        $rs = $this->ThemeFileService->update($data);
        //戻る値を確認
        $this->assertEquals($rs->getData('fullpath'), $fullpath . 'test_update.php');
        //実際にファイルが変更されいてるか確認すること
        $this->assertTrue(file_exists($fullpath . 'test_update.php'));
        //ファイルの中身を確認
        $this->assertEquals(file_get_contents($fullpath . 'test_update.php'), "<?php echo 'test' ?>");
        //変更した前にファイル名が存在しないか確認すること
        $this->assertFalse(file_exists($fullpath . 'test.php'));
        //作成されたファイルを削除
        unlink($fullpath . 'test_update.php');

        //異常系テスト・ファイル名を入力しない（パスは正常なテーマ配下のままフォーム検証で失敗させる）
        $data['base_name'] = '';
        $this->expectException(BcFormFailedException::class);
        $this->expectExceptionMessage('ファイルの保存に失敗しました。');
        $this->ThemeFileService->update($data);
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        //テストファイルを作成
        $fullpath = BASER_PLUGINS . 'BcThemeSample' . '/templates/layout/base_name_1.php';
        (new BcFile($fullpath))->create();
        $rs = $this->ThemeFileService->delete($fullpath);
        //戻る値を確認
        $this->assertTrue($rs);
        //実際にファイルが削除されいてるか確認すること
        $this->assertFalse(file_exists($fullpath . 'base_name_1.php'));

        //存在しないファイルを削除した場合、
        $rs = $this->ThemeFileService->delete($fullpath);
        //戻る値を確認
        $this->assertFalse($rs);
    }

    /**
     * test copy
     */
    public function test_copy()
    {
        //テストファイルを作成
        $fullpath = BASER_PLUGINS . 'BcThemeSample' . '/templates/layout/';
        (new BcFile($fullpath . 'base_name_1.php', true))->create();
        //サービスメソッドをコール
        $rs = $this->ThemeFileService->copy($fullpath . 'base_name_1.php');
        //戻る値を確認
        $this->assertEquals($rs['base_name'], 'base_name_1_copy');
        $this->assertEquals($rs['fullpath'], $fullpath . 'base_name_1_copy.php');
        //実際にファイルが作成されいてるか確認すること
        $this->assertTrue(file_exists($fullpath . 'base_name_1_copy.php'));
        //生成されたテストファイルを削除
        unlink($fullpath . 'base_name_1.php');
        unlink($fullpath . 'base_name_1_copy.php');
    }

    /**
     * test upload
     */
    public function test_upload()
    {
        //テストテーマフォルダを作成
        $fullpath = BASER_PLUGINS . 'BcThemeSample' . '/templates/layout/';
        (new BcFolder($fullpath . 'new_folder'))->create();

        //テストファイルを作成
        $filePath = TMP . 'test_upload' . DS;
        (new BcFolder($filePath))->create();
        $testFile = $filePath . 'uploadTestFile.html';
        (new BcFile($testFile))->create();

        //Postデータを生成
        $files = [
            'file' => new UploadedFile(
                $testFile,
                10,
                UPLOAD_ERR_OK,
                'uploadTestFile.html',
                "html",
            )
        ];
        //テスト対象メソッドをコール
        $this->ThemeFileService->upload($fullpath . 'new_folder', $files);
        //実際にファイルが存在するか確認すること
        $this->assertTrue(file_exists($fullpath . 'new_folder/uploadTestFile.html'));

        //テストファイルとフォルダを削除
        rmdir($filePath);
        unlink($fullpath . 'new_folder/uploadTestFile.html');
        rmdir($fullpath . 'new_folder');
    }

    /**
     * test copyToTheme
     */
    public function test_copyToTheme()
    {
        //データを生成
        $this->getRequest()->getAttribute('currentSite');
        SiteFactory::make(['id' => 1, 'status' => true, 'theme' => 'BcColumn'])->persist();
        //パラメーターを作成
        $param = [
            'plugin' => 'BaserCore',
            'theme' => 'BcFront',
            'type' => 'css',
            'path' => 'bge_style.css',
            'fullpath' => '/var/www/html/plugins/bc-front/webroot/css/bge_style.css',
            'assets' => true
        ];
        //対象メソッドをコール
        $rs = $this->ThemeFileService->copyToTheme($param);
        //戻る値を確認
        $this->assertEquals($rs, '/plugins/BcColumn/webroot/css/bge_style.css');
        $copiedFilePath = '/var/www/html/plugins/BcColumn/webroot/css/bge_style.css';
        //実際にファイルが作成されいてるか確認すること
        $this->assertTrue(file_exists($copiedFilePath));
        //ファイルの中身を確認
        $this->assertTextContains('.cke_editable {
  padding: 15px;
}', file_get_contents($copiedFilePath));
        //作成されたファイルを削除
        unlink($copiedFilePath);
    }

    /**
     * test getImg
     */
    public function test_getImg()
    {
        $data = [
            'theme' => 'BcFront',
            'type' => 'img',
            'path' => 'logo.png',
            'fullpath' => '/var/www/html/plugins/bc-front/webroot/img/logo.png'
        ];

        $img = $this->ThemeFileService->getImg($data);
        $this->assertNotNull($img);
    }

    /**
     * test getImgThumb
     */
    public function test_getImgThumb()
    {
        $data = [
            'theme' => 'BcFront',
            'type' => 'img',
            'path' => 'logo.png',
            'fullpath' => '/var/www/html/plugins/bc-front/webroot/img/logo.png'
        ];

        $imgThumb = $this->ThemeFileService->getImgThumb($data, 100, 100);
        //戻る値を確認
        $this->assertNotNull($imgThumb['imgThumb']);
        $this->assertEquals('png', $imgThumb['extension']);
    }
}
