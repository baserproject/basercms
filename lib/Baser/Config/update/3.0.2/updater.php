<?php
/**
 * 3.0.2 バージョン アップデートスクリプト
 *
 * ----------------------------------------
 * 　アップデートの仕様について
 * ----------------------------------------
 * アップデートスクリプトや、スキーマファイルの仕様については
 * 次のファイルに記載されいているコメントを参考にしてください。
 *
 * /lib/Baser/Controllers/UpdatersController.php
 *
 * スキーマ変更後、モデルを利用してデータの更新を行う場合は、
 * ClassRegistry を利用せず、モデルクラスを直接イニシャライズしないと、
 * スキーマのキャッシュが古いままとなるので注意が必要です。
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * plugins テーブル変更
 */
	if($this->loadSchema('3.0.2', '', 'plugins', $filterType = 'alter')) {
		$this->setUpdateLog('plugins テーブルの構造変更に成功しました。');
	} else {
		$this->setUpdateLog('plugins テーブルの構造変更に失敗しました。', true);
	}
	
/**
 * シリアライズされたデータを更新
 * 
 * UserGroup.default_favorites
 */
	App::uses('UserGroup', 'Model');
	$UserGroup = new UserGroup();
	$datas = $UserGroup->find('all', array('recursive' => -1));
	$result = true;
	foreach($datas as $data) {
		$data['UserGroup']['default_favorites'] = BcUtil::serialize(unserialize($data['UserGroup']['default_favorites']));
		if(!$UserGroup->save($data)) {
			$result = false;
		}
	}
	if($result){
		$this->setUpdateLog('user_groups テーブルの変換に成功しました。');
	} else {
		$this->setUpdateLog('user_groups テーブルの変換に失敗しました。', true);
	}

/**
 * シリアライズされたデータを更新
 * 
 * SiteConfig.content_categories
 * SiteConfig.content_types
 */
	App::uses('SiteConfig', 'Model');
	$SiteConfig = new SiteConfig();
	$data = $SiteConfig->findExpanded('all', array('recursive' => -1));
	$data['content_categories'] = BcUtil::serialize(unserialize($data['content_categories']));
	$data['content_types'] = BcUtil::serialize(unserialize($data['content_types']));
	if($SiteConfig->saveKeyValue($data)) {
		$this->setUpdateLog('site_configs テーブルの変換に成功しました。');
	} else {
		$this->setUpdateLog('site_configs テーブルの変換に失敗しました。', true);
	}
	
/**
 * シリアライズされたデータを更新
 *
 * WidgetArea.widgets
 */
	App::uses('WidgetArea', 'Model');
	$WidgetArea = new WidgetArea();
	$datas = $WidgetArea->find('all', array('recursive' => -1));
	$result = true;
	foreach($datas as $data) {
		$data['WidgetArea']['widgets'] = BcUtil::serialize(unserialize($data['WidgetArea']['widgets']));
		if(!$WidgetArea->save($data)) {
			$result = false;
		}
	}
	if($result){
		$this->setUpdateLog('widget_areas テーブルの変換に成功しました。');
	} else {
		$this->setUpdateLog('widget_areas テーブルの変換に失敗しました。', true);
	}
	
/**
 * シリアライズされたデータを更新
 * 
 * BlogContent.eye_catch_size
 */
	CakePlugin::load('Blog');
	App::uses('BlogContent', 'Blog.Model');
	
	$BlogContent = new BlogContent();
	$datas = $BlogContent->find('all', array('recursive' => -1));
	$result = true;
	foreach($datas as $data) {
		$data['BlogContent']['eye_catch_size'] = BcUtil::serialize(unserialize($data['BlogContent']['eye_catch_size']));
		if(!$BlogContent->save($data)) {
			$result = false;
		}
	}
	if($result){
		$this->setUpdateLog('blog_contents テーブルの変換に成功しました。');
	} else {
		$this->setUpdateLog('blog_contents テーブルの変換に失敗しました。', true);
	}