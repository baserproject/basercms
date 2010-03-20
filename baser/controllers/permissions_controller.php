<?php
/* SVN FILE: $Id$ */
/**
 * アクセス制限設定コントローラー
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
 * @package			baser.controllers
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * アクセス制限設定コントローラー
 *
 * @package			baser.controllers
 */
class PermissionsController extends AppController {
/**
 * クラス名
 *
 * @var     string
 * @access  public
 */    
    var $name = 'Permissions';
/**
 * モデル
 *
 * @var 	array
 * @access 	public
 */
	var $uses = array('Permission');
/**
 * コンポーネント
 *
 * @var     array
 * @access  public
 */
    var $components = array('Auth','Cookie','AuthConfigure');
/**
 * ヘルパ
 *
 * @var 	array
 * @access 	public
 */
	var $helpers = array('Time','Freeze');
/**
 * サブメニューエレメント
 *
 * @var 	array
 * @access 	public
 */
	var $subMenuElements = array('users','user_groups','permissions');
/**
 * ぱんくずナビ
 *
 * @var		string
 * @access 	public
 */
	var $navis = array('ユーザー管理'=>'/admin/users/index',
						'ユーザーグループ管理'=>'/admin/user_group/index',
                        'アクセス制限設定管理'=>'/admin/permissions/index');
/**
 * アクセス制限設定の一覧を表示する
 *
 * @return  void
 * @access  public
 */
    function admin_index($userGroupId=null){

		/* セッション処理 */
		if($userGroupId){
			$this->Session->write('Filter.Permission.user_group_id',$userGroupId);
			$this->data['Permission']['user_group_id'] = $userGroupId;
		}elseif(!empty($this->params['url']['user_group_id'])){
            $this->Session->write('Filter.Permission.user_group_id',$this->params['url']['user_group_id']);
			$this->data['Permission']['user_group_id'] = $this->params['url']['user_group_id'];
        }else{
            if($this->Session->check('Filter.Permission.user_group_id')){
                $this->data['Permission']['user_group_id'] = $this->Session->read('Filter.Permission.user_group_id');
            }else{
                $this->Session->del('Filter.Permission.user_group_id');
                $this->Permission->getMax('user_group_id',array('id <>'=>1));
                $this->data['Permission']['user_group_id'] = $this->Permission->getMax('user_group_id',array('id <>'=>1));;
            }
        }

		/* 並び替え処理 */
		if(!empty($this->params['named']['sortup'])){
			$this->Permission->sortup($this->params['named']['sortup'],array('Permission.user_group_id'=>$this->data['Permission']['user_group_id']));
		}
		if(!empty($this->params['named']['sortdown'])){
			$this->Permission->sortdown($this->params['named']['sortdown'],array('Permission.user_group_id'=>$this->data['Permission']['user_group_id']));
		}

		/* 条件を生成 */
        $conditions = array();
        if(!empty($this->data['Permission']['user_group_id'])){
            $conditions['Permission.user_group_id'] = $this->data['Permission']['user_group_id'];
        }

		/* データ取得 */
		$listDatas = $this->Permission->find('all',array('conditions'=>$conditions, 'order'=>'Permission.sort'));
		
		/* 表示設定 */
        $this->set('listDatas',$listDatas);
		$this->pageTitle = 'アクセス制限設定一覧';

    }
/**
 * [ADMIN] 登録処理
 *
 * @return  void
 * @access  public
 */
    function admin_add(){

        if(!$this->data){
			$this->data = $this->Permission->getDefaultValue();
		}else{
			/* 登録処理 */
			$this->data['Permission']['no'] = $this->Permission->getMax('no',array('user_group_id'=>$this->data['Permission']['user_group_id']))+1;
			$this->data['Permission']['sort'] = $this->Permission->getMax('sort',array('user_group_id'=>$this->data['Permission']['user_group_id']))+1;
			$this->Permission->create($this->data);
			if($this->Permission->save()){
                $this->deleteViewCache();
                $message = '新規アクセス制限設定「'.$this->data['Permission']['name'].'」を追加しました。';
				$this->Session->setFlash($message);
				$this->Permission->saveDbLog($message);
				$this->redirect(array('action'=>'index'));
			}else{
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

        }

        /* 表示設定 */
        $this->pageTitle = '新規アクセス制限設定登録';
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
		if(!$id) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action'=>'admin_index'));
		}

		if(empty($this->data)){
			$this->data = $this->Permission->read(null, $id);
		}else{

			/* 更新処理 */
			if($this->Permission->save($this->data)){
                $message = 'アクセス制限設定「'.$this->data['Permission']['name'].'」を更新しました。';
				$this->Session->setFlash($message);
				$this->Permission->saveDbLog($message);
				$this->redirect(array('action'=>'index'));
			}else{
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		/* 表示設定 */
        $this->pageTitle = 'アクセス制限設定編集：'.$this->data['Permission']['name'];
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
		$post = $this->Permission->read(null, $id);

		/* 削除処理 */
		if($this->Permission->del($id)) {
            $message = 'アクセス制限設定「'.$post['Permission']['name'].'」 を削除しました。';
			$this->Session->setFlash($message);
			$this->Permission->saveDbLog($message);
		}else{
			$this->Session->setFlash('データベース処理中にエラーが発生しました。');
		}

		$this->redirect(array('action'=>'index'));

	}

}
?>