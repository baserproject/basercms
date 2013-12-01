<?php
/**
 * test for MailContent
 *
 * PHP versions 5
 *
 * baserCMS : Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 * @package         Mail Plugin
 * @copyright       Copyright 2008 - 2013, baserCMS Users Community
 * @link            http://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.0-beta
 * @license         http://basercms.net/license/index.html
 */
App::uses('MailContent','Mail.Model');

class MailContentTest extends CakeTestCase {

    public $fixtures = array(
        'baser.site_config',
        'plugin.mail.message',
        'plugin.mail.mail_config',
        'plugin.mail.mail_content',
        'plugin.mail.mail_field',
    );


    public function setUp() {
        $this->Message = ClassRegistry::init('Message');
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }
/**
 * [test_copy description]
 * @return [type] [description]
 */
    public function test_copy(){
    }
}
