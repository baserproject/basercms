<?php
/* SVN FILE: $Id$ */
/**
 * ブログプラグイン バージョン 1.6.14 アップデートスクリプト
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
 * @package			blog.config.update
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * スキーマの読み込み
 */
	if($this->loadSchema('1.6.14', 'blog')){
		$this->setMessage('blog_posts / blog_contents テーブル構造変更に成功しました。');
	} else {
		$this->setMessage('blog_posts / blog_contents テーブル構造変更に失敗しました。', true);
	}
/**
 * blog_contents データ更新
 * 
 * blog_contents 自体のデータ更新はないが、連動して contents のデータ更新を行う
 */
	$result = true;
	App::import('Model', 'Blog.BlogContent');
	$BlogContent = new BlogContent();
	$blogContents = $BlogContent->find('all', array('cache' => false));
	if($blogContents) {
		foreach($blogContents as $blogContent) {
			$BlogContent->set($BlogContent);
			if($BlogContent->save()) {
				$result = false;
			}
		}
		if($result) {
			$this->setMessage('blog_contents テーブルのデータ更新に成功しました。');
		} else {
			$this->setMessage('blog_contents テーブルのデータ更新に失敗しました。', true);
		}
	}