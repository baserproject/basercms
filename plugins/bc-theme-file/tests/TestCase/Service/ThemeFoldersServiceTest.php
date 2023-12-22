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
use BaserCore\Test\Factory\UserFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcFolder;
use BcThemeFile\Service\ThemeFoldersService;

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
        //typeはetcではない場合、
        $param['fullpath'] = '/var/www/html/plugins/bc-front/templates';
        $param['type'] = 'folder';
        $themeFiles = $this->ThemeFoldersService->getIndex($param);
        $this->assertCount(14, $themeFiles);

        //typeはetcかつpathは指定しない場合、
        $param['type'] = 'etc';
        $param['path'] = '';
        $themeFiles = $this->ThemeFoldersService->getIndex($param);
        $this->assertCount(8, $themeFiles);

        //typeはetcかつpathは指定した場合、
        $param['path'] = '/var/www/html/plugins/bc-front/templates';
        $themeFiles = $this->ThemeFoldersService->getIndex($param);
        $this->assertCount(12, $themeFiles);
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
        //テストテーマフォルダを作成
        $fullpath = BASER_PLUGINS . 'BcThemeSample/templates/layout/';
        mkdir($fullpath . 'new_folder');

        //ポストデータを作成
        $postData = [
            'fullpath' => $fullpath . 'new_folder',
            'name' => 'edit_folder'
        ];
        //サービスメソッドをコル
        $this->ThemeFoldersService->update($postData);

        //実際にフォルダが変更されいてるか確認すること
        $this->assertTrue(is_dir($fullpath . 'edit_folder'));
        //変更前のフォルダが存在しないか確認すること
        $this->assertFalse(is_dir($fullpath . 'new_folder'));
        //変更されたフォルダを削除
        rmdir($fullpath . 'edit_folder');
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        //テストテーマフォルダを作成
        $fullpath = BASER_PLUGINS . 'BcThemeSample' . '/templates/layout';
        (new BcFolder($fullpath . DS . 'delete_folder'))->create();
        $rs = $this->ThemeFoldersService->delete($fullpath . DS . 'delete_folder');
        //戻る値を確認
        $this->assertTrue($rs);
        //実際にフォルダが削除されいてるか確認すること
        $this->assertFalse(file_exists($fullpath . 'delete_folder'));
    }

    /**
     * test copy
     */
    public function test_copy()
    {
        //テストテーマフォルダを作成
        $fullpath = BASER_PLUGINS . 'BcThemeSample/templates/layout/';
        (new BcFolder($fullpath . 'new_folder'))->create();

        //対象のメソッドを確認
        $rs = $this->ThemeFoldersService->copy($fullpath . 'new_folder');

        //戻る値を確認
        $this->assertEquals($rs->type, 'folder');
        $this->assertEquals($rs->fullpath, $fullpath . 'new_folder_copy');
        $this->assertEquals($rs->parent, $fullpath);

        //実際にフォルダが作成されいてるか確認すること
        $this->assertTrue(is_dir($fullpath . 'new_folder_copy'));

        //作成されたフォルダを削除
        rmdir($fullpath . 'new_folder');
        rmdir($fullpath . 'new_folder_copy');
    }

    /**
     * test batch
     */
    public function test_batch()
    {
        //テストテーマフォルダパス
        $fullpath = BASER_PLUGINS . 'BcThemeSample/templates/layout';
        (new BcFolder($fullpath . DS . 'folder_1'))->create();
        (new BcFolder($fullpath . DS . 'folder_2'))->create();
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
        $fullPaths = [
            '/var/www/html/plugins/bc-front/webroot/img',
            '/var/www/html/plugins/bc-front/webroot/js',
        ];
        //対象のメソッドをコル
        $rs = $this->ThemeFoldersService->getNamesByFullpath($fullPaths);
        //戻る値を確認
        $this->assertEquals($rs, ['img', 'js']);
    }

    /**
     * test copyToTheme
     */
    public function test_copyToTheme()
    {
        //現在のテーマを設定する
        SiteFactory::make(['id' => 1, 'status' => true, 'theme' => 'BcPluginSample'])->persist();
        UserFactory::make()->admin()->persist();
        $this->getRequest();

        $fullpath = BASER_PLUGINS . '/BcPluginSample/templates/';
        $data = [
            'type' => 'Pages',
            'path' => '',
            'assets' => '',
            'plugin' => 'BaserCore',
            'fullpath' => '/var/www/html/plugins/bc-front/templates/Pages/'
        ];

        $rs = $this->ThemeFoldersService->copyToTheme($data);
        //戻る値を確認
        $this->assertEquals($rs, '/plugins/BcPluginSample/templates/Pages/');
        //実際にフォルダがコピーできるか確認すること
        $this->assertTrue(is_dir($fullpath . '/Pages'));
        //生成されたフォルダを削除
        unlink($fullpath . '/Pages/default.php');
        rmdir($fullpath . '/Pages');

        //コピーできないの場合、
        $data ['fullpath'] = '/var/www/html/plugins/bc-front/templates/Pages/11111';

        $rs = $this->ThemeFoldersService->copyToTheme($data);
        //戻る値を確認
        $this->assertFalse($rs);
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
