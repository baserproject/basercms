<?php
/* SVN FILE: $Id$ */
/**
 * 1.5.5 アップデートスクリプト
 * アクセス制限機能実装によるDB変更
 *
 * [メモ]
 * UserモデルにUserGroupモデルをbelongsToで関連付けていたが、
 * SQLite3 の editColumn メソッドでは、内部的にモデルの初期化を行っている為、
 * 初期化時にUserGroupモデルがないというエラーが発生してしまう。
 * editColumn は必ずテーブル生成の後に行う事
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
$db =& ConnectionManager::getDataSource('baser');

/**
 * user_groups テーブルを追加
 */
if($db->createTableSchema(array('model'=>'UserGroup','path'=>BASER_CONFIGS.'sql'))){
	$updateMessage[] = '■ ユーザーグループテーブルの生成に成功しました。';
}else{
	$updateMessage[] = '■ ユーザーグループテーブルの生成に失敗しました。<br />'.
						'手動で user_groups テーブルを生成してください。';
}

/**
 * permissions テーブルを追加
 */
if($db->createTableSchema(array('model'=>'Permission','path'=>BASER_CONFIGS.'sql'))){
	$updateMessage[] = '■ アクセス制限設定テーブルの生成に成功しました。';
}else{
	$updateMessage[] = '■ アクセス制限設定テーブルの生成に失敗しました。<br />'.
						'手動で permissions テーブルを生成してください。';
}

/**
 * 再接続
 */
$db->reconnect($db->config);

/**
 * users テーブルの authority_group を user_group_id にカラム名変更
 */
$userGroupIdCol = array('name'=>'user_group_id','type'=>'integer','length'=>4);
if($db->editColumn('User','authority_group','user_group_id',$userGroupIdCol)){
	$updateMessage[] = '■ ユーザーテーブルのフィールド名称を authority_group から user_group_id に変更しました。';
}else{
	$updateMessage[] = '■ ユーザーテーブルのフィールド名称変更に失敗しました。<br />'.
						' authority_group を user_group_id に手動で変更してください。';
}

/**
 * users テーブルの authority_group を 1 (admin)に書き換え
 */
//$User = ClassRegistry::init('User'); // フィールドリネームしたのでClassRegistryは使わない
App::import('Model','User');
$User = new User();
$users = $User->find('all');
foreach($users as $user){
	$user['User']['user_group_id']='1';
	if($User->save($user)){
		$updateMessage[] = '■ ユーザー: '.$user['User']['name'].' を管理者グループに所属させました。';
	}else{
		$updateMessage[] = '■ ユーザーデータの更新に失敗しました。<br />'.
							'手動で、一人のユーザーを管理者グループに所属させてください。';
	}
}

/**
 * user_groups テーブルの初期データを追加
 */
$UserGroup = ClassRegistry::init('UserGroup');
$datas = array(0=>array('UserGroup'=>array('name'=>'admins','title'=>'管理者')),
			   1=>array('UserGroup'=>array('name'=>'operators','title'=>'運営者')));
$ret = true;
foreach($datas as $data){
	$UserGroup->create($data);
	if(!$UserGroup->save()){
		$ret = false;
	}
}
if($ret){
	$updateMessage[] = '■ ユーザーグループデータの登録に成功しました。';
}else{
	$updateMessage[] = '■ ユーザーグループデータの登録に失敗しました。.<br />'.
						'手動でユーザーグループデータを登録してください。';
}

/**
 * permissions テーブルの初期データを追加
 */
$Permission = ClassRegistry::init('Permission');
$datas = array(0=>array('Permission'=>array('no'=>'1','sort'=>'1','name'=>'アクセス制限設定','user_group_id'=>'2','url'=>'/admin/permissions*','auth'=>'0','status'=>'1')),
				1=>array('Permission'=>array('no'=>'2','sort'=>'2','name'=>'システム設定','user_group_id'=>'2','url'=>'/admin/site_configs*','auth'=>'0','status'=>'1')),
				2=>array('Permission'=>array('no'=>'3','sort'=>'3','name'=>'グローバルメニュー管理','user_group_id'=>'2','url'=>'/admin/global_menus*','auth'=>'0','status'=>'1')),
				3=>array('Permission'=>array('no'=>'4','sort'=>'4','name'=>'プラグイン管理','user_group_id'=>'2','url'=>'/admin/plugins*','auth'=>'0','status'=>'1')),
				4=>array('Permission'=>array('no'=>'5','sort'=>'5','name'=>'ユーザー管理','user_group_id'=>'2','url'=>'/admin/users*','auth'=>'0','status'=>'1')),
				5=>array('Permission'=>array('no'=>'6','sort'=>'6','name'=>'ユーザー編集','user_group_id'=>'2','url'=>'/admin/users/edit*','auth'=>'1','status'=>'1')),
				6=>array('Permission'=>array('no'=>'7','sort'=>'7','name'=>'ユーザー編集','user_group_id'=>'2','url'=>'/admin/users/logout','auth'=>'1','status'=>'1')),
				7=>array('Permission'=>array('no'=>'8','sort'=>'8','name'=>'ブログ管理','user_group_id'=>'2','url'=>'/admin/blog/blog_contents*','auth'=>'0','status'=>'1')),
				8=>array('Permission'=>array('no'=>'9','sort'=>'9','name'=>'ブログ編集','user_group_id'=>'2','url'=>'/admin/blog/blog_contents/edit*','auth'=>'1','status'=>'1')),
				9=>array('Permission'=>array('no'=>'10','sort'=>'10','name'=>'メールフォーム基本設定','user_group_id'=>'2','url'=>'/admin/mail/mail_configs*','auth'=>'0','status'=>'1')),
				10=>array('Permission'=>array('no'=>'11','sort'=>'11','name'=>'メールフォーム管理','user_group_id'=>'2','url'=>'/admin/mail/mail_contents*','auth'=>'0','status'=>'1')),
				11=>array('Permission'=>array('no'=>'12','sort'=>'12','name'=>'メールフォーム編集','user_group_id'=>'2','url'=>'/admin/mail/mail_contents/edit*','auth'=>'1','status'=>'1')),
				12=>array('Permission'=>array('no'=>'13','sort'=>'13','name'=>'フィード管理','user_group_id'=>'2','url'=>'/admin/feed/feed_configs*','auth'=>'0','status'=>'1')),
				14=>array('Permission'=>array('no'=>'14','sort'=>'14','name'=>'ページテンプレート読込','user_group_id'=>'2','url'=>'/admin/pages/entry_page_files','auth'=>'0','status'=>'1')));

$ret = true;
foreach($datas as $data){
	$Permission->create($data);
	if(!$Permission->save()){
		$ret = false;
	}
}
if($ret){
	$updateMessage[] = '■ アクセス制限設定データの登録に成功しました。';
}else{
	$updateMessage[] = '■ アクセス制限設定データの登録に失敗しました。<br />'.
						'手動でアクセス制限設定データを登録してください。';
}

/**
 * 処理完了
 */
$updateMessage[] = '----- データベースの更新が完了しました -----';
$this->log($updateMessage,'install');
?>