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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
