<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Mail.Config
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * システムナビ
 */
$config['BcApp.adminNavigation'] = [
	'Plugins' => [
		'menus' => [
			'MailConfigs' => ['title' => __d('baser', 'メール基本設定'), 'url' => ['admin' => true, 'plugin' => 'mail', 'controller' => 'mail_configs', 'action' => 'form']],
		]
	]
];
/* @var MailContent $MailContent */
$MailContent = ClassRegistry::init('Mail.MailContent');
$mailContents = $MailContent->find('all', [
	'conditions' => ['Content.deleted' => 0],
	'recursive' => 0,
	'order' => $MailContent->id
]);
foreach($mailContents as $mailContent) {
	$mail = $mailContent['MailContent'];
	$content = $mailContent['Content'];
	$config['BcApp.adminNavigation.Contents.' . 'MailContent' . $mail['id']] = [
		'siteId' => $content['site_id'],
		'title' => $content['title'],
		'type' => 'mail-content',
		'icon' => 'bca-icon--mail',
		'menus' => [
			'MailMessages' . $mail['id'] => ['title' => __d('baser', '受信メール'), 'url' => ['admin' => true, 'plugin' => 'mail', 'controller' => 'mail_messages', 'action' => 'index', $mail['id']]],
			'MailFields' . $mail['id'] => [
				'title' => __d('baser', 'フィールド'),
				'url' => ['admin' => true, 'plugin' => 'mail', 'controller' => 'mail_fields', 'action' => 'index', $mail['id']],
				'currentRegex' => '/\/mail\/mail_fields\/[^\/]+?\/' . $mail['id'] . '($|\/)/s'
			],
			'MailContents' . $mail['id'] => ['title' => __d('baser', '設定'), 'url' => ['admin' => true, 'plugin' => 'mail', 'controller' => 'mail_contents', 'action' => 'edit', $mail['id']]]
		]
	];
}
// @deprecated 5.0.0 since 4.2.0 BcApp.adminNavigation の形式に変更
$config['BcApp.adminNavi.mail'] = [
	'name' => __d('baser', 'メールプラグイン'),
	'contents' => [
		['name' => __d('baser', '基本設定'), 'url' => ['admin' => true, 'plugin' => 'mail', 'controller' => 'mail_configs', 'action' => 'form']],
	]
];

$config['BcContents']['items']['Mail'] = [
	'MailContent' => [
		'title' => __d('baser', 'メールフォーム'),
		'multiple' => true,
		'preview' => true,
		'icon' => 'bca-icon--mail',
		'routes' => [
			'manage' => [
				'admin' => true,
				'plugin' => 'mail',
				'controller' => 'mail_fields',
				'action' => 'index'
			],
			'add' => [
				'admin' => true,
				'plugin' => 'mail',
				'controller' => 'mail_contents',
				'action' => 'ajax_add'
			],
			'edit' => [
				'admin' => true,
				'plugin' => 'mail',
				'controller' => 'mail_contents',
				'action' => 'edit'
			],
			'delete' => [
				'admin' => true,
				'plugin' => 'mail',
				'controller' => 'mail_contents',
				'action' => 'delete'
			],
			'view' => [
				'plugin' => 'mail',
				'controller' => 'mail',
				'action' => 'index'
			],
			'copy' => [
				'admin' => true,
				'plugin' => 'mail',
				'controller' => 'mail_contents',
				'action' => 'ajax_copy'
			]
		]
	]
];

/**
 * ショートコード
 */
$config['BcShortCode']['Mail'] = [
	'Mail.getForm'
];

$config['Mail']['autoComplete'] = [
	['name' => 'none', 'title' => '指定しない'],
	['name' => 'off', 'title' => '無効'],
	['name' => 'name', 'title' => '名前', 'child' => [
		['name' => 'honorific-prefix', 'title' => '接頭語（Mr.,Mrs.等）'],
		['name' => 'given-name', 'title' => '名前'],
		['name' => 'additional-name', 'title' => 'ミドルネーム'],
		['name' => 'family-name', 'title' => '名字'],
		['name' => 'honorific-suffix', 'title' => '接尾語（Jr.等）'],
	]],
	['name' => 'nickname', 'title' => 'ニックネーム'],
	['name' => 'organization-title', 'title' => '役職'],
	['name' => 'username', 'title' => 'ユーザー名'],
	['name' => 'new-password', 'title' => '新しいパスワード'],
	['name' => 'current-password', 'title' => '現在のパスワード'],
	['name' => 'one-time-code', 'title' => 'ワンタイムコード'],
	['name' => 'organization', 'title' => '企業または団体の名前'],
	['name' => 'street-address', 'title' => '住所', 'child' => [
		['name' => 'street-address1', 'title' => '住所（1行目）'],
		['name' => 'street-address2', 'title' => '住所（2行目）'],
		['name' => 'street-address3', 'title' => '住所（3行目）'],
	]],
	['name' => 'address-level1', 'title' => '住所1（都道府県、州）'],
	['name' => 'address-level2', 'title' => '住所2（市町村）'],
	['name' => 'address-level3', 'title' => '住所3（3番目の行政レベル）'],
	['name' => 'address-level4', 'title' => '住所4（もっとも細かい行政レベル）'],
	['name' => 'country', 'title' => '国コード'],
	['name' => 'country-name', 'title' => '国名'],
	['name' => 'postal-code', 'title' => '郵便番号'],
	['name' => 'cc-name', 'title' => 'クレジットカード名義', 'child' => [
		['name' => 'cc-given-name', 'title' => 'クレジットカード名義（名前）'],
		['name' => 'cc-additional-name', 'title' => 'クレジットカード名義（ミドルネーム）'],
		['name' => 'cc-family-name', 'title' => 'クレジットカード名義（名字）'],
	]],
	['name' => 'cc-number', 'title' => 'クレジットカード番号'],
	['name' => 'cc-exp', 'title' => 'クレジットカード有効期限', 'child' => [
		['name' => 'cc-exp-month', 'title' => 'クレジットカード有効期限（月）'],
		['name' => 'cc-exp-year', 'title' => 'クレジットカード有効期限（年）'],
	]],
	['name' => 'cc-csc', 'title' => 'クレジットカードセキュリティコード'],
	['name' => 'cc-type', 'title' => 'クレジットカード種類'],
	['name' => 'transaction-currency', 'title' => '決済通貨'],
	['name' => 'transaction-amount', 'title' => '決済通貨の単位による量'],
	['name' => 'language', 'title' => '言語'],
	['name' => 'bday', 'title' => '生年月日', 'child' => [
		['name' => 'bday-day', 'title' => '生年月日（日）'],
		['name' => 'bday-month', 'title' => '生年月日（月）'],
		['name' => 'bday-year', 'title' => '生年月日（年）'],
	]],
	['name' => 'sex', 'title' => '性別'],
	['name' => 'url', 'title' => 'URL'],
	['name' => 'photo', 'title' => '画像'],
	['name' => 'tel', 'title' => '電話番号', 'child' => [
		['name' => 'tel-country-code', 'title' => '国番号'],
		['name' => 'tel-national', 'title' => '国際電話番号', 'child' => [
			['name' => 'tel-area-code', 'title' => '電話番号（市外局番）'],
			['name' => 'tel-local', 'title' => '国番号や市外局番を含まない電話番号', 'child' => [
				['name' => 'tel-local-prefix', 'title' => '電話番号（市内局番）'],
				['name' => 'tel-local-suffix', 'title' => '電話番号（加入者番号）'],
			]],
		]],
	]],
	['name' => 'tel-extension', 'title' => '内線番号'],
	['name' => 'email', 'title' => 'Eメールアドレス'],
	['name' => 'impp', 'title' => 'インスタントメッセージングプロトコルの端点'],
	['name' => 'on', 'title' => '自動設定'],
];
