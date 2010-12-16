<?php
/* SVN FILE: $Id$ */
/**
 * バージョン アップデートスクリプト
 *
 * ----------------------------------------
 * 　アップデートの仕様について
 * ----------------------------------------
 * アップデートスクリプトや、スキーマファイルの仕様については
 * 次のファイルに記載されいているコメントを参考にしてください。
 *
 * /baser/controllers/updaters_controller.php
 *
 * スキーマ変更後、モデルを利用してデータの更新を行う場合は、
 * ClassRegistry を利用せず、モデルクラスを直接イニシャライズしないと、
 * スキーマのキャッシュが古いままとなるので注意が必要です。
 *
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.config.update
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * pages スキーマの読み込み
 */
	if(!$this->loadSchema('test', '', 'pages')){
		$this->setMessage('pages のテーブル構造の更新に失敗しました。', true);
	} else {
		$this->setMessage('pages のテーブル構造の更新に成功しました。');
	}
/**
 * pages テーブル更新
 *
 * 作成者のデータを追加
 */
	$db =& ConnectionManager::getDataSource('plugin');
	$db->cacheQueries = false;
	App::import('Model', 'Plugin');
	$Page = new Page();
	$pages = $Page->find('all');
	$result = true;
	if($pages) {
		foreach($pages as $page) {
			$page['Page']['author_id'] = 1;
			$Page->set($page);
			if(!$Page->save()) {
				$result = false;
			}
		}
	}
	if($result) {
		$this->setMessage('pages テーブルの更新に成功しました。');
	} else {
		$this->setMessage('pages テーブルの更新に失敗しました。', true);
	}
/**
 * site_configs 更新
 */
	App::import('Model', 'SiteConfig');
	App::import('Model', 'Mail.MailConfig');
	$MailConfig = new MailConfig();
	$mailConfig = $MailConfig->read(null, 1);
	$SiteConfig = new SiteConfig();
	$siteConfig = $SiteConfig->findExpanded();
	$siteConfig['mail_encode'] = 'ISO-2022-JP';
	if($mailConfig) {
		$siteConfig['smtp_host'] = $mailConfig['MailConfig']['smtp_host'];
		$siteConfig['smtp_user'] = $mailConfig['MailConfig']['smtp_username'];
		$siteConfig['smtp_password'] = $mailConfig['MailConfig']['smtp_password'];
	} else {
		$siteConfig['smtp_host'] = '';
		$siteConfig['smtp_user'] = '';
		$siteConfig['smtp_password'] = '';
	}
	$siteConfig['formal_name'] = $siteConfig['name'];
	if($SiteConfig->saveKeyValue($siteConfig)) {
		$this->setMessage('site_configs テーブルの更新に成功しました。');
	} else {
		$this->setMessage('site_configs テーブルの更新に失敗しました。', true);
	}
/**
 * mail_configs スキーマの読み込み
 */
	if(!$this->loadSchema('test', 'mail', 'mail_configs')){
		$this->setMessage('mail_configs のテーブル構造の更新に失敗しました。', true);
	} else {
		$this->setMessage('mail_configs のテーブル構造の更新に成功しました。');
	}
/**
 * blog_posts スキーマの読み込み
 */
	if(!$this->loadSchema('test', 'blog', 'blog_posts')){
		$this->setMessage('blog_posts のテーブル構造の更新に失敗しました。', true);
	} else {
		$this->setMessage('blog_posts のテーブル構造の更新に成功しました。');
	}