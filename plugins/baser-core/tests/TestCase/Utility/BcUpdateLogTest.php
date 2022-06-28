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

use BaserCore\Utility\BcUpdateLog;
use Cake\Filesystem\File;

/**
 * BcUpdateLogTest
 */
class BcUpdateLogTest extends \BaserCore\TestSuite\BcTestCase
{
    /**
     * test set and get and save and clear
     */
    public function test_setAndGetAndSaveAndClear()
    {
        $this->assertEquals([], BcUpdateLog::get());
        BcUpdateLog::set('test1');
        BcUpdateLog::set('test2');
        $this->assertEquals(['test1', 'test2'], BcUpdateLog::get());
        rename(LOGS . 'update.log', LOGS . 'update.bak.log');
        BcUpdateLog::save();
        $file = new File(LOGS . 'update.log');
        $this->assertMatchesRegularExpression("/.+?info: test1\n.+?info: test2/", $file->read());
        $this->assertEquals([], BcUpdateLog::get());
        BcUpdateLog::set('test1');
        BcUpdateLog::clear();
        $this->assertEquals([], BcUpdateLog::get());
    }

}
