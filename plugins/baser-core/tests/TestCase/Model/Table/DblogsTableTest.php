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

namespace BaserCore\Test\TestCase\Model\Table;

use BaserCore\Model\Table\DblogsTable;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class DblogsTableTest
 */
class DblogsTableTest extends BcTestCase
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
        $config = $this->getTableLocator()->exists('Dblogs')? [] : ['className' => 'BaserCore\Model\Table\DblogsTable'];
        $this->Dblogs = $this->getTableLocator()->get('Dblogs', $config);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Dblogs);
        parent::tearDown();
    }

    /**
     * Test validationDefault
     */
    public function testValidationDefault()
    {
        $testMessage = '';
        for ($i = 0; $i < 2000; $i++) {
            $testMessage .= '1';
        }
        $dblog = $this->Dblogs->newEntity([
            'message' => $testMessage,
        ]);
        $this->assertSame([
            'message' => ['maxLength' => 'メッセージは1000文字以内で入力してください。'],
        ], $dblog->getErrors());
    }

}
