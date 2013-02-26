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
		$this->setUpdateLog('favorites テーブルの作成に成功しました。');
	} else {
		$this->setUpdateLog('favorites テーブルの作成に失敗しました。', true);
	}
/**
 * スキーマの読み込み
 *
 * user_groups テーブルの構造変更
 */
	if($this->loadSchema('2.0.0', '', '', 'alter')){
		$this->setUpdateLog('user_groups テーブルの構造変更に成功しました。');
	} else {
		$this->setUpdateLog('user_groups テーブルの構造変更に失敗しました。', true);
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
		$this->setUpdateLog('site_configs テーブルのデータ更新に成功しました。');
	} else {
		$this->setUpdateLog('site_configs テーブルのデータ更新に失敗しました。', true);
	}
/**
 * global_menus データ更新
 */
	App::import('Model', 'GlobalMenu');
	$GlobalMenu = new GlobalMenu();
	$datas = $GlobalMenu->find('all', array('cache' => false));
	if($datas) {
		$result = true;
		foreach($datas as $data) {
			if($data['GlobalMenu']['menu_type'] == 'admin') {
				if(!$GlobalMenu->delete($data['GlobalMenu']['id'])) {
					$result = false;
				}
			}
		}
		if($result) {
			$this->setUpdateLog('global_menus テーブルのデータ更新に成功しました。');
		} else {
			$this->setUpdateLog('global_menus テーブルのデータ更新に失敗しました。', true);
		}
	}
/**
 * permissions データ更新
 */
	App::import('Model', 'Permission');
	App::import('Model', 'UserGroup');
	$UserGroup = new UserGroup();
	$Permission = new Permission();
	$userGroups = $UserGroup->find('all', array('cache' => false, 'recursive' => -1));
	if($userGroups) {
		$result = true;
		foreach($userGroups as $userGroup) {
			$permission = array('Permission' => array(
				'no'	=> $Permission->getMax('no', array('Permission.user_group_id' => $userGroup['UserGroup']['id']))+1,
				'sort'	=> $Permission->getMax('sort', array('Permission.user_group_id' => $userGroup['UserGroup']['id']))+1,
				'name'	=> 'よく使う項目',
				'user_group_id'	=> $userGroup['UserGroup']['id'],
				'url'	=> '/admin/favorites/*',
				'auth'	=> true,
				'status'=> true
			));
			$Permission->create($permission);
			if(!$Permission->save()) {
				$result = false;
			}
		}
		if($result) {
			$this->setUpdateLog('permissions テーブルのデータ更新に成功しました。');
		} else {
			$this->setUpdateLog('permissions テーブルのデータ更新に失敗しました。', true);
		}
	}
/**
 * blog_contents データ更新
 */
	App::import('Model', 'Blog.BlogContent');
	$BlogContent = new BlogContent();
	$blogContents = $BlogContent->find('all', array('cache' => false));
	if($blogContents) {
		$result = true;
		foreach($blogContents as $blogContent) {
			$blogContent['BlogContent']['status'] = true;
			$BlogContent->set($blogContent);
			if(!$BlogContent->save()) {
				$result = false;
			}
		}
		if($result) {
			$this->setUpdateLog('blog_contents テーブルのデータ更新に成功しました。');
		} else {
			$this->setUpdateLog('blog_contents テーブルのデータ更新に失敗しました。', true);
		}
	}
/**
 * mail_contents データ更新
 */
	App::import('Model', 'Mail.MailContent');
	$MailContent = new MailContent();
	$mailContents=  $MailContent->find('all', array('cache' => false));
	if($mailContents) {
		$result = true;
		foreach($mailContents as $mailContent) {
			$mailContent['MailContent']['status'] = true;
			$MailContent->set($mailContent);
			if(!$MailContent->save()) {
				$result = false;
			}
		}
		if($result) {
			$this->setUpdateLog('blog_contents テーブルのデータ更新に成功しました。');
		} else {
			$this->setUpdateLog('blog_contents テーブルのデータ更新に失敗しました。', true);
		}
	}