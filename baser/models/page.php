<?php
/* SVN FILE: $Id$ */
/**
 * ページモデル
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
 * ページモデル
 * @package			baser.models
 */
class Page extends AppModel {
/**
 * クラス名
 * @var		string
 * @access 	public
 */
   	var $name = 'Page';
/**
 * データベース接続
 * @var     string
 * @access  public
 */
    var $useDbConfig = 'baser';
/**
 * belongsTo
 * @var 	array
 * @access	public
 */
 	var $belongsTo = array(
                            'PageCategory' =>   array(  'className'=>'PageCategory',
                                                        'foreignKey'=>'page_category_id'));
/**
 * 更新前のページファイルのパス
 * @var	string
 * @access public
 */
	var $oldPath = '';
/**
 * beforeValidate
 * @return	boolean
 * @access	public
 */
	function beforeValidate(){

		$this->validate['name'] = array(array('rule' => array('minLength',1),
											'message' => ">> ページ名を入力して下さい。",
											'required' => true),
                                        array('rule' => 'pageExists',
											'message' => ">> 指定したページは既に存在します。ファイル名、またはカテゴリを変更して下さい。"));
		$this->validate['page_category_id'] = array(array('rule' => 'pageExists',
											'message' => ">> 指定したページは既に存在します。ファイル名、またはカテゴリを変更して下さい。",
											'required' => false));
		return true;

	}
/**
 * フォームの初期値を設定する
 * @return	array	初期値データ
 * @access	public
 */
	function getDefaultValue($theme){

        $data[$this->name]['no'] = $this->getMax('no',array('theme'=>$theme))+1;
        $data[$this->name]['sort'] = $this->getMax('sort',array('theme'=>$theme))+1;
        $data[$this->name]['theme'] = $theme;
		$data[$this->name]['status'] = false;
		return $data;

	}
/**
 * beforeSave
 * @return boolean
 */
    function beforeSave(){

        if($this->exists()){
            $this->oldPath = $this->_getPageFilePath($this->find(array('Page.id'=>$this->data['Page']['id'])));
        }else{
            $this->oldPath = '';
        }

         // 新しいページファイルのパスを取得する
        $newPath = $this->_getPageFilePath($this->data);
        // ファイルに保存
        $newFile = new File($newPath);

        if($newFile->open('w')){
            $newFile->close();
            return true;
        }else{
            return false;
        }

    }
/**
 * afterSave
 * @return boolean
 */
    function afterSave(){

        /*if(!$this->data['Page']['status']){
            $this->delFile($this->data);
            return true;
        }*/

        // タイトルタグと説明文を追加
        if(empty($this->data['Page']['id'])){
            $this->data['Page']['id'] = $this->getInsertID();
        }
        $contents = $this->addBaserPageTag($this->data['Page']['id'], $this->data['Page']['contents'], $this->data['Page']['title'],$this->data['Page']['description']);
        
        // 新しいページファイルのパスを取得する
        $newPath = $this->_getPageFilePath($this->data);
        
        // ファイルに保存
        $newFile = new File($newPath);
        if($newFile->open('w')){
            if($newFile->append($contents)){
                // テーマやファイル名が変更された場合は元ファイルを削除する
                if($this->oldPath && ($newPath != $this->oldPath)){
                    $oldFile = new File($this->oldPath);
                    $oldFile->delete();
                    unset($oldFile);
                }
            }
            $newFile->close();
            unset($newFile);
            chmod($newPath, 0666);
            return true;
        }else{
            return false;
        }

    }
/**
 * ページファイルのディレクトリを取得する
 * @param array $data
 * @return string
 */
    function _getPageFilePath($data){
        
        $file = $data['Page']['name'];
        $categoryId = $data['Page']['page_category_id'];
        $theme = $data['Page']['theme'];

        // pagesディレクトリのパスを取得
        if($theme){
            $path = WWW_ROOT.'themed'.DS.$theme.DS.'pages'.DS;
        }else{
            $path = VIEWS.'pages'.DS;

        }

        if(!is_dir($path)){
            mkdir($path);
            chmod($path,0777);
        }

        if($categoryId){
            $categoryPath = $this->PageCategory->getPath($categoryId);
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
        return $path.$file.'.html.ctp';
        
    }
/**
 * ページファイルを削除する
 * @param array $data 
 */
    function delFile($data){
        $path = $this->_getPageFilePath($data);
        if($path){
            return unlink($path);
        }
        return true;
    }
/**
 * ページのURLを取得する
 * @param array $data
 * @return string
 */
    function getPageUrl($data){
        $categoryId = $data['Page']['page_category_id'];
        $url = '/';
        if($categoryId){
            $categoryPath = $this->PageCategory->getPath($categoryId);
            if($categoryPath){
                foreach($categoryPath as $category){
                    $url .= $category['PageCategory']['name'].DS;
                }
            }
        }
        return $url.$data['Page']['name'].'.html';
    }
/**
 * Baserが管理するタグを追加する
 * @param string $contents
 * @param string $title
 * @return string
 */
    function addBaserPageTag($id,$contents,$title,$description){
        $tag = '<!-- BaserPageTagBegin -->'."\n";
        $tag .= '<?php $baser->setTitle(\''.$title.'\') ?>'."\n";
        $tag .= '<?php $baser->setDescription(\''.$description.'\') ?>'."\n";
        $tag .= '<?php $baser->editPage('.$id.') ?>'."\n";
        $tag .= '<!-- BaserPageTagEnd -->'."\n";
        return $tag . $contents;
    }
/**
 * ページ存在チェック
 *
 * @param	string	チェック対象文字列
 * @return	boolean
 * @access	public
 */
	function pageExists($check){
        if($this->exists()){
            return true;
        }else{
            $conditions['Page.name'] = $this->data['Page']['name'];
			if(empty($this->data['Page']['page_category_id'])){
				$conditions['Page.page_category_id'] = NULL;
			}else{
				$conditions['Page.page_category_id'] = $this->data['Page']['page_category_id'];
			}
            $conditions['Page.theme'] = $this->data['Page']['theme'];
            if(!$this->find($conditions)){
                return true;
            }else{
                return !file_exists($this->_getPageFilePath($this->data));
            }
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

        if(ClassRegistry::isKeySet('SiteConfig')){
            $SiteConfig = ClassRegistry::getObject('SiteConfig');
            $controlSources['theme'] = $SiteConfig->getThemes();
        }
		$controlSources['page_category_id'] = $this->PageCategory->getControlSource('parent_id');

		if(isset($controlSources[$field])){
			return $controlSources[$field];
		}else{
			return false;
		}

	}
    
}
?>