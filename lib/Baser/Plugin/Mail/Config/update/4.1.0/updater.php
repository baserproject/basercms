<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link			https://basercms.net baserCMS Project
 * @package         Baser.Config
 * @since           baserCMS v 4.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * 4.1.0 バージョン アップデートスクリプト
 */

/**
 * mail_contents テーブル構造変更
 */
if($this->loadSchema('4.1.0', 'Mail', 'mail_contents', $filterType = 'alter')) {
	$this->setUpdateLog('mail_contents テーブルの構造変更に成功しました。');
} else {
	$this->setUpdateLog('mail_contents テーブルの構造変更に失敗しました。', true);
}

/**
 * MailMessage テーブルデータ更新
 */
ClassRegistry::flush();
CakePlugin::load('Mail');
$MailContent = ClassRegistry::init('Mail.MailContent');
$mailContents = $MailContent->find('all', ['recursive' => 1]);
App::uses('BcTextHelper', 'View/Helper');
$BcText = new BcTextHelper(new View());
$result = true;
if ($mailContents) {
	foreach ($mailContents as $content) {
		$mailContentId = $content['MailContent']['id'];
		ClassRegistry::flush();
		$MailMessage = ClassRegistry::init('Mail.MailMessage');
		$MailMessage->setup($mailContentId);
		$types = Hash::combine($content['MailField'], '{n}.field_name', '{n}.type');
		$newSources = Hash::combine($content['MailField'], '{n}.field_name', '{n}.source');
		$mailMessages = $MailMessage->find('all');
		foreach ($mailMessages as $message) {
			foreach ($message['MailMessage'] as $key => $oldValue) {
				if (isset($types[$key])) {
					switch ($types[$key]) {
						case 'radio':
						case 'select':
							if (isset($newSources[$key])) {
								$sources = explode("|", $newSources[$key]);
								$i = 0;
								$oldSources = array();
								foreach ($sources as $source) {
									$i++;
									$oldSources[$i] = $source;
								}
								if (isset($oldSources[$oldValue]) && $oldSources[$oldValue]) {
									$message['MailMessage'][$key] = $oldSources[$oldValue];
								} else {
									$message['MailMessage'][$key] = $oldValue;
								}
							}
							break;
						case 'multi_check':
							if (isset($newSources[$key])) {
								$sources = explode("|", $newSources[$key]);
								$i = 0;
								$oldSources = array();
								foreach ($sources as $source) {
									$i++;
									$oldSources[$i] = $source;
								}
								$newValues = array();
								$oldValues = explode("|", $oldValue);
								foreach ($oldValues as $value) {
									if (isset($oldSources[$value]) && $oldSources[$value]) {
										$newValues[] = $oldSources[$value];
									} else {
										$newValues[] = $value;
									}
								}
								$message['MailMessage'][$key] = implode("|", $newValues);
							}
							break;
						case 'pref':
							$message['MailMessage'][$key] = $BcText->pref($oldValue);
							break;
						default:
							break;
					}
				}
			}
			$MailMessage->set($message);
			$result = $MailMessage->save(null, false);
			if($result) {
				$this->setUpdateLog(sprintf(__d('baser', 'メールプラグイン mail_message_ %s テーブルのデータ更新に成功しました。'), $mailContentId));
			} else {
				$this->setUpdateLog(sprintf(__d('baser', 'メールプラグイン mail_message_ %s テーブルのデータ更新に失敗しました。'), $mailContentId), true);
			}
		}
	}
}
