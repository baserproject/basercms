<?php
/* SVN FILE: $Id$ */
/**
 * 2.0.0 バージョン アップデートスクリプト
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
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.config.update
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * スキーマの読み込み
 *
 * favorites テーブルの作成
 */
	if($this->loadSchema('2.0.0', '', '', 'create')){
		$this->setMessage('favorites テーブルの作成に成功しました。');
	} else {
		$this->setMessage('favorites テーブルの作成に失敗しました。', true);
	}
/**
 * スキーマの読み込み
 *
 * user_groups テーブルの構造変更
 */
	if($this->loadSchema('2.0.0', '', '', 'alter')){
		$this->setMessage('user_groups テーブルの構造変更に成功しました。');
	} else {
		$this->setMessage('user_groups テーブルの構造変更に失敗しました。', true);
	}
/**
 * site_configs データ更新
 */
	App::import('Model', 'SiteConfig');
	$SiteConfig = new SiteConfig();
	$siteConfig = $SiteConfig->findExpanded();
	$siteConfig['login_credit'] = true;
	$siteConfig['admin_theme'] = '';
	if($SiteConfig->saveKeyValue($siteConfig)) {
		$this->setMessage('site_configs テーブルのデータ更新に成功しました。');
	} else {
		$this->setMessage('site_configs テーブルのデータ更新に失敗しました。', true);
	}