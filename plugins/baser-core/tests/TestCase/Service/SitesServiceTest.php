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
use BaserCore\Service\SitesService;

class SitesServiceTest extends \BaserCore\TestSuite\BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Sites',
    ];

    /**
     * @var SitesService|null
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
        $this->assertEquals('mobile', $user->name);
    }

    /**
     * Test getIndex
     */
    public function testGetIndex()
    {
        $request = $this->getRequest('/');

        $users = $this->Sites->getIndex($request->getQueryParams());
        $this->assertEquals('mobile', $users->first()->name);

        $request = $this->getRequest('/?name=smart');
        $users = $this->Sites->getIndex($request->getQueryParams());
        $this->assertEquals('smartphone', $users->first()->name);

        $request = $this->getRequest('/?num=1');
        $users = $this->Sites->getIndex($request->getQueryParams());
        $this->assertEquals(1, $users->all()->count());

        $request = $this->getRequest('/?status=1');
        $users = $this->Sites->getIndex($request->getQueryParams());
        $this->assertEquals(1, $users->all()->count());
    }

    /**
     * Test create
     */
    public function testCreate()
    {
        $request = $this->getRequest('/');
        $request = $request->withParsedBody([
            'name' => 'english',
            'display_name' => '英語サイト',
            'title' => 'baserの英語サイト',
            'alias' => 'en'
        ]);
        $this->Sites->create($request->getData());
        $request = $this->getRequest('/?name=english');
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
        $this->assertEquals(1, $users->all()->count());
    }
}
