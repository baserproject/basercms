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

use BaserCore\TestSuite\BcTestCase;
use BcThemeFile\Service\ThemeFoldersService;
use Cake\Filesystem\Folder;

/**
 * ThemeFoldersServiceTest
 */
class ThemeFoldersServiceTest extends BcTestCase
{

    public $ThemeFoldersService = null;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ThemeFoldersService = new ThemeFoldersService();
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test getNew
     */
    public function test_getNew()
    {
        $fullPath = '/var/www/html/plugins/bc-front/webroot/img';
        //対象のメソッドをコル
        $rs = $this->ThemeFoldersService->getNew($fullPath);
        //戻る値を確認
        $this->assertEquals($rs->type, 'folder');
        $this->assertEquals($rs->fullpath, $fullPath);
        $this->assertEquals($rs->parent, $fullPath);
    }

    /**
     * test get
     */
    public function test_get()
    {
        $fullPath = '/var/www/html/plugins/bc-front/webroot/img';
        //対象のメソッドをコル
        $rs = $this->ThemeFoldersService->get($fullPath);
        //戻る値を確認
        $this->assertEquals($rs->type, 'folder');
        $this->assertEquals($rs->fullpath, $fullPath);
        $this->assertEquals($rs->parent, '/var/www/html/plugins/bc-front/webroot/');
    }

    /**
     * test getIndex
     */
    public function test_getIndex()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test create
     */
    public function test_create()
    {
        $fullpath = BASER_PLUGINS . 'BcThemeSample' . '/templates/layout';
        $data = [
            'fullpath' => $fullpath,
            'parent' => BASER_PLUGINS . 'BcThemeSample' . '/templates',
            'name' => 'new_folder',
        ];
        $rs = $this->ThemeFoldersService->create($data);
        //戻る値を確認
        $this->assertEquals($rs->getData('mode'), 'create');
        $this->assertEquals($rs->getData('fullpath'), $data['fullpath'] . DS . $data['name']);
        $this->assertEquals($rs->getData('name'), $data['name']);
        //実際にフォルダが作成されいてるか確認すること
        $this->assertTrue(is_dir($fullpath . DS . 'new_folder'));
        //作成されたフォルダを削除
        rmdir($fullpath . DS . 'new_folder');
    }

    /**
     * test update
     */
    public function test_update()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test copy
     */
    public function test_copy()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test batch
     */
    public function test_batch()
    {
        //テストテーマフォルダパス
        $fullpath = BASER_PLUGINS . 'BcThemeSample/templates/layout';
        (new Folder())->create($fullpath . DS . 'folder_1', 0777);
        (new Folder())->create($fullpath . DS . 'folder_2', 0777);
        //一括削除処理をテスト
        $paths = [
            $fullpath . DS . 'folder_1',
            $fullpath . DS . 'folder_2'
        ];
        $this->ThemeFoldersService->batch('delete', $paths);
        //実際にフォルダが削除できるか確認すること
        $this->assertFalse(is_dir($fullpath . DS . 'folder_1'));
        $this->assertFalse(is_dir($fullpath . DS . 'folder_2'));
    }

    /**
     * test getNamesByFullpath
     */
    public function test_getNamesByFullpath()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test copyToTheme
     */
    public function test_copyToTheme()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getForm
     */
    public function test_getForm()
    {
        $data['fullpath'] = '/var/www/html/plugins/bc-front/webroot/img';
        $rs = $this->ThemeFoldersService->getForm($data);
        $this->assertEquals($rs->getData('fullpath'), $data['fullpath']);
    }
}
