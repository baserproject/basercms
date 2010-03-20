<?php
/* SVN FILE: $Id$ */
/**
 * 記事モデル
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
 * @package			baser.plugins.blog.models
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
 * 記事モデル
 *
 * @package			baser.plugins.blog.models
 */
class BlogPost extends BlogAppModel {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
   	var $name = 'BlogPost';
/**
 * belongsTo
 *
 * @var 	array
 * @access	public
 */
 	var $belongsTo = array(
                            'BlogCategory' =>   array(  'className'=>'Blog.BlogCategory',
                                                        'foreignKey'=>'blog_category_id'),
                            'User' =>           array(  'className'=>'User',
                                                        'foreignKey'=>'user_id'),
                            'BlogContent' =>    array(  'className'=>'Blog.BlogContent',
                                                        'foreignKey'=>'blog_content_id')
                            );
/**
 * hasMany
 *
 * @var		array
 * @access 	public
 */
 	var $hasMany = array('BlogComment'=>
							array('className'=>'Blog.BlogComment',
									'order'=>'created',
									'foreignKey'=>'blog_post_id',
									'dependent'=>true,
									'exclusive'=>false,
									'finderQuery'=>''));
/**
 * beforeValidate
 *
 * @return	void
 * @access	public
 */
	function beforeValidate(){
		
		$this->validate['name'] = array(array(  'rule' => array('minLength',1),
                                                'message' => ">> タイトルを入力して下さい",
                                                'required' => true));
		$this->validate['posts_date'] = array(array('rule' => array('minLength',1),
                                                    'message' => ">> 投稿日を入力して下さい",
                                                    'required' => true));
		$this->validate['user_id'] = array(array(   'rule' => array('minLength',1),
                                                    'message' => ">> 投稿者を選択して下さい",
                                                    'required' => true));
		return true;
	}
/**
 * beforeSave
 *
 * @return	boolean
 * @access	public
 */
    function beforeSave(){

        // niceditorで何も入力しない場合、自動的に<br>を入れられてしまうので、空にする
        if($this->data['BlogPost']['detail']=='<br>'){
            $this->data['BlogPost']['detail'] = '';
        }
        return true;

    }
/**
 * 初期値を取得する
 *
 * @return	array	初期値データ
 * @access	public
 */
	function getDefaultValue($authUser){
		$data[$this->name]['user_id'] = $authUser['User']['id'];
		$data[$this->name]['posts_date'] = date('Y/m/d H:i:s');
		$data[$this->name]['status'] = 0;
		return $data;
	}
/**
 * ブログの月別一覧を取得する
 *
 * @return array    月別リストデータ
 * @access public
 */
    function getBlogDates($blogContentId){
        
        // TODO CSVDBではGROUP BYが実装されていない為、取り急ぎPHPで処理
        /*$dates = $this->find('all',array('fields'=>array('YEAR(posts_date) as year','MONTH(posts_date) as month','COUNT(id)' as count),
                                          'conditions'=>array('BlogPost.status'=>1),
                                          'group'=>array('YEAR(posts_date)','MONTH(posts_date)'))));*/
        $posts = $this->find('all',array('conditions'=>array('BlogPost.status'=>1,'BlogPost.blog_content_id'=>$blogContentId),'order'=>'BlogPost.posts_date DESC'));
        $postsDates = Set::extract('/BlogPost/posts_date',$posts);

        $dates = array();
        foreach($postsDates as $postsDate){
            $exists = false;
            $_date = array();
            foreach($dates as $key => $date){
                if($date['year'] == date('Y',strtotime($postsDate)) &&
                        $date['month'] == date('m',strtotime($postsDate))){
                    $exists = true;
                    $dates[$key]['count']++;
                }
            }
            if(!$exists){
                $_date['year'] = date('Y',strtotime($postsDate));
                $_date['month'] = date('m',strtotime($postsDate));
                $_date['count'] = 1;
                $dates[] = $_date;
            }
        }
        return $dates;
        
    }
/**
 * カレンダー用に指定した月で記事の投稿がある日付のリストを取得する
 */
	function getEntryDates($contentId,$year,$month){
        
		$conditions = array('BlogPost.status'=>true,'BlogPost.blog_content_id'=>$contentId);

        $dbConfig = new DATABASE_CONFIG();
        $driver = str_replace('_ex','',$dbConfig->plugin['driver']);

        switch($driver){
        case 'mysql':
        case 'csv':
            if(!empty($year)){
                $conditions["YEAR(`BlogPost`.`posts_date`)"] = $year;
            }else{
                $conditions["YEAR(`BlogPost`.`posts_date`)"] = date('Y');
            }
            if(!empty($month)){
                $conditions["MONTH(`BlogPost`.`posts_date`)"] = $month;
            }else{
                $conditions["MONTH(`BlogPost`.`posts_date`)"] = date('m');
            }
            break;
        
        case 'postgres':
            if(!empty($year)){
                $conditions["date_part('year', \"BlogPost\".\"posts_date\")"] = $year;
            }else{
                $conditions["date_part('year', \"BlogPost\".\"posts_date\")"] = date('Y');
            }
            if(!empty($month)){
                $conditions["date_part('month', \"BlogPost\".\"posts_date\")"] = $month;
            }else{
                $conditions["date_part('month', \"BlogPost\".\"posts_date\")"] = date('m');
            }
            break;
        
        case 'sqlite':
        case 'sqlite3':
            if(!empty($year)){
                $conditions["strftime('%Y',BlogPost.posts_date)"] = $year;
            }else{
                $conditions["strftime('%Y',BlogPost.posts_date)"] = date('Y');
            }
            if(!empty($month)){
                $conditions["strftime('%m',BlogPost.posts_date)"] = sprintf('%02d',$month);
            }else{
                $conditions["strftime('%m',BlogPost.posts_date)"] = date('m');
            }
            break;

        }
        
		$entryDates = $this->find('all',array('fields'=>array('BlogPost.posts_date'),'conditions'=>$conditions));
		$entryDates = Set::extract('/BlogPost/posts_date',$entryDates);
        
		foreach($entryDates as $key => $entryDate){
			$entryDates[$key] = date('Y-m-d',strtotime($entryDate));
		}
		return $entryDates;
        
	}
/**
 * コントロールソースを取得する
 *
 * @param	string	フィールド名
 * @return	array	コントロールソース
 * @access	public
 */
	function getControlSource($field = null,$options = array()){

		$controlSources['blog_category_id'] = $this->BlogCategory->getControlSource('parent_id',$options);

		if(isset($controlSources[$field])){
			return $controlSources[$field];
		}else{
			return false;
		}

	}
    
}
?>