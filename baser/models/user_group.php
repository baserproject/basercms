<?php
/* SVN FILE: $Id$ */
/**
 * ユーザーグループモデル
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
 * @package			baser.models
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
 * ユーザーグループモデル
 *
 * @package			baser.models
 */
class UserGroup extends AppModel {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
	var $name = 'UserGroup';
/**
 * データベース接続
 *
 * @var     string
 * @access  public
 */
	var $useDbConfig = 'baser';
/**
 * hasMany
 *
 * @var		array
 * @access 	public
 */
	var $hasMany = array('Permission'=>
			array('className'=>'Permission',
							'order'=>'id',
							'foreignKey'=>'user_group_id',
							'dependent'=>true,
							'exclusive'=>false,
							'finderQuery'=>''),
			'User'=>
			array('className'=>'User',
							'order'=>'id',
							'foreignKey'=>'user_group_id',
							'dependent'=>false,
							'exclusive'=>false,
							'finderQuery'=>''));
/**
 * バリデーション
 *
 * @var		array
 * @access	public
 */
	var $validate = array(
		'name' => array(
			array(	'rule'		=> array('notEmpty'),
					'message'	=> 'ユーザーグループ名を入力してください。'),
			array(	'rule'		=> array('halfText'),
					'message'	=> 'ユーザーグループ名は半角のみで入力してください。'),
			array(	'rule'		=> array('duplicate','name'),
					'message'	=> '既に登録のあるユーザーグループ名です。'),
			array(	'rule'		=> array('maxLength', 50),
					'message'	=> 'ユーザーグループ名は50文字以内で入力してください。')
		),
		'title' => array(
			array(	'rule'		=> array('notEmpty'),
					'message'	=> '表示名を入力してください。'),
			array(	'rule'		=> array('maxLength', 50),
					'message'	=> '表示名は50文字以内で入力してください。')

		),
		'auth_prefix' => array(
			array(	'rule'		=> array('notEmpty'),
					'message'	=> '認証プレフィックスを入力してください。')
		)
	);
/**
 * 関連するユーザーを管理者グループに変更し保存する
 * @param <type> $cascade
 * @return <type>
 */
	function beforeDelete($cascade = true) {
		parent::beforeDelete($cascade);
		$ret = true;
		if(!empty($this->data['UserGroup']['id'])){
			$id = $this->data['UserGroup']['id'];
			$this->User->unBindModel(array('belongsTo'=>array('UserGroup')));
			$datas = $this->User->find('all',array('conditions'=>array('User.user_group_id'=>$id)));
			if($datas) {
				foreach($datas as $data) {
					$data['User']['user_group_id'] = 1;
					$this->User->set($data);
					if(!$this->User->save()) {
						$ret = false;
					}
				}
			}
		}
		return $ret;
	}
/**
 * 管理者グループ以外のグループが存在するかチェックする
 * @return	boolean
 * @access	void
 */
	function checkOtherAdmins(){
		if($this->find('first',array('conditions'=>array('UserGroup.id <>'=>1)))) {
			return true;
		}else {
			return false;
		}
	}
/**
 * 認証プレフィックスを取得する
 *
 * @param	int	$id
 * @return	string
 * @access	public
 */
	function getAuthPrefix($id) {
		
		$data = $this->find('first', array(
			'conditions'=>array('UserGroup.id'=>$id),
			'fields'=>array('UserGroup.auth_prefix'),
			'recursive'=>-1
		));
		if(isset($data['UserGroup']['auth_prefix'])) {
			return $data['UserGroup']['auth_prefix'];
		} else {
			return '';
		}
		
	}
}
?>