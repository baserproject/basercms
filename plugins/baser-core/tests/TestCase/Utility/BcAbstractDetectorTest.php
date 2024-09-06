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

namespace BaserCore\Test\TestCase\Utility;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcAgent;
use Cake\Core\Configure;

/**
 * Class BcAbstractDetector
 *
 */
class BcAbstractDetectorTest extends BcTestCase
{

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test find
     */
    public function testFind()
    {
        Configure::write("BcApp.smartphone", true);

        //Configureにnameがある場合、
        $rs = BcAgent::find('smartphone');
        $this->assertEquals('smartphone', $rs->name);
        $this->assertEquals('device', $rs->type);

        //Configureにnameがない場合、
        $this->assertNull(BcAgent::find('test'));
    }

    /**
     * test findAll
     */
    public function testFindAll()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test findCurrent
     */
    public function testFindCurrent()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
