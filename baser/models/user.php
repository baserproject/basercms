<?php
/* SVN FILE: $Id$ */
/**
 * ユーザーモデル
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
 * ユーザーモデル
 *
 * @package			baser.models
 */
class User extends AppModel {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
   	var $name = 'User';
/**
 * データベース接続
 *
 * @var     string
 * @access  public
 */
    var $useDbConfig = 'baser';
/**
 * belongsTo
 * @var 	array
 * @access	public
 */
 	var $belongsTo = array('UserGroup' =>   array(  'className'=>'UserGroup',
                                                        'foreignKey'=>'user_group_id'));
/**
 * beforeValidate
 *
 * @return	boolean
 * @access	public
 */
	function beforeValidate(){
		
		
		
		$this->validate['name'] = array(array('rule' => VALID_NOT_EMPTY,
											'message' => ">> アカウント名を入力して下さい"),
										array('rule' => 'halfText',
											'message' => '>> アカウント名は半角のみで入力して下さい'));
											
		$this->validate['real_name_1'] = array(array('rule' => VALID_NOT_EMPTY,
											'message' => ">> 名前[姓]を入力して下さい"));
											
		/*$this->validate['real_name_2'] = array(array('rule' => VALID_NOT_EMPTY,
											'message' => ">> 名前[名]を入力して下さい"));*/
	
        $this->validate['password'] = array(
										'minLength' => array(
											'rule' => array('minLength',6),
											'allowEmpty' => false,
											'message' => '>> パスワードは６文字以上で入力して下さい。'
											),
										'alphaNumeric' => array(
											'rule' => 'alphaNumeric',
										'message' => '>> パスワードは半角英数字のみで入力して下さい'
										)
									);
	
/*		$this->validate['email'] = array(array('rule' => array('email'),
											'message' => ">> Eメールの形式が不正です",
											'required' => true));*/
											
		$this->validate['user_group_id'] =	array(	array('rule' => VALID_NOT_EMPTY,
															'message' => ">> グループを選択して下さい"));
		return true;
		
	}
/**
 * バリデート処理
 *
 * @param	array	$options
 * @return	boolean True if there are no errors
 * @access 	public
 */
// TODO beforeValidateに移行する事
	function invalidFields($options = array())
	{

		$data = $this->data;
		
		// Id重複チェック
		if(!$this->exists() && isset($data[$this->name]['name']) && $this->checkRepeatedId($data[$this->name]['name'])){
			$this->invalidate('name','>> 希望されたIDは既に利用されています');
		}

		/*** パスワードチェック ***/
		
		if(isset($data[$this->name]['password_1']) && isset($data[$this->name]['password_2'])){

			// 入力ミスチェック
			if($data[$this->name]['password_1'] != $data[$this->name]['password_2']){
				$this->invalidate('password','>> パスワードが同じものではありません');
			}else{
				$this->data[$this->name]['password'] = $this->data[$this->name]['password_1'];
			}
            $this->data[$this->name]['password'] = $this->data[$this->name]['password_1'];
            
		}
		
		$ret = parent::invalidFields($options);

		// テキストボックスにエラークラスを表示させる
		// TODO invalidFields は、エラーのあるフィールドリストを返す関数の為、処理見直しが必要かも
		if(isset($this->validationErrors['password'])){
			$this->invalidate('password_1');
			$this->invalidate('password_2');
		}

		return $ret;
				
	}
/**
 * アカウント重複チェック
 * 
 * @param	string	user_name
 * @return	boolean
 * @access	public
 */
	function checkRepeatedId($id){
		
		$ret = $this->find(array('name'=>$id));
		if($ret){
			return true;
		}else{
			return false;
		}
		
	}
/**
 * コントロールソースを取得する
 * 
 * @param	string	フィールド名
 * @return	array	コントロールソース
 * @access	public
 */
	function getControlSource($field = null){

		$controlSources['user_group_id'] = $this->UserGroup->find('list');
		
		if(isset($controlSources[$field])){
			return $controlSources[$field];
		}else{
			return false;
		}
			
	}
/**
 * ユーザーリストを取得する
 * 認証ユーザーのみのリストを取得する場合は引数を指定する
 * @param array $authUser 
 */
	function getUserList($authUser=null){

		if($authUser && $authUser['User']['user_group_id']!=1){
			if($authUser['User']['real_name_2']){
				$name = $authUser['User']['real_name_1']." ".$authUser['User']['real_name_2'];
			}else{
				$name = $authUser['User']['real_name_1'];
			}
			return array($authUser['User']['id']=>$name);
		}

		$users = $this->find("all",array('fields'=>array('id','real_name_1','real_name_2')));
		$list = array();
        if ($users) {
			// 苗字が同じ場合にわかりにくいので、foreachで生成
			//$this->set('users',Set::combine($users, '{n}.User.id', '{n}.User.real_name_1'));
			foreach($users as $key => $user){
				if($user['User']['real_name_2']){
					$name = $user['User']['real_name_1']." ".$user['User']['real_name_2'];
				}else{
					$name = $user['User']['real_name_1'];
				}
				$list[$user['User']['id']] = $name;
			}
		}
		return $list;
	}
/**
 * フォームの初期値を設定する
 * 
 * @return	array	初期値データ
 * @access	public
 */
	function getDefaultValue(){
		
		$data[$this->name]['user_group_id'] = 1;
		return $data;
		
	}
/**
 * afterFind
 *
 * @param	array	結果セット
 * @param	array	$primary
 * @return	array	結果セット
 * @access	public
 */	
	function afterFind($results, $primary = false){
	
		if(isset($results[0][$this->name][0])){
			$results[0][$this->name] = $this->convertResults($results[0][$this->name]);
		}else{
			$results = $this->convertResults($results);
		}
		return parent::afterFind($results,$primary);
		
	}
/**
 * 取得結果を変換する
 *
 * HABTM対応
 *
 * @param	array	結果セット
 * @return	array	結果セット
 * @access	public
 */	
	function convertResults($results){

		if($results){
			if(isset($result[$this->name])||isset($results[0][$this->name])){
				foreach($results as $key => $result){
					if(isset($result[$this->name])){
						if($result[$this->name]){
							$results[$key][$this->name] = $this->convertToView($result[$this->name]);
						}
					}elseif(!empty($result)){
						$results[$key] = $this->convertToView($result);
					}				
				}
			}else{
				$results = $this->convertToView($results);
			}
		}
		return $results;
		
	}
/**
 * View用のデータを取得する
 *
 * @param	array	結果セット
 * @return	array	結果セット
 * @access	public
 */
	function convertToView($data){

		return $data;
		
	}
	
}
?>