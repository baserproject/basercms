<?php

/**
 * test for MailController
 *
 * baserCMS : Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 * @package         Mail.Test.Case.Controller
 * @copyright       Copyright 2008 - 2015, baserCMS Users Community
 * @link            http://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.0-beta
 * @license         http://basercms.net/license/index.html
 */
App::uses('MailController', 'Mail.Controller');
App::uses('Message', 'Mail.Model');

class MailControllerTest extends BaserTestCase {

	public $fixtures = array(
		'baser.Default.SiteConfig',
		'baser.Default.Page',
		'plugin.mail.Default/Message',
	);

	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
	}

/**
 * [test_index description]
 * @return [type] [description]
 */
	public function test_index() {
		// $result = $this->testAction('/contact/index');
	}

}
