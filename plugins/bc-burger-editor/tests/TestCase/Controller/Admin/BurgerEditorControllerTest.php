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
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

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
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loginAdmin($this->getRequest('/baser/admin'));
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
