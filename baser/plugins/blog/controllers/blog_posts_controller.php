<?php
/* SVN FILE: $Id$ */
/**
 * 記事コントローラー
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
 * 記事コントローラー
 *
 * @package			baser.plugins.blog.controllers
 */
class BlogPostsController extends BlogAppController{
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
	var $name = 'BlogPosts';
/**
 * モデル
 *
 * @var 	array
 * @access 	public
 */
	var $uses = array('Blog.BlogPost','Blog.BlogContent','Blog.BlogCategory');
/**
 * ヘルパー
 *
 * @var 	array
 * @access 	public
 */
	var $helpers = array('TextEx','TimeEx','Freeze','Ckeditor');
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
 * @var 	array
 * @access 	public
 */
	var $subMenuElements = array();
/**
 * ブログコンテンツデータ
 *
 * @var     array
 * @access  public
 */
    var $blogContent;
/**
 * beforeFilter
 *
 * @return	void
 * @access 	public
 */
	function beforeFilter(){
        
		parent::beforeFilter();
        if(isset($this->params['pass'][0])){
            $this->blogContent = $this->BlogContent->read(null,$this->params['pass'][0]);
            $this->navis = am($this->navis,array($this->blogContent['BlogContent']['title'].'管理'=>'/admin/blog/blog_posts/index/'.$this->params['pass'][0]));
            if($this->params['prefix']=='admin'){
                $this->subMenuElements = array('blog_posts','blog_categories','blog_common','plugins');
            }
        }
	}
/**
 * beforeRender
 *
 * @return	void
 * @access 	public
 */
	function beforeRender(){

		parent::beforeRender();
		$this->set('blogContent',$this->blogContent);

	}
/**
 * [ADMIN] 一覧表示
 *
 * @return	void
 * @access 	public
 */
	function admin_index($blogContentId){

		if(!$blogContentId) {
			$this->Session->setFlash('無効な処理です。');
			$this->redirect(array('controller'=>'blog_contents','action'=>'admin_index'));
		}

        /* セッション処理 */
        if($this->data){
            $this->Session->write('Filter.BlogPost.blog_category_id',$this->data['BlogPost']['blog_category_id']);
            $this->Session->write('Filter.BlogPost.status',$this->data['BlogPost']['status']);
        }else{
            if($this->Session->check('Filter.BlogPost.blog_category_id')){
                $this->data['BlogPost']['blog_category_id'] = $this->Session->read('Filter.BlogPost.blog_category_id');
            }else{
                $this->Session->del('Filter.BlogPost.blog_category_id');
            }
            if($this->Session->check('Filter.BlogPost.status')){
                $this->data['BlogPost']['status'] = $this->Session->read('Filter.BlogPost.status');
            }else{
                $this->Session->del('Filter.BlogPost.status');
            }
        }
        
        $conditions = array('BlogPost.blog_content_id'=>$blogContentId);
        // ページカテゴリ
        // 子カテゴリも検索条件に入れる
        $blogCategoryIds = array($this->data['BlogPost']['blog_category_id']);
        if(!empty($this->data['BlogPost']['blog_category_id'])){
            $children = $this->BlogCategory->children($this->data['BlogPost']['blog_category_id']);
            if($children){
                foreach($children as $child){
                    $blogCategoryIds[] = $child['BlogCategory']['id'];
                }
            }
            $conditions['BlogPost.blog_category_id'] = $blogCategoryIds;
        }
        // ステータス
        if(isset($this->data['BlogPost']['status']) && $this->data['BlogPost']['status'] !== ''){
            $conditions['BlogPost.status'] = $this->data['BlogPost']['status'];
        }

		// データを取得
		$this->paginate = array('conditions'=>$conditions,
                            	'fields'=>array(),
                            	'order'=>'BlogPost.id',
                            	'limit'=>10
                            	);

		$posts = $this->paginate('BlogPost');

		if($posts){
			$this->set('posts',$posts);
		}
		
		// 表示設定
		$this->pageTitle = '['.$this->blogContent['BlogContent']['title'].'] 記事一覧';
		
	}
/**
 * [ADMIN] 登録処理
 * 
 * @return	void
 * @access 	public
 */
	function admin_add($blogContentId){

		if(!$blogContentId) {
			$this->Session->setFlash('無効な処理です。');
			$this->redirect(array('controller'=>'blog_contents','action'=>'admin_index'));
		}
        
		if(empty($this->data)){
			$this->data = $this->BlogPost->getDefaultValue();
		}else{

            $this->data['BlogPost']['blog_content_id'] = $blogContentId;
            $this->data['BlogPost']['no'] = $this->BlogPost->getMax('no',array('BlogPost.blog_content_id'=>$blogContentId))+1;
            $this->data['BlogPost']['posts_date'] = str_replace('/','-',$this->data['BlogPost']['posts_date']);
			$this->BlogPost->create($this->data);
			
			// データを保存
			if($this->BlogPost->save()){
				$id = $this->BlogPost->getLastInsertId();
                $message = '記事「'.$this->data['BlogPost']['name'].'」を追加しました。';
				$this->Session->setFlash($message);
				$this->BlogPost->saveDbLog($message);
				// 編集画面にリダイレクト
				$this->redirect('/admin/blog/blog_posts/edit/'.$blogContentId.'/'.$id);
			}else{
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}
            
		}

		// 表示設定
		$users = $this->BlogPost->User->find("all",array('conditions'=>array('authority_group'=>"1")));
		if ($users) {
			// 苗字が同じ場合にわかりにくいので、foreachで生成
			//$this->set('users',Set::combine($users, '{n}.User.id', '{n}.User.real_name_1'));
			foreach($users as $key => $user){
				$_users[$user['User']['id']] = $user['User']['real_name_1']." ".$user['User']['real_name_2'];
			}
			$this->set('users',$_users);
		}
		$this->pageTitle = '['.$this->blogContent['BlogContent']['title'].'] 新規記事登録';
		$this->render('form');
		
	}
/**
 * [ADMIN] 編集処理
 * 
 * @param	int		blog_post_id
 * @return	void
 * @access 	public
 */
	function admin_edit($blogContentId,$id){

		if(!$blogContentId || !$id) {
			$this->Session->setFlash('無効な処理です。');
			$this->redirect(array('controller'=>'blog_contents','action'=>'admin_index'));
		}
		
		if(empty($this->data)){
			$this->data = $this->BlogPost->read(null, $id);
		}else{
			$this->data['BlogPost']['posts_date'] = str_replace('/','-',$this->data['BlogPost']['posts_date']);
			// データを保存
			if($this->BlogPost->save($this->data)){
                $message = '記事「'.$this->data['BlogPost']['name'].'」を更新しました。';
				$this->Session->setFlash($message);
				$this->BlogPost->saveDbLog($message);
				// 一覧にリダイレクトすると記事の再編集時に検索する必要があるので一旦コメントアウト
				//$this->redirect('/admin/blog/blog_posts/index/'.$blogContentId);
			}else{
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}
			
		}
		
		// 表示設定
		$users = $this->BlogPost->User->find("all",array('conditions'=>array('authority_group'=>"1")));
		$_users = array();
        if ($users) {
			// 苗字が同じ場合にわかりにくいので、foreachで生成
			//$this->set('users',Set::combine($users, '{n}.User.id', '{n}.User.real_name_1'));
			foreach($users as $key => $user){
				$_users[$user['User']['id']] = $user['User']['real_name_1']." ".$user['User']['real_name_2'];
			}
			
		}
        $this->set('users',$_users);
		$this->pageTitle = '['.$this->blogContent['BlogContent']['title'].'] 記事編集： '.$this->data['BlogPost']['name'];
		$this->render('form');
		
	}
/**
 * [ADMIN] 削除処理
 *
 * @param	int		blog_post_id
 * @return	void
 * @access 	public
 */
	function admin_delete($blogContentId,$id = null) {

		if(!$blogContentId || !$id) {
			$this->Session->setFlash('無効な処理です。');
			$this->redirect(array('controller'=>'blog_contents','action'=>'admin_index'));
		}
		
		// メッセージ用にデータを取得
		$post = $this->BlogPost->read(null, $id);
		
		// 削除実行
		if($this->BlogPost->del($id)) {
			$message = $post['BlogPost']['name'].' を削除しました。';
			$this->Session->setFlash($message);
			$this->BlogPost->saveDbLog($message);
		}else{
			$this->Session->setFlash('データベース処理中にエラーが発生しました。');
		}
		
		$this->redirect(array('action'=>'admin_index',$blogContentId));
		
	}
/**
 * 外部データインポート
 *
 * WordPressのみ対応（2.2.3のみ検証済）
 *
 * @return  void
 * @access  public
 */
    function admin_import(){

        // 入力チェック
        $check = true;$message = '';
        if(!isset($this->data['Import']['blog_content_id']) || !$this->data['Import']['blog_content_id']){
            $message .= '取り込み対象のブログを選択して下さい<br />';
            $check = false;
        }
        if(!isset($this->data['Import']['user_id']) || !$this->data['Import']['user_id']){
            $message .= '記事の投稿者を選択して下さい<br />';
            $check = false;
        }        
        if(!isset($this->data['Import']['file']['tmp_name'])){
            $message .= 'XMLデータを選択して下さい<br />';
            $check = false;
        }
        if($this->data['Import']['file']['type'] != 'text/xml'){
            $message .= 'XMLデータを選択して下さい<br />';
            $check = false;
        }else{

            // XMLデータを読み込む
            App::import('Xml');
            $xml = new Xml($this->data['Import']['file']['tmp_name']);

            $_posts = Set::reverse($xml);

            if(!isset($_posts['Rss']['Channel']['Item'])){
                $message .= 'XMLデータが不正です<br />';
                $check = false;
            }else{
                $_posts = $_posts['Rss']['Channel']['Item'];
            }
            
        }

        // 送信内容に問題がある場合には元のページにリダイレクト
        if(!$check){
            $this->Session->setFlash($message);
            $this->redirect(array('controller'=>'blog_configs','action'=>'form'));
        }

        // カテゴリ一覧の取得
        $blogCategoryList = $this->BlogCategory->find('list',array('conditions'=>array('blog_content_id'=>$this->data['Import']['blog_content_id'])));
        $blogCategoryList = array_flip($blogCategoryList);

        // ポストデータに変換し１件ずつ保存
        $count = 0;
        foreach($_posts as $_post){
            if(!$_post['Encoded'][0]){
                continue;
            }
            $post = array();
            $post['blog_content_id'] = $this->data['Import']['blog_content_id'];
            $post['no'] = $this->BlogPost->getMax('no',array('BlogPost.blog_content_id'=>$this->data['Import']['blog_content_id']))+1;
            $post['name'] = $_post['title'];
            $_post['Encoded'][0] = str_replace("\n","<br />",$_post['Encoded'][0]);
            $encoded = split('<!--more-->',$_post['Encoded'][0]);
            $post['content'] = $encoded[0];
            if(isset($encoded[1])) {
                $post['detail'] = $encoded[1];
            }else{
                $post['detail'] = '';
            }
            if(isset($_post['Category'])){
                $_post['category'] = $_post['Category'][0];
            }elseif(isset($_post['category'])){
                $_post['category'] = $_post['category'];
            }else{
                $_post['category'] = '';
            }
            if(isset($blogCategoryList[$_post['category']])){
                $post['blog_category_no'] = $blogCategoryList[$_post['category']];
            }else{
                $no = $this->BlogCategory->getMax('no',array('BlogCategory.blog_content_id'=>$this->data['Import']['blog_content_id']))+1;
                $this->BlogCategory->create(array('name'=>$_post['category'],'blog_content_id'=>$this->data['Import']['blog_content_id'],'no'=>$no));
                $this->BlogCategory->save();
                $post['blog_category_id'] = $this->BlogCategory->getInsertID();
                $blogCategoryList[$_post['category']] = $post['blog_category_id'];
            }
            
            $post['user_id'] = $this->data['Import']['user_id'];
            $post['status'] = 1;
            $post['posts_date'] = $_post['post_date'];
            
            $this->BlogPost->create($post);
            if($this->BlogPost->save()){
                $count++;
            }
            
        }

        $this->Session->setFlash( $count . ' 件の記事を取り込みました');
        $this->redirect(array('controller'=>'blog_configs','action'=>'form'));

    }
	
}
?>