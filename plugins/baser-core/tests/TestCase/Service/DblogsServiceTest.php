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

use BaserCore\Service\DblogsService;
use BaserCore\Model\Table\DblogsTable;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class DblogsServiceTest
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
        $dbLog = $this->DblogsService->create([
            'message' => 'Test Message',
        ]);
        $savedDblog = $this->Dblogs->get($dbLog->id);
        $this->assertEquals('Test Message', $savedDblog->message);
    }

}
