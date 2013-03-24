<?php
/* SVN FILE: $Id$ */
/**
 * 2.1.0 バージョン アップデートスクリプト
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
 * スキーマ読み込み
 *
 * editor_templates テーブルの作成
 */
	if($this->loadSchema('2.1.0', '', '', 'create')){
		$this->setUpdateLog('editor_templates テーブルの作成に成功しました。');
	} else {
		$this->setUpdateLog('editor_templates テーブルの作成に失敗しました。', true);
	}
/**
 * pages / page_categories テーブルの構造変更
 */
	if($this->loadSchema('2.1.0', '', '', 'alter')){
		$this->setUpdateLog('users / plugins / pages / page_categories テーブルの構造変更に成功しました。');
	} else {
		$this->setUpdateLog('users / pages / plugins / page_categories テーブルの構造変更に失敗しました。', true);
	}
/**
 * CSV読み込み 
 */
	if($this->loadCsv('2.1.0')) {
		$this->setUpdateLog('editor_templates テーブルの初期データ読み込みに成功しました。');
	} else {
		$this->setUpdateLog('editor_templates テーブルの初期データ読み込みに失敗しました。', true);
	}
/**
 * site_configs データ更新
 */
	App::import('Model', 'SiteConfig');
	$SiteConfig = new SiteConfig();
	$siteConfig = $SiteConfig->findExpanded();
	$siteConfig['root_layout_template'] = 'default';
	$siteConfig['root_layout_template_mobile'] = 'default';
	$siteConfig['root_layout_template_smartphone'] = 'default';
	$siteConfig['root_content_template'] = 'default';
	$siteConfig['root_content_template_mobile'] = 'default';
	$siteConfig['root_content_template_smartphone'] = 'default';
	if($SiteConfig->saveKeyValue($siteConfig)) {
		$this->setUpdateLog('site_configs テーブルのデータ更新に成功しました。');
	} else {
		$this->setUpdateLog('site_configs テーブルのデータ更新に失敗しました。', true);
	}
/**
 * plugins データ更新 
 */
	App::import('Model', 'Plugin');
	$Plugin = new Plugin();
	$datas = $Plugin->find('all', array('conditions' => array('Plugin.status' => true)));
	$result = true;
	if($datas) {
		foreach($datas as $data) {
			$data['Plugin']['db_inited'] = true;
			$Plugin->set($data);
			if(!$Plugin->save()) {
				$result = false;
			}
		}
		
	}
	if($result) {
		$this->setUpdateLog('plugins テーブルのデータ更新に成功しました。');
	} else {
		$this->setUpdateLog('plugins テーブルのデータ更新に失敗しました。', true);
	}