<?php
/* SVN FILE: $Id$ */
/**
 * 1.6.12 バージョン アップデートスクリプト
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
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2011, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.config.update
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * contents スキーマの読み込み
 *
 * contents テーブルの作成
 */
	if(!$this->loadSchema('1.6.12')){
		$this->setMessage('page_categories / dblogs テーブルの構造変更に失敗しました。', true);
	} else {
		$this->setMessage('page_categories / dblogs テーブルの構造変更に成功しました。');
	}
/**
 * site_configs 更新
 */
	App::import('Model', 'SiteConfig');
	$SiteConfig = new SiteConfig();
	$siteConfig = $SiteConfig->findExpanded();
	$siteConfig['root_owner_id'] = '';
	$siteConfig['category_permission'] = '';
	if($SiteConfig->saveKeyValue($siteConfig)) {
		$this->setMessage('site_configs テーブルの更新に成功しました。');
	} else {
		$this->setMessage('site_configs テーブルの更新に失敗しました。', true);
	}