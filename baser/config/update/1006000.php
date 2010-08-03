<?php
/* SVN FILE: $Id$ */
/**
 * 1.6.0 バージョン アップデートスクリプト
 *
 * ・ブログ記事一覧の件数を設定できるようにした。
 * ・ブログ記事一覧の並び替え方向を設定できるようにした。
 * ・ウィジェットエリア管理機能を追加。
 * ・メンテナンス機能実装
 * ・Twitter関連はTwitterプラグインに移動
 * ・ページ機能の仕様変更
 *		今までページ名について内部的に拡張子.htmlを自動的に付加する仕様としていたがつけない仕様に変更した。
 * ・ページカテゴリに初期データとして「mobile」を追加。モバイル用のシステムカテゴリとして利用する。
 * ・画象認証機能を追加（ブログ・メール）
 * ・コア、ブログ、メールそれぞれにウィジェットエリアを指定できるようにした。
 *
 * ----------------------------------------
 * 　アップデートスクリプト記述について　
 * ----------------------------------------
 * ・スキーマを更新した際、データ更新時にエラーが出てしまう場合の対処
 *		→ $db->cacheSource = false;
 *		→ モデルはClassRegistryは利用せずそのままイニシャライズする
 * ・新しいテーブルを追加する場合はスキーマを利用する
 *		→ /admin/schemas/write でスキーマファイルの生成ができる
 * ----------------------------------------
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
App::import('Model','SiteConfig');
App::import('Model','Page');
App::import('Model','PageCategory');
App::import('Model','WidgetArea');
App::import('Model','Mail.MailContent');
App::import('Model','Blog.BlogContent');
App::import('Model','Blog.BlogCategory');
/**
 * DB接続を取得
 */
	$db =& ConnectionManager::getDataSource('baser');
	$db->cacheSources = false;
/**
 * 新しいテーブルを追加（ bc_widget_areas ）
 */
	$this->setUpdateMessage('新しいテーブルを追加します。',true, true);
	if($db->createTableSchema(array('model'=>'WidgetArea','path'=>BASER_CONFIGS.'sql'))){
		$this->setUpdateMessage('bc_widget_areas テーブルの生成に成功しました。');
	}else{
		$this->setUpdateMessage('bc_widget_areas テーブルの生成に失敗しました。手動でテーブルを生成してください。');
	}
/**
 * PageCategory 構造変更
 * ・フィールド削除（ no / theme ）
 */
	$this->setUpdateMessage('bc_page_categories テーブルの構造を更新します。',true, true);
	$PageCategory = new PageCategory();
	$cols = array('no', 'theme');
	foreach($cols as $col){
		if($db->deleteColumn($PageCategory, $col)) {
			$this->setUpdateMessage($col.' フィールドを削除しました。');
		}else{
			$this->setUpdateMessage($col.' フィールドの削除に失敗しました。手動で削除してください。');
		}
	}
/**
 * SiteConfig データ更新
 * ・キー追加（ maintenance / widget_area ）
 * ・キー削除（ twitter_username / twitter_view_num ）
 */
	$this->setUpdateMessage('bc_site_configs のテーブルのデータを更新します。',true, true);
	$SiteConfig = new SiteConfig();
	$id = $SiteConfig->field('id',array('SiteConfig.name'=>'twitter_username'));
	if($id){
		if($SiteConfig->del($id)){
			$this->setUpdateMessage('twitter_username フィールドを削除しました。');
		}else{
			$this->setUpdateMessage('twitter_username フィールドの削除に失敗しました。手動で削除してください。');
		}
	}
	$id = $SiteConfig->field('id',array('SiteConfig.name'=>'twitter_count'));
	if($id){
		if($SiteConfig->del($id)){
			$this->setUpdateMessage('twitter_count フィールドを削除しました。');
		}else{
			$this->setUpdateMessage('twitter_count フィールドの削除に失敗しました。手動で削除してください。');
		}
	}
	$SiteConfig->create(array('name'=>'maintenance'));
	if($SiteConfig->save()){
		$this->setUpdateMessage('maintenance フィールドを追加しました。');
	}else{
		$this->setUpdateMessage('maintenance フィールドの追加に失敗しました。手動で追加してください。');
	}
	$SiteConfig->create(array('name'=>'widget_area','value'=>'1'));
	if($SiteConfig->save()){
		$this->setUpdateMessage('widget_area フィールドを追加しました。');
	}else{
		$this->setUpdateMessage('widget_area フィールドの追加に失敗しました。手動で追加してください。');
	}
/**
 * PageCategory データ更新
 * ・mobile カテゴリを追加
 */
	$PageCategory = new PageCategory();
	$this->setUpdateMessage('bc_page_categories のデータを更新します。',true, true);
	if($PageCategory->field('id',array('PageCategory.name'=>'mobile'))){
		$this->setUpdateMessage('bc_page_categories のデータ更新は必要ありませんでした。');
	}else{
		$PageCategory->create(array('name'=>'mobile','title'=>'モバイル','parent_id'=>''));
		if($PageCategory->save()){
			$this->setUpdateMessage('mobile を追加しました。');
		}else{
			$this->setUpdateMessage('mobile を追加に失敗しました。手動で追加してください。');
		}
	}
/**
 * Page データ更新
 * ・別テーマのページを削除
 * ・既存データのページ名に「.html」を追加
 * ・
 */
	$this->setUpdateMessage('bc_pages のデータを更新します。',true, true);
	$theme = $this->siteConfigs['theme'];
	$Page = new Page();
	$Page->recursive = -1;
	$pages = $Page->find('all');
	foreach($pages as $page){
		if($page['Page']['theme'] == $theme){
			if($page['Page']['name']=='index' && !$page['Page']['page_category_id']){
				$page['Page']['url'] = '/index';
				$Page->set($page);
				if($Page->save()){
					$this->setUpdateMessage('トップページの更新に成功しました。');
				}else{
					$this->setUpdateMessage('トップページの更新に失敗しました。手動で更新してください。');
				}
			}else{
				$page['Page']['name'] = $page['Page']['name'].'.html';
				$Page->set($page);
				if($Page->save()){
					$this->setUpdateMessage($page['Page']['url'].' の更新に成功しました。');
				}else{
					$this->setUpdateMessage($page['Page']['url'].' の更新に失敗しました。手動で更新してください。更新する際、ページ名に拡張子をつけてから更新してください。');
				}
			}
		}else{
			if($Page->del($page['Page']['id'])){
				$this->setUpdateMessage('別テーマの '.$page['Page']['url'].' の削除に成功しました。');
			}else{
				$this->setUpdateMessage('別テーマの '.$page['Page']['url'].' の削除に失敗しました。手動で削除してください。');
			}
		}
	}
/**
 * Page 構造変更
 * ・フィールド削除（ no / theme ）
 * ※ 別テーマのページデータの削除完了後である必要あり
 */
	$cols = array('no', 'theme');
	$this->setUpdateMessage('bc_pages テーブルの構造を更新します。',true, true);
	foreach($cols as $col){
		if($db->deleteColumn($Page, $col)) {
			$this->setUpdateMessage($col.' フィールドを削除しました。');
		}else{
			$this->setUpdateMessage($col.' フィールドの削除に失敗しました。手動で削除してください。');
		}
	}
/**
 * WidgetArea データ追加
 */
	$this->setUpdateMessage('bc_widget_areas テーブルにデータを追加します。',true, true);
	$WidgetArea = new WidgetArea();
	$WidgetArea->create(array('name'=>'標準サイドバー'));
	if($WidgetArea->save()){
		$this->setUpdateMessage('標準サイドバーを追加しました。');
	}else{
		$this->setUpdateMessage('標準サイドバーの追加に失敗しました。ウィジェットエリア管理より手動で追加してください。');
	}
/**
 * DB接続を取得
 */
	$db =& ConnectionManager::getDataSource('plugin');
	$db->cacheSources = false;
/**
 * BlogContent 構造変更
 * ・フィールド追加（ list_count / list_direction / auth_captcha / feed_count / widget_area ）
 * ・フィールド削除（ theme ）
 */
	$BlogContent = new BlogContent();
	$this->setUpdateMessage('bc__blog_contents のテーブルの構造を更新します。',true, true);
	$cols = array(	array('name'=>'list_count','type'=>'integer','length'=>4),
					array('name'=>'list_direction', 'type'=>'string', 'length'=>4),
					array('name'=>'auth_captcha', 'type'=>'boolean'),
					array('name'=>'widget_area', 'type'=>'integer','length'=>4),
					array('name'=>'feed_count','type'=>'integer','length'=>4));
	foreach($cols as $col){
		if($db->addColumn($BlogContent,$col['name'],$col)){
			$this->setUpdateMessage($col['name'].' フィールドを追加しました。');
		}else{
			$this->setUpdateMessage($col['name'].' フィールドを追加しようとして失敗しました。手動で追加してください。');
		}
	}
	if($db->deleteColumn($BlogContent, 'theme')){
		$this->setUpdateMessage('theme フィールドを削除しました。');
	} else {
		$this->setUpdateMessage('theme フィールドの削除に失敗しました。手動で削除してください。');
	}
/**
 * MailContent 構造変更
 *
 * フィールド追加（ widget_area / auth_captcha ）
 */
	$MailContent = new MailContent();
	$this->setUpdateMessage('bc__mail_contents テーブルの構造を更新します。',true, true);
	$cols = array(	array('name'=>'auth_captcha', 'type'=>'boolean'),
					array('name'=>'widget_area', 'type'=>'integer','length'=>4));
	foreach($cols as $col){
		if($db->addColumn($MailContent,$col['name'],$col)){
			$this->setUpdateMessage($col['name'].' フィールドを追加しました。');
		}else{
			$this->setUpdateMessage($col['name'].' フィールドを追加しようとして失敗しました。手動で追加してください。');
		}
	}
/**
 * BlogContent データ更新
 */
	$this->setUpdateMessage('bc__blog_contents テーブルのデータを更新します。',true, true);
	$BlogContent = new BlogContent();
	$blogContents = $BlogContent->find('all');
	if($blogContents){
		foreach($blogContents as $blogContent){
			$blogContent['BlogContent']['list_direction'] = 'DESC';
			$blogContent['BlogContent']['list_count'] = 10;
			$blogContent['BlogContent']['feed_count'] = 10;
			$BlogContent->set($blogContent);
			if($BlogContent->save()){
				$this->setUpdateMessage($blogContent['BlogContent']['name'].' の更新に成功しました。');
			}else{
				$this->setUpdateMessage($blogContent['BlogContent']['name'].' の更新に失敗しました。手動で'.$blogContent['BlogContent']['name'].'を更新してください。');
			}
		}
	}