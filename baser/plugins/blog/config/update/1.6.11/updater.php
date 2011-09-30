<?php
/* SVN FILE: $Id$ */
/**
 * ブログプラグイン バージョン 1.6.11 アップデートスクリプト
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
 */
	if($this->loadSchema('1.6.11', 'blog')){
		$this->setMessage('ブログプラグインのテーブル構造の更新に成功しました。');
	} else {
		$this->setMessage('ブログプラグインのテーブル構造の更新に失敗しました。', true);
	}
/**
 * ブログコンテンツ更新
 */
	App::import('Model', 'Blog.BlogContent');
 	$BlogContent = new BlogContent();
 	$datas = $BlogContent->find('all');
	$result = true;
	foreach($datas as $data) {
		$data['BlogContent']['tag_use'] = false;
		if($BlogContent->save($data)) {
			continue;
		} else {
			$result = false;
			break;
		}
	}
	if($result){
		$this->setMessage('blog_contents テーブルのデータ更新に成功しました。');
	} else {
		$this->setMessage('blog_contents テーブルのデータ更新に失敗しました。', true);
	}
/**
 * blog_posts 更新
 *
 * 保存処理を行う事で contents テーブルに検索データを追加
 */
	App::import('Model', 'Blog.BlogPost');
	$BlogPost = new BlogPost();
	// contents テーブルに登録する際、ClassRegistry::init() で、BlogCategoryを呼び出すが
	// なぜか、既に登録されている BlogCategory は、actAs が空になっている為、
	// getPath が呼び出せずエラーとなってしまう。
	// 再インスタンス化する為に、一旦削除する
	ClassRegistry::removeObject('BlogCategory');
	$blogPosts = $BlogPost->find('all');
	$result = true;
	foreach($blogPosts as $blogPost) {
		if($BlogPost->save($blogPost)) {
			continue;
		} else {
			$result = false;
			break;
		}
	}
	if($result){
		$this->setMessage('blog_contents > contents テーブルのデータ更新に成功しました。');
	} else {
		$this->setMessage('blog_contents > contents テーブルのデータ更新に失敗しました。', true);
	}