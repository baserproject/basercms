<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Config
 * @since			baserCMS v 4.0.5
 * @license			http://basercms.net/license/index.html
 */

/**
 * 4.0.5 バージョン アップデートスクリプト
 */

/**
 * アップロードデータの保存先フォルダ名変更（ブログコンテンツ）
 */
	$BlogContent = ClassRegistry::init('Blog.BlogContent');
	$blogContents = $BlogContent->find('all');
	
	if($blogContents) {
		$Folder = new Folder();
		foreach($blogContents as $blogContent) {
			$oldPath = WWW_ROOT . 'files' . DS . 'blog' . DS . $blogContent['Content']['name'] . DS;
			$newPath = WWW_ROOT . 'files' . DS . 'blog' . DS . $blogContent['BlogContent']['id'] . DS;
			if(is_dir($oldPath) && !is_dir($newPath)) {
				$Folder->move([
					'to' => $newPath,
					'from' => $oldPath,
					'mode' => 0777
				]);
			}
		}
	}
	
/**
 * アップロードデータの保存先フォルダ名変更（メールコンテンツ）
 */
	$MailContent = ClassRegistry::init('Mail.MailContent');
	$mailContents = $MailContent->find('all');
	
	if($mailContents) {
		$Folder = new Folder();
		foreach($mailContents as $mailContent) {
			$oldPath = WWW_ROOT . 'files' . DS . 'mail' . DS . 'limited' . DS . $mailContent['Content']['name'] . DS;
			$newPath = WWW_ROOT . 'files' . DS . 'mail' . DS . 'limited' . DS . $mailContent['MailContent']['id'] . DS;
			if(is_dir($oldPath) && !is_dir($newPath)) {
				$Folder->move([
					'to' => $newPath,
					'from' => $oldPath,
					'mode' => 0777
				]);
			}
		}
	}