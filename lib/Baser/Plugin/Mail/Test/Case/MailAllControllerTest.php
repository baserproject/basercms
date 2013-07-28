<?php
/**
 * run all controller baser mail tests
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 * @package         Mail Plugin
 * @copyright       Copyright 2008 - 2013, baserCMS Users Community
 * @link            http://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.0-beta
 * @license         http://basercms.net/license/index.html
 */

class MailAllControllerTest extends PHPUnit_Framework_TestSuite {

/**
 * Suite define the tests for this suite
 *
 * @return void
 */
    public static function suite() {
        $suite = new CakeTestSuite('All Helper tests');
        $suite->addTestDirectory(__DIR__ . DS . 'Controller'. DS);
        $suite->addTestDirectory(__DIR__ . DS . 'Controller'. DS . 'Component'.DS);
        return $suite;
    }
}