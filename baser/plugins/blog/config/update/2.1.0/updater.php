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
 * @package			baser.plugins.blog.config.update
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * スキーマ読み込み
 *
 * blog_posts / blog_contents テーブルの構造変更
 */
	if($this->loadSchema('2.1.0', 'blog', '', 'alter')){
		$this->setUpdateLog('blog_posts / blog_contents テーブルの構造変更に成功しました。');
	} else {
		$this->setUpdateLog('blog_posts / blog_contents テーブルの構造変更に失敗しました。', true);
	}
/**
 * データ更新 
 */
	App::import('Model', 'Blog.BlogContent');
	$BlogContent = new BlogContent();
	$datas = $BlogContent->find('all', array('recursive' => -1));
	if($datas) {
		$result = true;
		foreach($datas as $data) {
			$data['BlogContent']['eye_catch_size'] = 'a:4:{s:11:"thumb_width";s:3:"300";s:12:"thumb_height";s:3:"300";s:18:"mobile_thumb_width";s:3:"100";s:19:"mobile_thumb_height";s:3:"100";}';
			$data['BlogContent']['use_content'] = true;
			if(!$BlogContent->save($data)) {
				$result = false;
			}
		}
		if($result) {
			$this->setUpdateLog('blog_contents テーブルのデータ更新に成功しました。');
		} else {
			$this->setUpdateLog('blog_contents テーブルのデータ更新に失敗しました。', true);
		}
	}