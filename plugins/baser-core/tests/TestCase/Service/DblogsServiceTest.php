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

use BaserCore\Model\Table\DblogsTable;
use BaserCore\Service\DblogsService;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class DblogsServiceTest
 * @property DblogsService $DblogsService
 * @property DblogsTable $Dblogs
 */
class DblogsServiceTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Dblogs',
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
        $this->DblogsService = new DblogsService();
        $this->Dblogs = $this->getTableLocator()->get('Dblogs');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->DblogsService);
        unset($this->Dblogs);
        parent::tearDown();
    }

    /**
     * Test create
     */
    public function testCreate()
    {
        $this->getRequest();
        $dblog = $this->DblogsService->create(['message' => 'Test Message']);
        $savedDblog = $this->Dblogs->get($dblog->id);
        $this->assertEquals('Test Message', $savedDblog->message);
    }

    /**
     * Test getIndex
     */
    public function testGetIndex()
    {
        $request = $this->getRequest('/');
        $dblogs = $this->DblogsService->getIndex($request->getQueryParams());
        $this->assertEquals('dblogs test message1', $dblogs->first()->message);

        $request = $this->getRequest('/?message=message2');
        $dblogs = $this->DblogsService->getIndex($request->getQueryParams());
        $this->assertEquals('dblogs test message2', $dblogs->first()->message);

        $request = $this->getRequest('/?user_id=3');
        $dblogs = $this->DblogsService->getIndex($request->getQueryParams());
        $this->assertEquals('dblogs test message3', $dblogs->first()->message);
    }

    /**
     * Test getDblogs
     */
    public function testGetDblogs()
    {
        $dblogs = $this->DblogsService->getDblogs(2)->toArray();
        $this->assertEquals(2, count($dblogs));
        $this->assertEquals(3, $dblogs[0]->id);
    }

    /**
     * Test delteAll
     */
    public function testDeleteAll()
    {
        $dblogs = $this->DblogsService->getDblogs(1)->toArray();
        $this->assertEquals(true, count($dblogs));
        $this->DblogsService->deleteAll();
        $dblogs = $this->DblogsService->getDblogs(1)->toArray();
        $this->assertEquals(0, count($dblogs));
    }

    /**
     * test get
     */
    public function test_get()
    {
        $dblog = $this->DblogsService->get(1);
        $this->assertEquals('dblogs test message1', $dblog->message);
    }

    /**
     * test getList
     */
    public function test_getList()
    {
        $this->assertEquals([], $this->DblogsService->getList());
    }

    /**
     * test getNew
     */
    public function test_getNew()
    {
        $this->assertEquals([], $this->DblogsService->getNew()->toArray());
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        $count = $this->DblogsService->getIndex()->count();
        $this->DblogsService->delete(1);
        $this->assertEquals($count - 1, $this->DblogsService->getIndex()->count());
    }

}
