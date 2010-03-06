<?php
/* SVN FILE: $Id$ */
/**
 * ブログコンテンツコントローラー
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
 * @package			baser.plugins.blog.controllers
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
 * ブログコンテンツコントローラー
 *
 * @package			baser.plugins.blog.controllers
 */
class BlogContentsController extends BlogAppController{
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
	var $name = 'BlogContents';
/**
 * モデル
 *
 * @var 	array
 * @access 	public
 */
	var $uses = array('SiteConfig',"Blog.BlogContent");
/**
 * ヘルパー
 *
 * @var 	array
 * @access 	public
 */
	var $helpers = array('Html','TimeEx','Freeze');
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
                        'ブログ管理'=>'/admin/blog/blog_contents/index');
/**
 * サブメニューエレメント
 *
 * @var		string
 * @access 	public
 */
	var $subMenuElements = array();
/**
 * before_filter
 *
 * @return	void
 * @access 	public
 */
    function beforeFilter(){
        parent::beforeFilter();
        if($this->params['prefix']=='admin'){
            $this->subMenuElements = array('blog_common','plugins');
        }
    }
/**
 * [ADMIN] ブログコンテンツ一覧
 *
 * @return  void
 * @access  public
 */
    function admin_index(){

        $listDatas = $this->BlogContent->findAll();
        $this->set('listDatas',$listDatas);
        $this->pageTitle = 'ブログ一覧';
        
    }
/**
 * [ADMIN] ブログコンテンツ追加
 *
 * @return  void
 * @access  public
 */
    function admin_add(){

        $this->pageTitle = '新規ブログ登録';
        
        if(!$this->data){
            $this->data = $this->_getDefaultValue();
        }else{

			/* 登録処理 */
			$this->BlogContent->create($this->data);

			// データを保存
			if($this->BlogContent->save()){
                $id = $this->BlogContent->getLastInsertId();
                $message = '新規ブログ「'.$this->data['BlogContent']['title'].'」を追加しました。';
				$this->Session->setFlash($message);
				$this->BlogContent->saveDbLog($message);
				$this->redirect(array('controller'=>'blog_posts','action'=>'index',$id));
			}else{
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}
            
        }

        // テーマの一覧を取得
        $this->set('themes',$this->SiteConfig->getThemes());
        $this->render('form');
        
    }
/**
 * [ADMIN] 編集処理
 *
 @ @param	int		blog_category_no
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
			$this->data = $this->BlogContent->read(null, $id);
		}else{

			/* 更新処理 */
			if($this->BlogContent->save($this->data)){
                $message = 'ブログ「'.$this->data['BlogContent']['title'].'」を更新しました。';
				$this->Session->setFlash($message);
				$this->BlogContent->saveDbLog($message);
				$this->redirect(array('action'=>'index',$id));
			}else{
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		/* 表示設定 */
        $this->set('themes',$this->SiteConfig->getThemes());
		$this->pageTitle = 'ブログ設定編集：'.$this->data['BlogContent']['title'];
		$this->render('form');

	}
/**
 * [ADMIN] 削除処理
 *
 @ @param	int		blog_content_id
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
		$post = $this->BlogContent->read(null, $id);

		/* 削除処理 */
		if($this->BlogContent->del($id)) {
            $message = 'ブログ「'.$post['BlogContent']['title'].'」 を削除しました。';
			$this->Session->setFlash($message);
			$this->BlogContent->saveDbLog($message);
		}else{
			$this->Session->setFlash('データベース処理中にエラーが発生しました。');
		}

		$this->redirect(array('action'=>'index'));

	}
/**
 * フォームの初期値を取得する
 *
 * @return  void
 * @access  protected
 */
    function _getDefaultValue(){

        $data['BlogContent']['layout'] = 'default';
        $data['BlogContent']['template'] = 'default';
        $data['BlogContent']['theme'] = 'default';
        return $data;
        
    }
    
}
?>