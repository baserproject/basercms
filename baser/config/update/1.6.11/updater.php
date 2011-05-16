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
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
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
 * contents スキーマの読み込み
 *
 * contents テーブルの作成
 */
	if(!$this->loadSchema('1.6.11', '', 'contents')){
		$this->setMessage('contents テーブル構造の作成に失敗しました。', true);
	} else {
		$this->setMessage('contents テーブル構造の作成に成功しました。');
	}
/**
 * pages 更新
 *
 * 保存処理を行う事で contents テーブルに検索データを追加
 */
	$Page = ClassRegistry::init('Page');
	$pages = $Page->find('all');
	$result = true;
	foreach($pages as $page) {
		if($Page->save($page)) {
			continue;
		} else {
			$result = false;
			break;
		}
	}
	if($result){
		$this->setMessage('pages > contents テーブルのデータ更新に成功しました。');
	} else {
		$this->setMessage('pages > contents テーブルのデータ更新に失敗しました。', true);
	}
/**
 * site_configs 更新
 */
	App::import('Model', 'SiteConfig');
	$SiteConfig = new SiteConfig();
	$siteConfig = $SiteConfig->findExpanded();
	$siteConfig['admin_list_num'] = '10';
	if($SiteConfig->saveKeyValue($siteConfig)) {
		$this->setMessage('site_configs テーブルの更新に成功しました。');
	} else {
		$this->setMessage('site_configs テーブルの更新に失敗しました。', true);
	}