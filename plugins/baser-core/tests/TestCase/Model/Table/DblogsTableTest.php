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

namespace BaserCore\Test\TestCase\Model\Table;

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
        $this->Dblogs = $this->getTableLocator()->get('BaserCore.Dblogs');
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
     * Test initialize
     *
     * @return void
     */
    public function testInitialize(): void
    {
        $this->assertEquals('dblogs', $this->Dblogs->getTable());
        $this->assertTrue($this->Dblogs->hasBehavior('Timestamp'));
        $this->assertTrue($this->Dblogs->hasAssociation('Users'));
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
