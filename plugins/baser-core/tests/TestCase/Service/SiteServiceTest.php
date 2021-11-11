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
use BaserCore\Service\BcAdminServiceInterface;
use BaserCore\Service\SiteService;
use BaserCore\Utility\BcContainerTrait;

class SiteServiceTest extends \BaserCore\TestSuite\BcTestCase
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Sites',
    ];

    /**
     * @var SiteService|null
     */
    public $Sites = null;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Sites = new SiteService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Sites);
        parent::tearDown();
    }

    /**
     * Test getNew
     */
    public function testGetNew()
    {
        $this->assertFalse($this->Sites->getNew()->status);
    }

    /**
     * Test get
     */
    public function testGet()
    {
        $user = $this->Sites->get(1);
        $this->assertEquals('', $user->name);
    }

    /**
     * Test getIndex
     */
    public function testGetIndex()
    {
        $request = $this->getRequest('/');

        $users = $this->Sites->getIndex($request->getQueryParams());
        $this->assertEquals('', $users->first()->name);

        $request = $this->getRequest('/?name=smart');
        $users = $this->Sites->getIndex($request->getQueryParams());
        $this->assertEquals('smartphone', $users->first()->name);

        $request = $this->getRequest('/?num=1');
        $users = $this->Sites->getIndex($request->getQueryParams());
        $this->assertEquals(1, $users->all()->count());

        $request = $this->getRequest('/?status=1');
        $users = $this->Sites->getIndex($request->getQueryParams());
        $this->assertEquals(5, $users->all()->count());
    }

    /**
     * Test create
     */
    public function testCreate()
    {
        $request = $this->getRequest('/');
        $request = $request->withParsedBody([
            'name' => 'chinese',
            'display_name' => '中国サイト',
            'title' => 'baserの中国サイト',
            'alias' => 'zh'
        ]);
        $this->Sites->create($request->getData());
        $request = $this->getRequest('/?name=chinese');
        $users = $this->Sites->getIndex($request->getQueryParams());
        $this->assertEquals(1, $users->all()->count());
    }

    /**
     * Test update
     */
    public function testUpdate()
    {
        $request = $this->getRequest('/');
        $request = $request->withParsedBody([
            'name' => 'ucmitz',
        ]);
        $user = $this->Sites->get(1);
        $this->Sites->update($user, $request->getData());
        $request = $this->getRequest('/?name=ucmitz');
        $users = $this->Sites->getIndex($request->getQueryParams());
        $this->assertEquals(1, $users->all()->count());
    }

    /**
     * Test delete
     */
    public function testDelete()
    {
        $this->Sites->delete(2);
        $request = $this->getRequest('/');
        $users = $this->Sites->getIndex($request->getQueryParams());
        $this->assertEquals(5, $users->all()->count());
        $this->expectException("Exception");
        $this->Sites->delete(1);
    }


    /**
     * Test getLangList
     */
    public function testGetLangList()
    {
        $langs = $this->Sites->getLangList();
        $this->assertEquals('english', key($langs));
    }

    /**
     * Test getDeviceList
     */
    public function testGetDeviceList()
    {
        $devices = $this->Sites->getDeviceList();
        $this->assertEquals('mobile', key($devices));
    }

    /**
     * Test getSiteList
     */
    public function testGetSiteList()
    {
        $this->assertEquals(5, count($this->Sites->getList()));
        $this->Sites->create([
            'name' => 'test',
            'display_name' => 'test',
            'alias' => 'test',
            'title' => 'test',
            'status' => true
        ]);
        $this->assertEquals(6, count($this->Sites->getList()));
    }

    /**
     * Test getThemeList
     */
    public function testGetThemeList()
    {
        // TODO BcUtil::getAllThemeList() を実装しないとテストができない
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
