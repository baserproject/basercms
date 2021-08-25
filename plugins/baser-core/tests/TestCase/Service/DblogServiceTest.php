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

use BaserCore\Model\Table\DblogsTable;
use BaserCore\Service\DblogService;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class DblogServiceTest
 * @property DblogService $DblogService
 * @property DblogsTable $Dblogs
 */
class DblogServiceTest extends BcTestCase
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
        $this->DblogService = new DblogService();
        $this->Dblogs = $this->getTableLocator()->get('Dblogs');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->DblogService);
        unset($this->Dblogs);
        parent::tearDown();
    }

    /**
     * Test create
     */
    public function testCreate()
    {
        $this->getRequest();
        $dblog = $this->DblogService->create('Test Message');
        $savedDblog = $this->Dblogs->get($dblog->id);
        $this->assertEquals('Test Message', $savedDblog->message);
    }

    /**
     * Test getIndex
     */
    public function testGetIndex()
    {
        $request = $this->getRequest('/');
        $dblogs = $this->DblogService->getIndex($request->getQueryParams());
        $this->assertEquals('dblogs test message1', $dblogs->first()->message);

        $request = $this->getRequest('/?message=message2');
        $dblogs = $this->DblogService->getIndex($request->getQueryParams());
        $this->assertEquals('dblogs test message2', $dblogs->first()->message);

        $request = $this->getRequest('/?user_id=3');
        $dblogs = $this->DblogService->getIndex($request->getQueryParams());
        $this->assertEquals('dblogs test message3', $dblogs->first()->message);
    }

    /**
     * Test getDblogs
     */
    public function testGetDblogs()
    {
        $dblogs = $this->DblogService->getDblogs(2)->toArray();
        $this->assertEquals(2, count($dblogs));
        $this->assertEquals(3, $dblogs[0]->id);
    }

    /**
     * Test delteAll
     */
    public function testDeleteAll()
    {
        $dblogs = $this->DblogService->getDblogs(1)->toArray();
        $this->assertEquals(true, count($dblogs));
        $this->DblogService->deleteAll();
        $dblogs = $this->DblogService->getDblogs(1)->toArray();
        $this->assertEquals(0, count($dblogs));
    }
}
