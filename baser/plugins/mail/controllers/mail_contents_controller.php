<?php
/* SVN FILE: $Id$ */
/**
 * メールコンテンツコントローラー
 * 
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2009, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2009, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.plugins.mail.controllers
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
/**
 * メールコンテンツコントローラー
 *
 * @package			baser.plugins.mail.controllers
 */
class MailContentsController extends MailAppController{
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
	var $name = 'MailContents';
/**
 * モデル
 *
 * @var 	array
 * @access 	public
 */
	var $uses = array("Mail.MailContent",'Mail.Message');
/**
 * ヘルパー
 *
 * @var 	array
 * @access 	public
 */
	var $helpers = array('Html','TimeEx','Freeze','TextEx');
/**
 * コンポーネント
 *
 * @var     array
 * @access  public
 */
    var $components = array('Auth','Cookie','AuthConfigure');
/**
 * ぱんくずナビ
 *
 * @var		string
 * @access 	public
 */
	var $navis = array('システム設定'=>'/admin/site_configs/form',
                        'プラグイン設定'=>'/admin/plugins/index',
                        'メールフォーム管理'=>'/admin/mail/mail_contents/index');
/**
 * サブメニューエレメント
 *
 * @var		string
 * @access 	public
 */
	var $subMenuElements = array();
/**
 * [ADMIN] メールフォーム一覧
 *
 * @return  void
 * @access  public
 */
    function admin_index(){

        $listDatas = $this->MailContent->findAll();
        $this->set('listDatas',$listDatas);
        $this->subMenuElements = array('mail_common','plugins');
        $this->pageTitle = 'メールフォーム一覧';

    }
/**
 * [ADMIN] メールフォーム追加
 *
 * @return  void
 * @access  public
 */
    function admin_add(){

        $this->pageTitle = '新規メールフォーム登録';

        if(!$this->data){
            $this->data = $this->MailContent->getDefaultValue();
        }else{

			/* 登録処理 */
            if(!$this->data['MailContent']['sender_1_']){
                $this->data['MailContent']['sender_1'] = '';
            }
			$this->MailContent->create($this->data);

			/* データを保存 */
			if($this->MailContent->save()){

                // 新しいメッセージテーブルを作成
                $this->Message->createTable($this->data['MailContent']['name']);

                $message = '新規メールフォーム「'.$this->data['MailContent']['title'].'」を追加しました。';
				$this->Session->setFlash($message);
				$this->MailContent->saveDbLog($message);
				$this->redirect(array('controller'=>'mail_contents','action'=>'index'));
			}else{
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

        }
        $this->subMenuElements = array('mail_common','plugins');
        $this->render('form');

    }
/**
 * [ADMIN] 編集処理
 *
 @ @param	int		ID
 * @return	void
 * @access 	public
 */
	function admin_edit($id){

		/* 除外処理 */
		if(!$id && empty($this->data)) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action'=>'admin_index'));
		}

		if(empty($this->data)){
			$this->data = $this->MailContent->read(null, $id);
		}else{
            $old = $this->MailContent->read(null,$id);
            if(!$this->data['MailContent']['sender_1_']){
                $this->data['MailContent']['sender_1'] = '';
            }
			/* 更新処理 */
			if($this->MailContent->save($this->data)){

                // メッセージテーブルの名前を変更
                if($old['MailContent']['name'] != $this->data['MailContent']['name']){
                    $this->Message->renameTable($old['MailContent']['name'],$this->data['MailContent']['name']);
                }

                $message = 'メールフォーム「'.$this->data['MailContent']['title'].'」を更新しました。';
				$this->Session->setFlash($message);
				$this->MailContent->saveDbLog($message);
				$this->redirect(array('action'=>'index'));
			}else{
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		/* 表示設定 */
        $this->subMenuElements = array('mail_common','plugins');
		$this->pageTitle = 'メールフォーム設定編集：'.$this->data['MailContent']['title'];
		$this->render('form');

	}

/**
 * [ADMIN] 削除処理
 *
 @ @param	int		ID
 * @return	void
 * @access 	public
 */
	function admin_delete($id = null) {

		/* 除外処理 */
		if(!$id) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action'=>'admin_index'));
		}

		// メッセージ用にデータを取得
		$mailContent = $this->MailContent->read(null, $id);

		/* 削除処理 */
		if($this->MailContent->del($id)) {

            // メッセージテーブルを削除
            $this->Message->deleteTable($mailContent['MailContent']['name']);

            $message = 'メールフォーム「'.$mailContent['MailContent']['title'].'」 を削除しました。';
			$this->Session->setFlash($message);
			$this->MailContent->saveDbLog($message);
            
		}else{
			$this->Session->setFlash('データベース処理中にエラーが発生しました。');
		}

		$this->redirect(array('action'=>'index'));

	}

}
?>