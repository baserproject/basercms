<?php
/* SVN FILE: $Id$ */
/**
 * Plugin 拡張クラス
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
 * @package			baser.controllers
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::import('Helper','Freeze',true,BASER_HELPERS);
/**
 * Plugin 拡張クラス
 *
 * プラグインのコントローラーより継承して利用する
 *
 * @package			baser.controllers
 */
class PluginsController extends AppController {
/**
 * クラス名
 *
 * @var     string
 * @access  public
 */
    var $name = 'Plugins';
/**
 * モデル
 *
 * @var 	array
 * @access 	public
 */
	var $uses = array('GlobalMenu','Plugin','PluginContent');
/**
 * コンポーネント
 *
 * @var     array
 * @access  public
 */
    var $components = array('Auth','Cookie','AuthConfigure');
/**
 * ヘルパ
 *
 * @var 	array
 * @access 	public
 */
	var $helpers = array('Time','Freeze');
/**
 * サブメニューエレメント
 *
 * @var 	array
 * @access 	public
 */
	var $subMenuElements = array();
/**
 * ぱんくずナビ
 *
 * @var		string
 * @access 	public
 */
	var $navis = array('システム設定'=>'/admin/site_configs/form',
                        'プラグイン設定'=>'/admin/plugins/index');
/**
 * コンテンツID
 *
 * @var int
 */
    var $contentId = null;
/**
 * beforeFilter
 *
 * @return	void
 * @access	private
 */
	function beforeFilter(){

		parent::beforeFilter();

        if(!isset($this->Plugin)){
            $this->cakeError('missingClass', array(array('className' => 'Plugin',
                        'notice'=>'プラグインでは、コントローラーで、Pluginモデルを読み込んでおく必要があります。usesプロパティを確認して下さい。')));
        }

        // 有効でないプラグインを実行させない
        if($this->name != 'Plugins' && !$this->Plugin->find('all',array('conditions'=>array('name'=>$this->params['plugin'])))){
            $this->notFound();
        }
        
		$this->contentId = $this->getContentId();

	}
/**
 * コンテンツIDを取得する
 *
 * 一つのプラグインで複数のコンテンツを実装する際に利用する。
 * 
 * @return int  $pluginNo
 */
    function getContentId(){

        if(!isset($this->PluginContent)){
            return null;
        }
        
		if(!isset($this->params['url']['url'])){
			return null;
		}
									   
        $url = split('/',$this->params['url']['url']);
        if($url[0]!=Configure::read('Mobile.prefix')){
            $url = $url[0];
        }else{
            $url = $url[1];
        }
		// プラグインと同じ名前のコンテンツ名の場合に正常に動作しないので
		// とりあえずコメントアウト
        /*if( Inflector::camelize($url) == $this->name){
            return null;
        }*/
        $pluginContent = $this->PluginContent->findByName($url);
        if($pluginContent){
            return $pluginContent['PluginContent']['content_id'];
        }else{
            return null;
        }
        
    }
/**
 * プラグインの一覧を表示する
 *
 * @return  void
 * @access  public
 */
    function admin_index(){

        $listDatas = $this->Plugin->find('all');
        if(!$listDatas){
            $listDatas = array();
        }
        // プラグインフォルダーのチェックを行う。
        // データベースに登録されていないプラグインをリストアップ
        $pluginFolder = new Folder(APP.'plugins'.DS);
        $plugins = $pluginFolder->read(true,true);
        $unRegistryPlugins = array();
        foreach($plugins[0] as $plugin){
            $exists = false;
            foreach($listDatas as $data){
                if($plugin == $data['Plugin']['name']){
                    $exists = true;
                    break;
                }
            }
            if(!$exists){
                $unRegistryPlugin = array('Plugin'=>array('id'=>null,'name'=>null,'title'=>null,'admin_link'=>null,'created'=>null,'modified'=>null));
                $unRegistryPlugin['Plugin']['name'] = $plugin;
                $unRegistryPlugin['Plugin']['title'] = '未登録';
                $unRegistryPlugins[] = $unRegistryPlugin;
            }
        }
        $listDatas = array_merge($listDatas,$unRegistryPlugins);

        // 表示設定
        $this->set('listDatas',$listDatas);
        $this->subMenuElements = array('plugins','site_configs');
        $this->pageTitle = 'プラグイン一覧';

    }
/**
 * [ADMIN] ファイル削除
 *
 * @param   string  プライグイン名
 * @access  public
 */
    function admin_delete_file($pluginName){
        $this->__deletePluginFile($pluginName);
        $message = 'プラグイン「'.$pluginName.'」 を完全に削除しました。';
        $this->Session->setFlash($message);
        $this->redirect(array('action'=>'index'));
    }
/**
 * プラグインファイルを削除する
 * データベースのデータは削除せずそのまま残す
 * @param string $pluginName
 * @access private
 */
    function __deletePluginFile($pluginName){
        $folder = new Folder();
        $folder->delete(APP.'plugins'.DS.$pluginName);
        //$folder->delete(APP.'db'.DS.'csv'.DS.$pluginName);
        //$folder->delete(BASER_PLUGINS.$pluginName);
    }
/**
 * [ADMIN] 登録処理
 *
 * @return  void
 * @access  public
 */
    function admin_add($name){

        if(!$this->data){
            $this->data['Plugin']['name']=$name;

            if(file_exists(APP.'plugins'.DS.$name.DS.'config'.DS.'config.php')){
                include APP.'plugins'.DS.$name.DS.'config'.DS.'config.php';
            }elseif(file_exists(BASER_PLUGINS.$name.DS.'config'.DS.'config.php')){
                include BASER_PLUGINS.$name.DS.'config'.DS.'config.php';
            }
            if(isset($adminLink)) $this->data['Plugin']['admin_link'] = $adminLink;
            if(isset($title)) $this->data['Plugin']['title'] = $title;
            if(!empty($installMessage)){
                $this->Session->setFlash($installMessage);
            }
            
        }else{
            if(file_exists(APP.'plugins'.DS.$name.DS.'config'.DS.'install.php')){
                include APP.'plugins'.DS.$name.DS.'config'.DS.'install.php';
            }elseif(file_exists(BASER_PLUGINS.$name.DS.'config'.DS.'install.php')){
                include BASER_PLUGINS.$name.DS.'config'.DS.'install.php';
            }
			/* 登録処理 */
			$this->Plugin->create($this->data);

			// データを保存
			if($this->Plugin->save()){
                $this->deleteCache();
                $message = '新規プラグイン「'.$this->data['Plugin']['title'].'」を BaserCMS に登録しました。';
				$this->Session->setFlash($message);
				$this->Plugin->saveDbLog($message);
				$this->redirect(array('action'=>'index'));
			}else{
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

        }

        /* 表示設定 */
        $this->subMenuElements = array('plugins','site_configs');
        $this->pageTitle = '新規プラグイン登録';
        $this->render('form');

    }
/**
 * [ADMIN] 編集処理
 *
 @ @param	int		ID
 * @return	void
 * @access 	public
 */
	function admin_edit($id){

		/* 除外処理 */
		if(!$id) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action'=>'admin_index'));
		}

		if(empty($this->data)){
			$this->data = $this->Plugin->read(null, $id);
		}else{

			/* 更新処理 */
			if($this->Plugin->save($this->data)){
                $message = 'プラグイン「'.$this->data['Plugin']['title'].'」を更新しました。';
				$this->Session->setFlash($message);
				$this->Plugin->saveDbLog($message);
				$this->redirect(array('action'=>'index',$id));
			}else{
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		/* 表示設定 */
        $this->subMenuElements = array('plugins','site_configs');
		$this->pageTitle = 'プラグイン編集：'.$this->data['Plugin']['title'];
		$this->render('form');

	}
/**
 * [ADMIN] 削除処理
 *
 @ @param	int		ID
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
		$post = $this->Plugin->read(null, $id);

		/* 削除処理 */
		if($this->Plugin->del($id)){
            $message = 'プラグイン「'.$post['Plugin']['title'].'」 を 無効化しました。';
			$this->Session->setFlash($message);
			$this->Plugin->saveDbLog($message);
		}else{
			$this->Session->setFlash('データベース処理中にエラーが発生しました。');
		}

		$this->redirect(array('action'=>'index'));

	}

}
?>