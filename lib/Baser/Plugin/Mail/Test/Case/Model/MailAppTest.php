<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.Test.Case.Model
 * @since			baserCMS v 3.0.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('MailAppModel', 'Mail.Model');

/**
 * Class MailAppTest
 * @property MailAppModel $MailApp
 */

class MailAppTest extends BaserTestCase {

	public $fixtures = array(
		'baser.Default.SiteConfig',
		'plugin.mail.Default/MailMessage',
		'plugin.mail.Default/MailConfig',
		'plugin.mail.Default/MailContent',
		'plugin.mail.Default/MailField',
	);

	public function setUp() {
		$this->MailApp = ClassRegistry::init('Mail.MailApp');
		parent::setUp();
	}

	public function tearDown() {
		unset($this->MailApp);
		parent::tearDown();
	}

}
