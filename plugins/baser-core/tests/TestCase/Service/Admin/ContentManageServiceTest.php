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

namespace BaserCore\Test\TestCase\Service\Admin;

use BaserCore\Service\Admin\ContentManageService;
use BaserCore\TestSuite\BcTestCase;

/**
 * ContentManageServiceTest
 * @property ContentManageService $ContentManage
 */
class ContentManageServiceTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Contents'
    ];


    /**
     * setUp
     *
     * @return void
     */
    public function setUp():void
    {
        parent::setUp();
        $this->ContentManage = new ContentManageService();
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->ContentManage);
    }

    /**
     * testGetAdminAjaxIndex
     * @dataProvider getAdminAjaxIndexDataProvider
     * @param  string $action
     * @param  string $listType
     * @param  string $siteId
     * @param  array $content
     * @param  string $template
     * @return void
     */
    public function testGetAdminAjaxIndex($action, $listType, $siteId, $content, $template): void
    {
        $request = $this->getRequest()->withQueryParams(array_merge(['action' => $action, 'list_type' => $listType, 'site_id' => $siteId], $content));
        $result = $this->ContentManage->getAdminAjaxIndex($request->getQueryParams());
        $this->assertEquals($template, key($result));
        $data = array_shift($result);
        $this->assertInstanceOf('Cake\ORM\Query', $data);
    }

    public function getAdminAjaxIndexDataProvider()
    {
        return [
            // tree形式の場合
            ['index', '1', '0', [
                'open' => '1',
                'folder_id' => '',
                'name' => '',
                'type' => '',
                'self_status' => '',
                'author_id' => '',
            ], "index_tree"],
            // Table形式の場合(content条件なし)
            ['index', '2', '0',
            [
                'open' => '1',
                'folder_id' => '',
                'name' => '',
                'type' => '',
                'self_status' => '',
                'author_id' => '',
            ]
            , "index_table"],
            // trash形式の場合
            ['trash_index', '1', '0',
            [
                'name' => '',
                'type' => '',
                'self_status' => true,
                'author_id' => '',
            ], "index_trash"],
        ];
    }

    /**
     * testGetContentsInfo
     *
     * @return void
     */
    public function testGetContentsInfo()
    {
        $result = $this->ContentManage->getContensInfo();
        $this->assertTrue(isset($result[0]['unpublished']));
        $this->assertTrue(isset($result[0]['published']));
        $this->assertTrue(isset($result[0]['total']));
        $this->assertTrue(isset($result[0]['display_name']));
    }

}
