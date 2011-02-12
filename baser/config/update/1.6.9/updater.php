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
 * user_groups スキーマの読み込み
 */
	if(!$this->loadSchema('test', '', 'user_groups')){
		$this->setMessage('user_groups のテーブル構造の更新に失敗しました。', true);
	} else {
		$this->setMessage('user_groups のテーブル構造の更新に成功しました。');
	}
/**
 * user_groups テーブル更新
 *
 * 認証プレフィックスの更新
 */
	App::import('Model', 'UserGroup');
	$UserGroup = new UserGroup();
	$datas = $UserGroup->find('all');
	$result = true;
	if($datas) {
		foreach($datas as $data) {
			$data['UserGroup']['auth_prefix'] = 'admin';
			$UserGroup->set($data);
			if(!$UserGroup->save()) {
				$result = false;
			}
		}
	}
	if($result) {
		$this->setMessage('user_groups テーブルの更新に成功しました。');
	} else {
		$this->setMessage('user_groups テーブルの更新に失敗しました。', true);
	}