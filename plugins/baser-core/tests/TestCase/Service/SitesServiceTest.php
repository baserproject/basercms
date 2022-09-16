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

namespace BaserCore\Test\TestCase\Service;

use BaserCore\Service\SitesService;
use BaserCore\Utility\BcContainerTrait;

class SitesServiceTest extends \BaserCore\TestSuite\BcTestCase
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
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.ContentFolders',
    ];

    /**
     * @var SitesService|null
     */
    public $Sites = null;

    /**
     * setUpBeforeClass
     */
    public static function setUpBeforeClass(): void
    {
        self::truncateTable('sites');
    }

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Sites = new SitesService();
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
     * dev#716
     * test construct
     * @return void
     */
    public function testConstruct(){
        $this->assertTrue(isset($this->Sites->Sites));
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

        $request = $this->getRequest('/?limit=1');
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
     * Test publish
     *
     * @return void
     */
    public function testPublish()
    {
        $sites = $this->getTableLocator()->get('Sites');

        $site = $sites->find()->order(['id' => 'ASC'])->first();
        $site->status = false;
        $sites->save($site);

        $this->Sites->publish($site->id);

        $site = $sites->get($site->id);
        $this->assertTrue($site->status);
    }

    /**
     * Test unpublish
     *
     * @return void
     */
    public function testUnpublish()
    {
        $sites = $this->getTableLocator()->get('Sites');

        $site = $sites->find()->order(['id' => 'ASC'])->first();
        $site->status = true;
        $sites->save($site);

        $this->Sites->unpublish($site->id);

        $site = $sites->get($site->id);
        $this->assertFalse($site->status);
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
        $themes = $this->Sites->getThemeList();
        $this->assertTrue(in_array('BcFront', $themes));
        $this->assertFalse(in_array('BcAdminThird', $themes));
    }

}
