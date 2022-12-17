<?php
// TODO ucmitz  : コード確認要
return;
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Uploader.Test.Case
 * @since           baserCMS v 4.0.9
 * @license         https://basercms.net/license/index.html
 */

class UploaderAllHelpersTest extends CakeTestSuite
{

    /**
     * Suite define the tests for this suite
     *
     * @return CakeTestSuite
     */
    public static function suite()
    {
        $suite = new CakeTestSuite('All Uploader Helper tests');
        $suite->addTestDirectory(__DIR__ . DS . 'View' . DS . 'Helper' . DS);
        return $suite;
    }
}
