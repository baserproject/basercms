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
 * beforeValidate
 *
 * @return	boolean
 * @access	public
 */
	function beforeValidate() {

		$this->validate['name'] = array(array(	'rule' => VALID_NOT_EMPTY,
						'message' => ">> ユーザーグループ名を入力して下さい"),
				array(	'rule' => 'halfText',
						'message' => '>> ユーザーグループは半角のみで入力して下さい'),
				array(	'rule' => array('duplicate','name'),
						'message' => '>> 既に登録のあるユーザーグループ名です'));
		$this->validate['title'] = array(array(	'rule' => VALID_NOT_EMPTY,
						'message' => ">> 表示名を入力して下さい"));
		return true;

	}
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
}
?>