<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Mail.Test.Fixture.Default
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

class MailContentFixture extends BaserTestFixture
{

	/**
	 * Records
	 *
	 * @var array
	 */
	public $records = [
		[
			'id' => '1',
			'description' => '<p><span style="color:#C30">*</span> 印の項目は必須となりますので、必ず入力してください。</p>',
			'sender_1' => '',
			'sender_2' => '',
			'sender_name' => 'baserCMS inc. [デモ]　お問い合わせ',
			'subject_user' => '【baserCMS】お問い合わせ頂きありがとうございます。',
			'subject_admin' => '【baserCMS】お問い合わせを受け付けました',
			'form_template' => 'default',
			'mail_template' => 'mail_default',
			'redirect_url' => 'https://basercms.net/',
			'auth_captcha' => 1,
			'widget_area' => null,
			'ssl_on' => 0,
			'publish_begin' => null,
			'publish_end' => null,
			'created' => '2015-01-27 12:56:53',
			'modified' => null
		],
	];

}
