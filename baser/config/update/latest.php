<?php
/* SVN FILE: $Id$ */
/**
 * 次期バージョン アップデートスクリプト
 *
 * ブログ記事一覧の件数を設定できるようにした。
 * ブログ記事一覧の並び替え方向を設定できるようにした。
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
 * DB接続を取得
 */
	$db =& ConnectionManager::getDataSource('plugin');

/**
 * bc__blog_contents テーブル フィールド追加
 */
	$this->setUpdateMessage('blog_contents テーブルの構造を更新します。',true, true);
	$cols = array(array('name'=>'list_count','type'=>'integer','length'=>4),
				array('name'=>'list_direction', 'type'=>'string', 'length'=>4));
	foreach($cols as $col){
		if($db->addColumn('BlogContents',$col['name'],$col)){
			$this->setUpdateMessage($col['name'].' フィールドを追加しました。');
		}else{
			$this->setUpdateMessage($col['name'].' フィールドを追加しようとして失敗しました。<br />'.
								$col['name'].' フィールドを手動で変更してください。');
		}
	}

/**
 * 再接続
 */
	$db->reconnect($db->config);

/**
 * bc__blog_contents テーブル データ更新
 */
	$this->setUpdateMessage('blog_contents テーブルのデータを更新します。',true, true);
	App::import('Model', 'Blog.BlogContent');
	$BlogContent = new BlogContent();
	$blogContents = $BlogContent->find('all');
	if($blogContents){
		foreach($blogContents as $blogContent){
			$blogContent['BlogContent']['list_direction'] = 'DESC';
			$blogContent['BlogContent']['list_count'] = 10;
			$BlogContent->set($blogContent);
			if($BlogContent->save()){
				$this->setUpdateMessage($blogContent['BlogContent']['name'].' の更新に成功しました。');
			}else{
				$this->setUpdateMessage($blogContent['BlogContent']['name'].' の更新に失敗しました。<br />手動で'.$blogContent['BlogContent']['name'].'を更新してください。');
			}
		}
	}