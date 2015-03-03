<?php
/**
 * 3.0.6 バージョン アップデートスクリプト
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
 * @since			baserCMS v 3.0.6
 * @license			http://basercms.net/license/index.html
 */

/**
 * 管理システム用アセットファイルをデプロイ
 */
	App::uses('BcManagerComponent', 'Controller/Component');
	$BcManager = new BcManagerComponent(new ComponentCollection());
	$BcManager->deployAdminAssets();

/**
 * プラグインの優先度の振り直し
 */
	App::uses('Plugin', 'Model');
	if(ClassRegistry::isKeySet('Plugin')) {
		$Plugin = ClassRegistry::getObject('Plugin');
	} else {
		$Plugin = ClassRegistry::init('Plugin');
	}
	$Plugin->rearrangePriorities();
	
/**
 * テーマ内の管理画面用のアセットファイルを削除する
 */
	$path = getViewPath();
	$paths = array(
		$path . 'css' . DS . 'admin',
		$path . 'js' . DS . 'admin',
		$path . 'img' . DS . 'admin'
	);
	$Folder = new Folder();
	foreach($paths as $path) {
		if(is_dir($path)) {
			$Folder->delete($path);
		}
	}
	