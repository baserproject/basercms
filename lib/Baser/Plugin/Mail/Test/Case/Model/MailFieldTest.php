<?php

/**
 * test for MessageField
 *
 * baserCMS : Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 * @package         Mail.Test.Case.Model
 * @copyright       Copyright 2008 - 2015, baserCMS Users Community
 * @link            http://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.0-beta
 * @license         http://basercms.net/license/index.html
 */
App::uses('MessageField', 'Mail.Model');

class MessageFieldTest extends BaserTestCase {

	public $fixtures = array(
		'baser.Default.SiteConfig',
		'plugin.mail.Default/Message',
		'plugin.mail.Default/MailConfig',
		'plugin.mail.Default/MailContent',
		'plugin.mail.Default/MailField',
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
	public function test_copy() {
	}

}
