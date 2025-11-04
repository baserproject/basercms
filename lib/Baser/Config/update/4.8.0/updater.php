<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright        Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Baser.Config
 * @since            baserCMS v 4.8.0
 * @license            https://basercms.net/license/index.html
 */

/**
 * 4.8.0 バージョン アップデートスクリプト
 */

/**
 * users テーブル構造変更
 */
if ($this->loadSchema('4.8.0', '', 'users', 'alter')) {
	$this->setUpdateLog('users テーブルの構造変更に成功しました。');
} else {
	$this->setUpdateLog('users テーブルの構造変更に失敗しました。', true);
}

App::uses('User', 'Model');

$User = new User();
$records = $User->find('all', ['recursive' => -1]);
$result = true;
foreach($records as $record) {
	$record['User']['status'] = true;
	if (!$User->save($record)) {
		$result = false;
	}
}
if ($result) {
	$this->setUpdateLog('users テーブルの変換に成功しました。');
} else {
	$this->setUpdateLog('users テーブルの変換に失敗しました。', true);
}
