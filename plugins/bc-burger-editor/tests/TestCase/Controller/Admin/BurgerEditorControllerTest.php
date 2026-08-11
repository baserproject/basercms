<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.4.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcBurgerEditor\Test\TestCase\Controller\Admin;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcBurgerEditor\Lib\BurgerEditorUtil;
use BcBurgerEditor\Service\BurgerEditorService;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Core\Plugin;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Laminas\Diactoros\UploadedFile;

/**
 * BurgerEditorControllerTest
 *
 * 各アクションが exit せず Response を返すことを含めて検証する
 */
class BurgerEditorControllerTest extends BcTestCase
{
    use ScenarioAwareTrait;

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        // 本プラグインはマイグレーションを持たず tests/bootstrap.php の一覧に含まれないため、
        // ルートを接続するにはリクエストごとに生成される app へ明示的に読み込ませる必要がある
        $this->appPluginsToLoad[] = 'BcBurgerEditor';
        // プラグインの設定はテストプロセスには読み込まれないため明示的に読み込む
        // （Bge.fileShare 等が未定義だと保存先の解決結果が実行時と変わる）
        if (Configure::read('Bge') === null) {
            if (!in_array('baser', Configure::configured(), true)) {
                Configure::config('baser', new PhpConfig());
            }
            Configure::load('BcBurgerEditor.setting', 'baser');
        }

        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loginAdmin($this->getRequest('/baser/admin'));
    }

    /**
     * tearDown
     *
     * アップロード先は実環境と同じ webroot/files/bgeditor 配下となるため、
     * テストが作成したファイルを必ず削除する
     */
    public function tearDown(): void
    {
        foreach($this->uploadedPaths as $path) {
            if (file_exists($path)) unlink($path);
        }
        $this->uploadedPaths = [];
        parent::tearDown();
    }

    /**
     * テストが作成したファイルのパス
     *
     * @var array
     */
    private $uploadedPaths = [];

    /**
     * 指定したファイル名を基準に、サイズ別を含む生成物を後始末の対象として登録する
     *
     * @param string $baseDir
     * @param string $fileId
     * @return void
     */
    private function registerUploadedPaths($baseDir, $fileId)
    {
        foreach(glob($baseDir . $fileId . '__*') as $path) {
            $this->uploadedPaths[] = $path;
        }
    }

    /**
     * test img_list
     */
    public function test_img_list()
    {
        $this->get('/baser/admin/bc-burger-editor/burger_editor/img_list');
        $this->assertResponseOk();
        $this->assertContentType('application/json');

        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertIsArray($result);
        $this->assertFalse($result['error']);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('pagination', $result);
        // 先頭には「画像無し」が入る
        $this->assertSame('画像無し', $result['data'][0]['name']);
    }

    /**
     * test img_list
     *
     * ページ番号を指定してもエラーとならない
     */
    public function test_img_list_withQuery()
    {
        $this->get('/baser/admin/bc-burger-editor/burger_editor/img_list?word=notfound&page=2');
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertFalse($result['error']);
    }

    /**
     * test file_list
     */
    public function test_file_list()
    {
        $this->get('/baser/admin/bc-burger-editor/burger_editor/file_list');
        $this->assertResponseOk();
        $this->assertContentType('application/json');

        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertFalse($result['error']);
        $this->assertArrayHasKey('pagination', $result);
    }

    /**
     * test img_delete
     *
     * 存在しないファイルを指定した場合は 0 を返す
     */
    public function test_img_delete_withMissingFile()
    {
        $this->enableCsrfToken();
        $this->post('/baser/admin/bc-burger-editor/burger_editor/img_delete', ['file' => 'not-exists.jpg']);
        $this->assertResponseOk();
        $this->assertSame('0', (string)$this->_response->getBody());
    }

    /**
     * test file_delete
     */
    public function test_file_delete_withMissingFile()
    {
        $this->enableCsrfToken();
        $this->post('/baser/admin/bc-burger-editor/burger_editor/file_delete', ['file' => 'not-exists.pdf']);
        $this->assertResponseOk();
        $this->assertSame('1', (string)$this->_response->getBody());
    }

    /**
     * test img_upload
     *
     * ファイルが送信されていない場合はエラーを返す
     */
    public function test_img_upload_withoutFile()
    {
        $this->enableCsrfToken();
        $this->post('/baser/admin/bc-burger-editor/burger_editor/img_upload');
        $this->assertResponseOk();

        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertSame('ファイルがアップロードされていません', $result['error']);
    }

    /**
     * test file_upload
     *
     * ファイルが送信されていない場合はエラーを返す
     */
    public function test_file_upload_withoutFile()
    {
        $this->enableCsrfToken();
        $this->post('/baser/admin/bc-burger-editor/burger_editor/file_upload');
        $this->assertResponseOk();

        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertSame('ファイルがアップロードされていません', $result['error']);
    }

    /**
     * test img_upload
     *
     * 画像以外を送信した場合はエラーを返す
     */
    public function test_img_upload_withNotImageFile()
    {
        $this->enableCsrfToken();
        $this->configRequest(['files' => ['file' => $this->createUploadedFile('sample.txt', 'text/plain', 'dummy')]]);
        $this->post('/baser/admin/bc-burger-editor/burger_editor/img_upload');
        $this->assertResponseOk();

        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertSame('画像形式のファイルをアップロードしてください', $result['error']);
    }

    /**
     * test img_upload
     *
     * 画像が保存され、一覧に反映される
     */
    public function test_img_upload()
    {
        $this->enableCsrfToken();
        $this->configRequest(['files' => ['file' => $this->createUploadedFile(
                'テスト画像.png',
                'image/png',
                file_get_contents(Plugin::path('BcBurgerEditor') . 'webroot' . DS . 'img' . DS . 'bg-sample.png')
            )]]);
        $this->post('/baser/admin/bc-burger-editor/burger_editor/img_upload');
        $this->assertResponseOk();

        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertFalse($result['error'], is_string($result['error'])? $result['error'] : '');
        // 「画像無し」の次にアップロードした画像が並ぶ
        $this->assertSame('テスト画像.png', $result['data'][1]['name']);
        $this->registerUploadedPaths($this->getSavePath('img'), $result['data'][1]['fileId']);
    }

    /**
     * test file_upload
     *
     * 許可されていない拡張子はエラーを返す
     */
    public function test_file_upload_withNotAllowedExtension()
    {
        $this->enableCsrfToken();
        $this->configRequest(['files' => ['file' => $this->createUploadedFile('sample.exe', 'application/octet-stream', 'dummy')]]);
        $this->post('/baser/admin/bc-burger-editor/burger_editor/file_upload');
        $this->assertResponseOk();

        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertSame('許可されていないファイル形式です', $result['error']);
    }

    /**
     * test file_upload / file_delete
     *
     * ファイルが保存され、削除できる
     */
    public function test_file_upload()
    {
        $this->enableCsrfToken();
        $this->configRequest(['files' => ['file' => $this->createUploadedFile('テスト資料.txt', 'text/plain', 'dummy')]]);
        $this->post('/baser/admin/bc-burger-editor/burger_editor/file_upload');
        $this->assertResponseOk();

        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertFalse($result['error'], is_string($result['error'])? $result['error'] : '');
        $this->assertSame('テスト資料.txt', $result['data'][0]['name']);

        // 実ファイルが保存されている
        $this->registerUploadedPaths($this->getSavePath('other'), $result['data'][0]['fileId']);
        $this->assertCount(1, $this->uploadedPaths);
    }

    /**
     * test file_delete
     *
     * 保存済みファイルが削除される
     */
    public function test_file_delete()
    {
        $filename = '9999__' . BurgerEditorUtil::b64e('削除対象') . '.txt';
        $filePath = $this->getSavePath('other') . $filename;
        file_put_contents($filePath, 'dummy');

        $this->enableCsrfToken();
        $this->post('/baser/admin/bc-burger-editor/burger_editor/file_delete', ['file' => $filename]);
        $this->assertResponseOk();
        $this->assertFileDoesNotExist($filePath);
    }

    /**
     * test img_delete
     *
     * 保存済み画像がサイズ別ファイルも含めて削除される
     */
    public function test_img_delete()
    {
        $base = '9999__' . BurgerEditorUtil::b64e('削除対象');
        $paths = [
            $this->getSavePath('img') . $base . '.png',
            $this->getSavePath('img') . $base . '__org.png',
            $this->getSavePath('img') . $base . '__small.png',
        ];
        foreach($paths as $path) {
            file_put_contents($path, 'dummy');
        }

        $this->enableCsrfToken();
        $this->post('/baser/admin/bc-burger-editor/burger_editor/img_delete', ['file' => $base . '.png']);
        $this->assertResponseOk();
        $this->assertSame('1', (string)$this->_response->getBody());
        foreach($paths as $path) {
            $this->assertFileDoesNotExist($path);
        }
    }

    /**
     * 保存先パスを取得する
     *
     * @param string $type img|other
     * @return string
     */
    private function getSavePath($type)
    {
        $service = new BurgerEditorService();
        $service->setupSavePath();
        return $type === 'img'? $service->getImageFileBaseDir() : $service->getOtherFileBaseDir();
    }

    /**
     * テスト用のアップロードファイルを生成する
     *
     * @param string $filename
     * @param string $type
     * @param string $content
     * @return UploadedFile
     */
    private function createUploadedFile($filename, $type, $content)
    {
        $tmpPath = TMP . 'bc_burger_editor_upload_' . uniqid() . '_' . $filename;
        file_put_contents($tmpPath, $content);
        return new UploadedFile($tmpPath, filesize($tmpPath), UPLOAD_ERR_OK, $filename, $type);
    }

    /**
     * test get_filename
     *
     * エンコードされたファイル名が復元される
     */
    public function test_get_filename()
    {
        $encoded = '12__' . BurgerEditorUtil::b64e('サンプル') . '.jpg';
        $this->get('/baser/admin/bc-burger-editor/burger_editor/get_filename/' . $encoded);
        $this->assertResponseOk();
        $this->assertContentType('application/json');

        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertSame('12.サンプル.jpg', $result['filename']);
    }

    /**
     * test get_filename
     *
     * 規則に合わないファイル名はそのまま返る
     */
    public function test_get_filename_withPlainName()
    {
        $this->get('/baser/admin/bc-burger-editor/burger_editor/get_filename/sample.jpg');
        $this->assertResponseOk();

        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertSame('sample.jpg', $result['filename']);
    }

}
