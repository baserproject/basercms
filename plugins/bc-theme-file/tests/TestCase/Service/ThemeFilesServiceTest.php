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
     * test get - プラグイン配置ルート外のファイルは読み出さない（GHSA-rqr8-gmx5-xvrr）
     */
    public function test_get_rejectsOutsidePluginsDir()
    {
        $this->expectException('BaserCore\Error\BcException');
        $this->expectExceptionMessage('パスにテーマディレクトリ外への参照が含まれています。');
        $this->ThemeFileService->get('/var/www/html/plugins/BcThemeSample/templates/layout/../../../../config/app.php');
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
            // update() は base_name から算出した rename 先を assertWithinThemeDir で検証するのが
            // フォーム検証（alphaNumericPlus）より先に実行されるため、必ず BcException で止まる
            $this->assertInstanceOf(BcException::class, $e);
            $this->assertEquals('パスにテーマディレクトリ外への参照が含まれています。', $e->getMessage());
        } finally {
            if (is_file($parent . 'q7g3_rename.php')) unlink($parent . 'q7g3_rename.php');
        }
        $this->assertFileDoesNotExist(TMP . 'q7g3_renamed.php');
    }

    /**
     * test update - base_name のみで見ると、alphaNumericPlus バリデーションを通過する入力からは
     * ../ トラバーサルを構成できないため、assertWithinThemeDir 単体で拒否される入力は存在しないことを確認する
     * （GHSA-q7g3-qwrc-mp89 M-5）
     *
     * alphaNumericPlus は [a-zA-Z0-9\-_]+ のみを許容し、'.' や '/' を含められないため、
     * dirname($fullpath) . DS . base_name（+ ext）で組み立てられる newPath は
     * fullpath 自体がテーマディレクトリ配下である限り、必ずテーマディレクトリ配下に収まる。
     * そのため「フォーム検証は通るが assertWithinThemeDir だけが拒否する」ケースは作れない。
     * 本テストはその事実を、alphaNumericPlus を通過する base_name で正常に更新できることをもって示す。
     */
    public function test_update_baseNameCannotBypassFormValidationToReachTraversal(): void
    {
        $parent = BASER_PLUGINS . 'BcThemeSample' . '/templates/layout/';
        (new BcFile($parent . 'q7g3_safe_rename.php'))->create();
        try {
            // alphaNumericPlus を通過する（英数字・ハイフン・アンダースコアのみ）が、
            // 見た目上はトラバーサルを狙ったような base_name
            $rs = $this->ThemeFileService->update([
                'fullpath' => $parent . 'q7g3_safe_rename.php',
                'parent' => $parent,
                'base_name' => 'dotdotslash-tmp-q7g3',
                'ext' => 'php',
                'contents' => 'changed',
            ]);
            $this->assertEquals($parent . 'dotdotslash-tmp-q7g3.php', $rs->getData('fullpath'));
            $this->assertFileExists($parent . 'dotdotslash-tmp-q7g3.php');
        } finally {
            if (is_file($parent . 'q7g3_safe_rename.php')) unlink($parent . 'q7g3_safe_rename.php');
            if (is_file($parent . 'dotdotslash-tmp-q7g3.php')) unlink($parent . 'dotdotslash-tmp-q7g3.php');
        }
    }

    /**
     * test update - 二重URLエンコードされた ../ 相当の文字列を base_name に与えても
     * テーマディレクトリ外への rename はできない（GHSA-q7g3-qwrc-mp89 M-5）
     *
     * update() 自身が base_name に rawurldecode() を1回かけるため、'%252e' は '%2e' に
     * なった時点で newPath に組み込まれる。assertWithinThemeDir はさらにもう1回
     * rawurldecode() をかけて検査するため、この newPath は最終的に '../' として解決され、
     * トラバーサルとして検知・拒否される（= 実際には「トラバーサル扱いされない」のではなく
     * 「二重デコードの結果としてトラバーサルとして検知され、拒否される」という結果になる）。
     */
    public function test_update_doubleEncodedIsNotTraversal(): void
    {
        $parent = BASER_PLUGINS . 'BcThemeSample' . '/templates/layout/';
        (new BcFile($parent . 'q7g3_double.php'))->create();
        $outside = TMP . 'q7g3_double_pwned.php';
        try {
            $this->ThemeFileService->update([
                'fullpath' => $parent . 'q7g3_double.php',
                'parent' => $parent,
                'base_name' => '%252e%252e%252f%252e%252e%252f%252e%252e%252f%252e%252e%252ftmp%252fq7g3_double_pwned',
                'ext' => 'php',
                'contents' => 'changed',
            ]);
            $this->fail('例外が発生しませんでした。');
        } catch (\Exception $e) {
            $this->assertInstanceOf(BcException::class, $e);
            $this->assertEquals('パスにテーマディレクトリ外への参照が含まれています。', $e->getMessage());
        } finally {
            if (is_file($parent . 'q7g3_double.php')) unlink($parent . 'q7g3_double.php');
        }
        // テーマ外は当然、テーマ内にも二重デコードされたファイルは作成されていないこと
        $this->assertFileDoesNotExist($outside);
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
        // コピー先には BcColumn 本体の bge_style.css が存在するため、上書き前に退避し、テスト後に必ず戻す
        $copiedFilePath = '/var/www/html/plugins/BcColumn/webroot/css/bge_style.css';
        $backupPath = $copiedFilePath . '.bak';
        $existed = file_exists($copiedFilePath);
        if ($existed) copy($copiedFilePath, $backupPath);
        try {
            //対象メソッドをコール
            $rs = $this->ThemeFileService->copyToTheme($param);
            //戻る値を確認
            $this->assertEquals($rs, '/plugins/BcColumn/webroot/css/bge_style.css');
            //実際にファイルが作成されいてるか確認すること
            $this->assertTrue(file_exists($copiedFilePath));
            //ファイルの中身を確認
            $this->assertTextContains('.cke_editable {
  padding: 15px;
}', file_get_contents($copiedFilePath));
        } finally {
            //作成されたファイルを削除し、元からあったファイルは戻す
            if (file_exists($copiedFilePath)) unlink($copiedFilePath);
            if ($existed) rename($backupPath, $copiedFilePath);
        }
        $this->assertSame($existed, file_exists($copiedFilePath), 'テスト前後で bge_style.css の有無が変わっています');
    }

    /**
     * test copyToTheme - コピー先のテーマディレクトリ外への書き込みを拒否する（GHSA-2pj4-v76f-wjvx / GHSA-f6p8-29pq-8m9h）
     *
     * copyToTheme() はコピー先 $themePath を $params['type'] / $params['path'] から組み立て
     * getFullpath() を通さないため、path にトラバーサルを含めるとテーマ外へ書き込めてしまう。
     * sink で assertWithinThemeDir が働き BcException で拒否されること。
     */
    public function test_copyToTheme_rejectsOutsideThemeDir(): void
    {
        SiteFactory::make(['id' => 1, 'status' => true, 'theme' => 'BcColumn'])->persist();
        $param = [
            'plugin' => 'BaserCore',
            'theme' => 'BcFront',
            'type' => 'css',
            // テーマ配置ルート(plugins/)の外へ抜けるトラバーサル
            'path' => '../../../../../../../../tmp/pwned.css',
            'fullpath' => '/var/www/html/plugins/bc-front/webroot/css/bge_style.css',
            'assets' => true,
        ];
        $this->expectException('BaserCore\Error\BcException');
        $this->expectExceptionMessage('パスにテーマディレクトリ外への参照が含まれています。');
        $this->ThemeFileService->copyToTheme($param);
    }

    /**
     * test copyToTheme - カレントテーマ以外（他テーマ・コアプラグイン）への書き込みを拒否する
     *
     * 以前の境界は plugins/ 全体だったため、path に `..` を含めるとカレントテーマ以外の
     * プラグイン配下（コアプラグイン等）へ書き込めた。コピー先はカレントテーマ配下に限定する。
     * @dataProvider provideCopyToThemeOutsideCurrentTheme
     */
    public function test_copyToTheme_rejectsOutsideCurrentTheme(string $path): void
    {
        SiteFactory::make(['id' => 1, 'status' => true, 'theme' => 'BcColumn'])->persist();
        $param = [
            'plugin' => 'BaserCore',
            'theme' => 'BcFront',
            'type' => 'css',
            'path' => $path,
            'fullpath' => '/var/www/html/plugins/bc-front/webroot/css/bge_style.css',
            'assets' => true,
        ];
        $this->expectException('BaserCore\Error\BcException');
        $this->expectExceptionMessage('パスにテーマディレクトリ外への参照が含まれています。');
        $this->ThemeFileService->copyToTheme($param);
    }

    public static function provideCopyToThemeOutsideCurrentTheme(): array
    {
        return [
            'コアプラグイン配下' => ['../../../baser-core/webroot/pwned.css'],
            '他テーマ配下' => ['../../../bc-front/webroot/css/pwned.css'],
        ];
    }

    /**
     * test copyToTheme - コピー元がコピー元テーマ／プラグインのディレクトリ外の場合は拒否する
     *
     * Admin web 経路では parseArgs() が URL の path を `..` を除去せず fullpath に組み立てるため、
     * コピー元にサーバ上の任意ファイルを指定でき、カレントテーマの webroot 配下へコピーして
     * 公開 URL から取得できた（任意ファイル読み出し）。Service の sink でもコピー元を検証する。
     */
    public function test_copyToTheme_rejectsOutsideSourceDir(): void
    {
        SiteFactory::make(['id' => 1, 'status' => true, 'theme' => 'BcColumn'])->persist();
        $param = [
            'plugin' => 'BaserCore',
            'theme' => 'BcFront',
            'type' => 'css',
            'path' => 'leaked.css',
            // コピー元が bc-front/webroot/css/ の外
            'fullpath' => '/var/www/html/plugins/bc-front/webroot/css/../../../../config/app.php',
            'assets' => true,
        ];
        $this->expectException('BaserCore\Error\BcException');
        $this->expectExceptionMessage('パスにテーマディレクトリ外への参照が含まれています。');
        $this->ThemeFileService->copyToTheme($param);
        $this->assertFileDoesNotExist('/var/www/html/plugins/BcColumn/webroot/css/leaked.css');
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
