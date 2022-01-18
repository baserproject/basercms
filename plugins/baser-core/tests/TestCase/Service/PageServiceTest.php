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

namespace BaserCore\Test\TestCase\Service;

use BaserCore\Model\Table\PagesTable;
use BaserCore\Service\PageService;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class PageServiceTest
 * @property PageService $PageService
 * @property PagesTable $Pages
 */
class PageServiceTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Pages',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Users',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->PageService = new PageService();
        $this->Pages = $this->getTableLocator()->get('Pages');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->PageService);
        unset($this->Pages);
        parent::tearDown();
    }

    /**
     * Test get
     *
     * @return void
     */
    public function testGet()
    {
        $page = $this->PageService->get(2);
        $this->assertRegExp('/<section class="mainHeadline">/', $page->contents);
    }
    /**
     * Test getTrash
     *
     * @return void
     */
    public function testGetTrash()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $page = $this->PageService->getTrash(1);
        // 論理削除されているコンテンツに紐付いている場合
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->expectExceptionMessage('Record not found in table contents');
        $page = $this->PageService->getTrash(1);
        // $this->assertEquals('削除済みフォルダー(親)', $page->folder_template);
        // $this->assertEquals(10, $page->content->entity_id);
        // $this->assertEquals('メインサイト', $page->content->site->display_name);
    }

    /**
     * Test create
     */
    public function testCreate()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->getRequest();
        $Page = $this->PageService->create('Test Message');
        $savedPage = $this->Pages->get($Page->id);
        $this->assertEquals('Test Message', $savedPage->message);
    }

    /**
     * Test getIndex
     */
    public function testGetIndex()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $request = $this->getRequest('/');
        $Pages = $this->PageService->getIndex($request->getQueryParams());
        $this->assertEquals('Pages test message1', $Pages->first()->message);

        $request = $this->getRequest('/?message=message2');
        $Pages = $this->PageService->getIndex($request->getQueryParams());
        $this->assertEquals('Pages test message2', $Pages->first()->message);

        $request = $this->getRequest('/?user_id=3');
        $Pages = $this->PageService->getIndex($request->getQueryParams());
        $this->assertEquals('Pages test message3', $Pages->first()->message);
    }
}
