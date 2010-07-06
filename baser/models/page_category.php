<?php
/* SVN FILE: $Id: page_category.php 309 2010-06-13 11:57:06Z ryuring $ */
/**
 * ページカテゴリーモデル
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
 * @version			$Revision: 309 $
 * @modifiedby		$LastChangedBy: ryuring $
 * @lastmodified	$Date: 2010-06-13 20:57:06 +0900 (日, 13 6 2010) $
 * @license			http://basercms.net/license/index.html
 */
/**
 * ページカテゴリーモデル
 *
 * @package			baser.models
 */
class PageCategory extends AppModel {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
   	var $name = 'PageCategory';
/**
 * バリデーション設定
 * @var array
 */
    var $validationParams = array();
/**
 * データベース接続
 *
 * @var     string
 * @access  public
 */
    var $useDbConfig = 'baser';
/**
 * actsAs
 * @var array
 */
    var $actsAs = array('Tree');
/**
 * hasMany
 * @var array
 */
    var $hasMany = array('Page' => array('className'=>'Page',
                                        'conditions'=>'',
                                        'order'=>'Page.id',
                                        'limit'=>'',
                                        'foreignKey'=>'page_category_id',
                                        'dependent'=>false,
                                        'exclusive'=>false,
                                        'finderQuery'=>''));
/**
 * カテゴリIDリスト
 * ※ キャッシュ用
 * var		mixed
 * access	protected
 */
		var $_categoryIds = -1;
/**
 * beforeValidate
 *
 * @return	boolean
 * @access	public
 */
	function beforeValidate(){

		$this->validate['name'] = array(array(	'rule' => array('minLength',1),
												'message' => ">> ページカテゴリ名を入力して下さい。",
												'required' => true),
										array(	'rule' => 'halfText',
												'message' => '>> ページカテゴリー名は半角のみで入力して下さい'),
                                        array(  'rule' => array('duplicatePageCategory'),
                                                'message' => '>> 入力されたページカテゴリー名は、同一階層に既に登録されています'));
		$this->validate['title'] = array(array(	'rule' => array('minLength',1),
												'message' => ">> ページカテゴリタイトルを入力して下さい。",
												'required' => true));
		return true;

	}
/**
 * フォームの初期値を設定する
 *
 * @return	array	初期値データ
 * @access	public
 */
	function getDefaultValue($theme){

        $data[$this->name]['no'] = $this->getMax('no',array('theme'=>$theme))+1;
		return $data;

	}
/**
 * コントロールソースを取得する
 *
 * @param	string	フィールド名
 * @return	array	コントロールソース
 * @access	public
 */
	function getControlSource($field = null,$options = array()){

        if(ClassRegistry::isKeySet('SiteConfig')){
            $SiteConfig = ClassRegistry::getObject('SiteConfig');
            $controlSources['theme'] = $SiteConfig->getThemes();
        }

        $conditions['PageCategory.theme'] = $this->validationParams['theme'];
        if(!empty($options['excludeParentId'])){
            $children = $this->children($options['excludeParentId']);
            $excludeIds = array($options['excludeParentId']);
            foreach($children as $child){
                $excludeIds[] = $child['PageCategory']['id'];
            }
            $conditions['NOT']['PageCategory.id'] = $excludeIds;
        }

        $parents = $this->generatetreelist($conditions);
        $controlSources['parent_id'] = array();
        foreach($parents as $key => $parent){
			if($parent){
				if(preg_match("/^([_]+)/i",$parent,$matches)){
					$parent = preg_replace("/^[_]+/i",'',$parent);
					$prefix = str_replace('_','&nbsp&nbsp&nbsp',$matches[1]);
					$parent = $prefix.'└'.$parent;
				}
				$controlSources['parent_id'][$key] = $parent;
			}
        }

		if(isset($controlSources[$field])){
			return $controlSources[$field];
		}else{
			return false;
		}

	}
/**
 * beforeSave
 * @return boolean
 */
    function beforeSave(){

        // 新しいページファイルのパスを取得する
        $newPath = $this->_getPageDirPath($this->data);
        if($this->exists()){
            $oldPath = $this->_getPageDirPath($this->find(array('id'=>$this->id)));
            if($newPath != $oldPath){
                $dir = new Folder();
                $ret = $dir->move(array('to'=>$newPath,'from'=>$oldPath,'chmod'=>0777));
            }else{
                if(!is_dir($newPath)){
                    $dir = new Folder();
                    $ret = $dir->create($newPath, 0777);
                }
                $ret = true;
            }
        }else{
            $dir = new Folder();
            $ret = $dir->create($newPath, 0777);
        }

        return $ret;

    }
/**
 * ページカテゴリのパスを取得する
 * @param array $data
 * @return string
 */
    function _getPageDirPath($data){

        $theme = $data['PageCategory']['theme'];
        $categoryName = $data['PageCategory']['name'];
        $parentId = $data['PageCategory']['parent_id'];

        if($theme){
            $path = WWW_ROOT.'themed'.DS.$theme.DS.'pages'.DS;
        }else{
            $path = VIEWS.'pages'.DS;
        }

        if($parentId){
            $categoryPath = $this->getPath($parentId);
            if($categoryPath){
                foreach($categoryPath as $category){
                    $path .= $category['PageCategory']['name'].DS;
                    if(!is_dir($path)){
                        mkdir($path,0777);
                        chmod($path,0777);
                    }
                }
            }
        }

        return $path.$categoryName.DS;

    }
/**
 * 同一階層に同じニックネームのカテゴリがないかチェックする
 * 同じテーマが条件
 * @param array $check
 * @return boolean
 */
    function duplicatePageCategory($check){

        $parentId = $this->data['PageCategory']['parent_id'];

        $conditions['PageCategory.theme'] = $this->validationParams['theme'];
        if($parentId){
            $conditions['PageCategory.parent_id'] = $parentId;
        }else{
            $conditions['OR'] = array('PageCategory.parent_id'=>'');
            $conditions['OR'] = array('PageCategory.parent_id'=>null);
        }

        $children = $this->find('all',array('conditions'=>$conditions));

        if($children){
            foreach($children as $child){
                if($this->exists()){
                    if($this->id == $child['PageCategory']['id']){
                        continue;
                    }
                }
                if($child['PageCategory']['name'] == $check[key($check)]){
                    return false;
                }
            }
        }
        return true;

    }
/**
 * 関連するページデータをカテゴリ無所属に変更し保存する
 * @param <type> $cascade
 * @return <type>
 */
    function beforeDelete($cascade = true) {
        parent::beforeDelete($cascade);
        $id = $this->data['PageCategory']['id'];
        $this->Page->unBindModel(array('belongsTo'=>array('PageCategory')));
        $pages = $this->Page->find('all',array('conditions'=>array('Page.page_category_id'=>$id)));
        $ret = true;
        if($pages){
            foreach($pages as $page){
                $page['Page']['page_category_id'] = '';
                $this->Page->set($page);
                if(!$this->Page->save()){
                    $ret = false;
                }
            }
        }
        return $ret;
    }
/**
 * ページカテゴリIDを取得する
 *
 * @param		int			$categoryNo
 * @param		string	$theme
 * @return	$mixed	array / false
 */
	function getCategoryId($categoryNo,$theme){
		if($this->_categoryIds === -1){
			$conditions = array('PageCategory.theme'=>$theme);
			$this->unbindModel(array('hasMany'=>array('Page')));
			$pageCategories = $this->find('all',array('conditions'=>$conditions,'fields'=>array('id','no')));
			if($pageCategories){
				$this->_categoryIds = array();
				foreach($pageCategories as $pageCateogry){
					$this->_categoryIds[$pageCateogry['PageCategory']['no']] = $pageCateogry['PageCategory']['id'];
				}
			}else{
				$this->_categoryIds = array();
			}
		}
		if(isset($this->_categoryIds[$categoryNo])){
			return $this->_categoryIds[$categoryNo];
		}else{
			return false;
		}
	}
}
?>