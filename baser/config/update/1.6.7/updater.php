<?php
/* SVN FILE: $Id$ */
/**
 * 1.6.7 バージョン アップデートスクリプト
 *
 * ----------------------------------------
 * 　アップデートの仕様について
 * ----------------------------------------
 * アップデートスクリプトや、スキーマファイルの仕様については
 * 次のファイルに記載されいているコメントを参考にしてください。
 *
 * /baser/controllers/updaters_controller.php
 *
 * スキーマ変更後、データの更新を行う場合は、ClassRegistry を利用せず、
 * モデルクラスを直接イニシャライズしないと、
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
 * スキーマの読み込み
 */
	if(!$this->loadSchema('1.6.7')){
		$this->setMessage('データベース構造の更新に失敗しました。', true);
	} else {
		$this->setMessage('データベース構造の更新に成功しました。');
	}
/**
 * plugins テーブルの更新
 *
 * ■ 全てのプラグインを無効化
 * ■ 各プラグインのテーブルを確認し、存在すれば、plugins テーブルに無効状態でレコードを追加
 * ■ このバージョンより、plugins にレコードがある＝プラグイン用のテーブルが存在するという仕様となる
 *		※ 無効の場合でもプラグイン用のテーブルを残す仕様の為
 */
	$db =& ConnectionManager::getDataSource('plugin');
	$db->cacheQueries = false;
	$listSources = $db->listSources();
	$prefix = $db->config['prefix'];
	$pluginTables = array('blog'=>$prefix.'blog_contents',
							'mail'=>$prefix.'mail_contents',
							'feed'=>$prefix.'feed_configs',
							'uploader'=>$prefix.'uploader_files',
							'twitter'=>$prefix.'twitter_configs');
	$tableExistsPlugins = array();
	foreach($pluginTables as $key => $table) {
		if(in_array($table, $listSources)) {
			$tableExistsPlugins[] = $key;
		}
	}
	App::import('Model', 'Plugin');
	$Plugin = new Plugin();
	$result = true;
	foreach($tableExistsPlugins as $plugin) {
		$data = $Plugin->find('first', array('conditions'=>array('name'=>$plugin)));
		$appPath = APP.'plugins'.DS.$plugin.DS.'config'.DS.'config.php';
		$baserPath = BASER_PLUGINS.$plugin.DS.'config'.DS.'config.php';
		$path = '';
		if(file_exists($appPath)) {
			$path = $appPath;
		}elseif(file_exists($baserPath)) {
			$path = $baserPath;
		}
		$data['Plugin']['name'] = $plugin;
		$data['Plugin']['status'] = false;
		if($path) {
			include $path;
			if(isset($title))
				$data['Plugin']['title'] = $title;
		}
		$version = $this->getBaserVersion($plugin);
		if($version && !preg_match('/^Baser/', $version)) {
			$data['Plugin']['version'] = $version;
		}
		if(!$Plugin->save($data)) {
			$result = false;
		}
	}
	if($result) {
		$this->setMessage('plugins テーブルの更新に成功しました。');
	} else {
		$this->setMessage('plugins テーブルの更新に失敗しました。', true);
	}
/**
 * プラグインの有効化を促すメッセージを追加
 */
	$this->setMessage('全てのプラグインは一時的に無効状態になっています。', true);
	$this->setMessage('プラグイン一覧より、必要なプラグインの有効化を行ってください。', true);