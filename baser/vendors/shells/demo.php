<?php
/* SVN FILE: $Id$ */
/**
 * デモデータ操作用シェルスクリプト
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
 * @package			baser.config
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
class DemoShell extends Shell {
	
	var $uses = array('User','SiteConfig');
//	var $components = array('Auth');
/**
 * デモ用のCSVデータを初期化する
 */
	function initcsv() {
	
		if($this->_initCsv()){
			
			App::import('Core','Security');
			$this->deleteCache();

			$ret = true;
			$user['User']['name'] = 'admin';
			$user['User']['password_1'] = Security::hash('demodemo', null, true);
			$user['User']['password_2'] = $user['User']['password_1'];
			$user['User']['real_name_1'] = 'admin';
			$user['User']['authority_group'] = 1;
			$this->User->create($user);
			if(!$this->User->save()) $ret = false;
			
			$user['User']['name'] = 'member';
			$user['User']['password_1'] = Security::hash('demodemo', null, true);
			$user['User']['password_2'] = $user['User']['password_1'];
			$user['User']['real_name_1'] = 'member';
			$user['User']['authority_group'] = 2;
			$this->User->create($user);
			if(!$this->User->save()) $ret = false;
			
			if($ret){
				
				$siteConfig = $this->SiteConfig->findExpanded();
				$siteConfig['address'] = '福岡県福岡市博多区博多駅前';
				$siteConfig['googlemaps_key'] = 'ABQIAAAAQMyp8zF7wiAa55GiH41tChRi112SkUmf5PlwRnh_fS51Rtf0jhTHomwxjCmm-iGR9GwA8zG7_kn6dg';
                $siteConfig['demo_on'] = true;
				if($this->SiteConfig->saveKeyValue($siteConfig)){
					echo "デモデータの初期化に成功しました\n";
				}else{
					echo "システム設定の更新に失敗しました\n";
				}
				
			}else{
				echo "ユーザー「member」の作成に失敗しました\n";
			}
			
		}else{
			echo "CSVデータベースの初期化に失敗しました\n";
		}
		
	}
/**
 * キャッシュファイルを削除する
 */
    function deleteCache(){
        App::import('Core','Folder');
        $folder = new Folder(CACHE);

        $files = $folder->read(true,true,true);
        foreach($files[1] as $file){
            @unlink($file);
        }
        foreach($files[0] as $dir){
            $folder = new Folder($dir);
            $caches = $folder->read(true,true,true);
            foreach($caches[1] as $file){
                if(basename($file) != 'empty'){
                    @unlink($file);
                }
            }
        }
    }
/**
 * CSVデータの初期化
 */
	function _initCsv(){
		
		if(!is_dir(APP.'db')){
			mkdir(APP.'db',0777);
			chmod(APP.'db',0777);
		}
		if(!is_dir(APP.'db'.DS.'csv')){
			mkdir(APP.'db'.DS.'csv',0777);
			chmod(APP.'db'.DS.'csv',0777);
		}
		
		$folder = new Folder();
		
		$dbConfig = new DATABASE_CONFIG();
		$folder->delete($dbConfig->baser['database']);
		$folder->delete($dbConfig->plugin['database']);
		if($this->_initCsvBaser($dbConfig->baser['prefix'],$dbConfig->baser['database'])){
			if($this->_initCsvPlugin($dbConfig->plugin['prefix'],$dbConfig->plugin['database'])){
				// TODO baserとpluginのデータソースを別々に取得して処理を行った場合、ファイルロックの問題か固まってしまうので
				// baserのデータソースを使い回している。デモの場合、ファイルの場所が同じなのでとりあえずの処理。
				$db =& ConnectionManager::getDataSource('baser');
				$dataSources = array('baser','plugin');
				$ret = true;
				foreach ($dataSources as $dataSource){
					$folder = new Folder($dbConfig->{$dataSource}['database']);
					$files = $folder->read(true,true);
					
					foreach($files[1] as $file){
						if($file == $dbConfig->{$dataSource}['prefix'].'_blog_posts.csv'){
							$sql = "UPDATE `".str_replace(".csv",'',$file)."` SET `posts_date`='".date('Y-m-d H:i:s')."',`created`='".date('Y-m-d H:i:s')."', `modified`='".date('Y-m-d H:i:s')."' WHERE 1=1";
						}else{
							$sql = "UPDATE `".str_replace(".csv",'',$file)."` SET `created`='".date('Y-m-d H:i:s')."', `modified`='".date('Y-m-d H:i:s')."' WHERE 1=1";
						}
						$_ret = $db->execute($sql);
						if(!$_ret) $ret = $_ret;
					}
				}
				return $ret;
			}
		}else{
			return false;
		}
		
	}
/**
 * コアCSVの初期化
 */
	function _initCsvBaser($dbPrefix,$dbDBName){
			
		$targetDir = $dbDBName.DS;
		if(!is_dir($targetDir)){
			mkdir($targetDir,0777);
			chmod($targetDir,0777);
		}

		/* BaesrコアのCSVファイルをコピー */
		$sourceDir = BASER_CONFIGS.'csv'.DS.'baser'.DS;
		$folder = new Folder($sourceDir);
		$files = $folder->read(true,true);
		$ret = true;
		foreach($files[1] as $file){
			if($file != 'empty' && $ret){
				if (!file_exists($targetDir.$dbPrefix.$file)) {
					$_ret = copy($sourceDir.$file,$targetDir.$dbPrefix.$file);
					if ($_ret) {
						chmod($targetDir.$dbPrefix.$file,0666);
					}else{
						$ret = $_ret;
					}
				}
			}
			
		}

		return $ret;
		
	}
/**
 * プラグインCSVの初期化
 */
	function _initCsvPlugin($dbPrefix,$dbDBName){
		
		$targetDir = $dbDBName.DS;
		if(!is_dir($targetDir)){
			mkdir($targetDir,0777);
			chmod($targetDir,0777);
		}
		$ret = true;
		/* BaserプラグインのCSVファイルをコピー */
		$plugins = array('blog','feed','mail');
		foreach($plugins as $plugin){
			$sourceDir = BASER_PLUGINS.$plugin.DS.'config'.DS.'csv'.DS.$plugin.DS;
			$folder = new Folder($sourceDir);
			$files = $folder->read(true,true);
			foreach($files[1] as $file){
				if($file != 'empty' && $ret){
					if (!file_exists($targetDir.$dbPrefix.$file)) {
						$_ret = copy($sourceDir.$file,$targetDir.$dbPrefix.$file);
						if ($_ret) {
							chmod($targetDir.$dbPrefix.$file,0666);
						}else{
							$ret = $_ret;
						}
					}
				}
			}
		}
	
		return $ret;
		
	}
}
?>