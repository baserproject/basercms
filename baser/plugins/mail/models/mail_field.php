<?php
/* SVN FILE: $Id$ */
/**
 * メールフィールドモデル
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
 * @package			baser.plugins.mail.models
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * メールフィールドモデル
 *
 * @package			baser.plugins.mail.models
 *
 */
class MailField extends MailAppModel {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
	var $name = 'MailField';
/**
 * beforeValidate
 *
 * @return	void
 * @access	public
 */
	function beforeValidate(){

		$this->validate['field_name'] = array(array('rule' => 'halfText',
													'message' => '>> フィールド名は半角のみで入力して下さい。',
													'allowEmpty'=>false),
												array(	'rule'=>'duplicateMailField',
														'message' => '>> 入力されたフィールド名は既に登録されています'));
		$this->validate['name'] = array(array(	'rule' => VALID_NOT_EMPTY,
												'message' => ">> 項目名を入力して下さい。"));
		$this->validate['type'] = array(array(	'rule' => VALID_NOT_EMPTY,
												'message' => ">> タイプを入力して下さい"));

		return true;
	}
/**
 * コントロールソースを取得する
 *
 * @return	array	source
 * @access 	public
 */
	function getControlSource($field = null){
		
		$source['type'] = array('text'=>'テキスト',
								'textarea'=>'テキストエリア',
								'radio'=>'ラジオボタン',
								'select'=>'セレクトボックス',
								'multi_check'=>'マルチチェックボックス',
                                'autozip'=>'自動補完郵便番号',
								'pref'=>'都道府県リスト',
								'hidden'=>'隠しフィールド');
								
		$source['valid'] = array('VALID_NOT_EMPTY'=>'入力必須',
									'VALID_EMAIL'=>'Eメールチェック',
									'/^(|[0-9]+)$/'=>'数値チェック',
									'/^([0-9]+)$/'=>'数値チェック（入力必須）');
									
		$source['valid_ex'] = array('VALID_EMAIL_CONFIRM'=>'Eメール比較チェック',
									'VALID_GROUP_COMPLATE'=>'グループチェック',
									'VALID_NOT_UNCHECKED'=>'チェックなしチェック',
									'VALID_DATETIME'=>'日付チェック');
		$source['auto_convert'] = array('CONVERT_HANKAKU'=>'半角変換');

		if($field){
			return $source[$field];
		}else{
			return $source;
		}			
	}
/**
 * 同じ名称のフィールド名がないかチェックする
 * 同じメールコンテンツが条件
 * @param array $check
 * @return boolean
 */
    function duplicateMailField($check){

        $conditions = array('MailField.'.key($check)=>$check[key($check)],
                            'MailField.mail_content_id' => $this->data['MailField']['mail_content_id']);
        if($this->exists()){
            $conditions['NOT'] = array('MailField.id'=>$this->id);
        }
		$ret = $this->find($conditions);
		if($ret){
			return false;
		}else{
			return true;
		}

    }
}
?>