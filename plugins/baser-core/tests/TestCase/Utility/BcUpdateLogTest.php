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
        if (!file_exists(LOGS)) {
            mkdir(LOGS, 0777);
        }

        // set get test
        $this->assertEquals([], BcUpdateLog::get());
        BcUpdateLog::set('test1');
        BcUpdateLog::set('test2');
        $this->assertEquals(['test1', 'test2'], BcUpdateLog::get());

        // file save test
        BcUpdateLog::save();
        $contents = file(LOGS . 'update.log', FILE_IGNORE_NEW_LINES);
        $this->assertMatchesRegularExpression("/.+?info: test2/", $contents[count($contents) -1]);
        $this->assertEquals([], BcUpdateLog::get());

        // tmp clear test
        BcUpdateLog::set('test1');
        BcUpdateLog::clear();
        $this->assertEquals([], BcUpdateLog::get());
    }

}
