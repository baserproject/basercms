<?php
/* SVN FILE: $Id$ */
/**
 * システム設定モデル
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
 * システム設定モデル
 *
 * @package			baser.models
 */
class SiteConfig extends AppModel {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
   	var $name = 'SiteConfig';
/**
 * データベース接続
 *
 * @var     string
 * @access  public
 */
    var $useDbConfig = 'baser';
/**
 * beforeValidate
 *
 * @return	boolean
 * @access	public
 */
	function beforeValidate(){
		
		$this->validate['name'] = array(array('rule' => array('minLength',1),
											'message' => ">> WEBサイト名を入力して下さい",
											'required' => true));
		$this->validate['email'] = array(array('rule' => array('email'),
											'message' => ">> 管理者メールアドレスの形式が不正です"),
                                         array('rule' => array('minLength',1),
											'message' => ">> 管理者メールアドレスを入力してください。"));
		return true;
		
	}
/**
 * テーマの一覧を取得する
 *
 * @return array
 */
    function getThemes(){

        $themes = array();
        $themedFolder = new Folder(VIEWS.'themed'.DS);
        $_themes = $themedFolder->read(true,true);
        foreach($_themes[0] as $theme){
            $themes[$theme] = Inflector::camelize($theme);
        }
        $themedFolder = new Folder(WWW_ROOT.'themed'.DS);
        $_themes = array_merge($themes,$themedFolder->read(true,true));
        foreach($_themes[0] as $theme){
            $themes[$theme] = Inflector::camelize($theme);
        }
        return $themes;
        
    }
/**
 * コントロールソースを取得する
 * @param string $field
 * @return mixed array | false
 */
    function getControlSource($field=null){
		$controlSources['mode'] = array(-1=>'インストールモード',0=>'ノーマルモード',1=>'デバッグモード１',2=>'デバッグモード２',3=>'デバッグモード３');
		if(isset($controlSources[$field])){
			return $controlSources[$field];
		}else{
			return false;
		}
    }
/**
 * Key Value 形式のテーブルよりデータを取得して
 * １レコードとしてデータを展開する
 * @return array
 */
    function findExpanded(){
        $dbDatas = $this->find('all');
        if($dbDatas){
            foreach($dbDatas as $dbData){
                $_siteConfig = $dbData['SiteConfig'];
                $siteConfig[$_siteConfig['name']] = $_siteConfig['value'];
            }
        }
        return $siteConfig;
    }
/**
 * Key Value 形式のテーブルにデータを保存する
 * @param array $data
 */
    function saveKeyValue($data){
        if(isset($data['SiteConfig'])){
            $data = $data['SiteConfig'];
        }
        $siteConfigs = array();
        foreach($data as $key => $value){
            $siteConfig = $this->find(array('name'=>$key));
            if(!$siteConfig){
				$siteConfig = array();
				$siteConfig['SiteConfig']['name'] = $key;
				$siteConfig['SiteConfig']['value'] = $value;
				$this->create($siteConfig);
			}else{
				$siteConfig['SiteConfig']['value'] = $value;
				$this->set($siteConfig);
			}
			$this->save($siteConfig,false);
			// SQliteの場合、トランザクション用の関数をサポートしていない場合があるので、
			// 個別に保存するようにした。
            
        }
		return true;
    }

}
?>