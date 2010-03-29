<?php
/* SVN FILE: $Id$ */
/**
 * インストーラーコントローラー
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
 * @package			cake
 * @subpackage		cake.app.controllers
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
 * インストール条件
 *  @global string PHP_MINIMUM_VERSION
 *  @global integer PHP_MINIMUM_MEMORY_LIMIT in MB
 */
define("PHP_MINIMUM_VERSION","4.3.0");
define("PHP_MINIMUM_MEMORY_LIMIT", 16);
/**
 * インストーラーコントローラー
 */
class InstallationsController extends AppController
{
    var $name = 'installations';								// クラス名
    var $components = array('Session');							// コンポーネント
    var $layout = "installations";								// レイアウト
    var $helpers = array('Html', 'Form', 'Javascript', 'Time'); // ヘルパー
	var $uses = null;											// モデル
	var $webrootExists = false;
/**
 * データベースエラーハンドラ
 *
 * @param int		$errno
 * @param string	$errstr
 * @param string	$errfile
 * @param int		$errline
 * @param string	$errcontext
 * @return void
 * @access public
 */
    function dbErrorHandler( $errno, $errstr, $errfile=null, $errline=null, $errcontext=null ) {

		if ($errno==2) {
            $this->Session->setFlash("データベースへの接続でエラーが発生しました。データベース設定を見直して下さい。<br />".$errstr);
            restore_error_handler();
        }

    }
/**
 * インストール不能警告メッセージを表示
 * @return void
 */
    function alert(){
        $this->pageTitle = 'BaserCMSのインストールを開始できません';
    }
/**
 * beforeFilter
 *
 * @return void
 * @access public
 */
    function beforeFilter()
    {

       if(file_exists(CONFIGS.'database.php')){
            $db = ConnectionManager::getInstance();
            if($db->config->baser['driver'] != ''){
                $installed = 'complete';
            }else{
                $installed = 'half';
            }
        }else{
            $installed = 'yet';
        }

        switch ($this->action){
        case 'alert':
            break;
        case 'reset':
            if(Configure::read('debug') != -1){
                $this->notFound();
            }
            break;
        case 'update':
            break;
        default:
            if($installed == 'complete'){
                $this->notFound();
            }else{
                if(Configure::read('debug') == 0){
                    $this->redirect(array('action'=>'alert'));
                }
            }
            break;
        }


        $postdata= $this->data;

		if (strpos($this->webroot, 'webroot') === false) {
			$this->webroot = DS;
		}

		if(is_dir(APP.'webroot')){
			$this->webrootExists = true;
		}

		if(file_exists(dirname(APP).DS.'.htaccess') && (!$this->webrootExists || file_exists(APP.'webroot'.DS.'.htaccess'))){
			if(strpos($this->base, '/app/webroot/index.php') !== false){
                $this->base = str_replace('/app/webroot/index.php','',$this->base);
            }elseif(strpos($this->base, '/index.php') !== false){
                $this->base = str_replace('/index.php','',$this->base);
            }

			$corefilename=CONFIGS.'install.php';
			if(!file_exists($corefilename)){
				$installCoreData = array("<?php");
				$installCoreData[] = "Configure::write('App.baseUrl', '');";
				$installCoreData[] = "?>";
				file_put_contents($corefilename, implode("\n", $installCoreData));
				// rewrite設定が書いた.htaccess が既に存在している前提では、
				// install.phpが生成される前の段階では、$this->redirectがうまく動作しない。
				// rewriteされた同じ処理のURLにリダイレクトさせる事により、install.phpを読み込ませる。
				header('Location: '.$this->base.'/installations/'.$this->action);
			}
		}
		$this->theme = null;
    }
/**
 * Step 1: ウェルカムページ
 *
 * @return void
 * @access public
 */
    function index()
    {

		$this->pageTitle = 'BaserCMSのインストール';

       // キャッシュファイルを削除する（デバッグ用）
       if(is_writable(CACHE)){
            $this->deleteCache();
       }

    }
/**
 * Step 2: 必須条件チェック
 *
 * @return void
 * @access public
 */
    function step2() {

        $this->pageTitle = 'BaserCMSのインストール [ステップ２]';
        $this->set('phpminimumversion', PHP_MINIMUM_VERSION);
        $phpversionok= version_compare ( preg_replace('/[a-z-]/','', phpversion()),PHP_MINIMUM_VERSION,'>=');
        $this->set('phpversionok', $phpversionok);
        $this->set('phpactualversion', preg_replace('/[a-z-]/','', phpversion()));

        // PHP memory limit チェック
        $phpCurrentMemoryLimit = intval(ini_get('memory_limit'));
        $phpMemoryOk = ((($phpCurrentMemoryLimit >= PHP_MINIMUM_MEMORY_LIMIT) || $phpCurrentMemoryLimit == -1) === TRUE);
		// セーフモード
        $safemodeoff = !ini_get('safe_mode');
        // configs 書き込み権限
        $configdirwritable=is_writable(CONFIGS);
        // core.phpの書き込み権限
        $corefilewritable=is_writable(CONFIGS.'core.php');
		// DEMO用のページディレクトリの書き込み権限
		$demopagesdirwritable = is_writable(WWW_ROOT.'themed'.DS.'demo'.DS.'pages');
        // 一時フォルダの書き込み権限
        $tmpdirwritable = is_writable(TMP) && is_writable(TMP.'logs') && is_writable(TMP.'sessions') && is_writable(CACHE) && is_writable(CACHE.'models') && is_writable(CACHE.'persistent') && is_writable(CACHE.'views');
		// SQLiteディレクトリ書き込み権限
		$dbDirWritable = is_writable(APP.'db');

		// mod_rewrite モジュールインストール
		$apachegetmodules = function_exists('apache_get_modules');
        if($apachegetmodules){
			$modrewriteinstalled = in_array('mod_rewrite',apache_get_modules());
		}else{
			$modrewriteinstalled = false;
		}

        // メインディレクトリの書き込み権限
		$htaccesswritable=is_writable(dirname(APP)) && is_writable(APP) && is_writable(WWW_ROOT);
		// .htaccessの存在チェック
		$htaccessExists=file_exists(dirname(APP).DS.'.htaccess') && (!$this->webrootExists || file_exists(APP.'webroot'.DS.'.htaccess'));

		/* viewに変数をセット */
        $this->set('phpminimummemorylimit', PHP_MINIMUM_MEMORY_LIMIT);
        $this->set('phpcurrentmemorylimit', $phpCurrentMemoryLimit);
		$this->set('phpmemoryok', $phpMemoryOk);
		$this->set('apachegetmodules',$apachegetmodules);
        $this->set('modrewriteinstalled', $modrewriteinstalled);
        $this->set('configdirwritable', $configdirwritable);
        $this->set('corefilewritable',$corefilewritable);
        $this->set('htaccesswritable', $htaccesswritable);
		$this->set('htaccessExists', $htaccessExists);
        $this->set('safemodeoff', $safemodeoff);
		$this->set('dbDirWritable',$dbDirWritable);
        $this->set('tmpdirwritable',$tmpdirwritable);
		$this->set('demopagesdirwritable',$demopagesdirwritable);
		$this->set('blRequirementsMet', ($tmpdirwritable && $configdirwritable && $corefilewritable && $phpversionok && $demopagesdirwritable));

        /* ダミーのデータベース設定ファイルを保存 */
        $this->_writeDatabaseConfig('', 'localhost', '', 'dummy', 'dummy', 'dummy', '', '');
        $this->Session->write('modrewritesupport', ($modrewriteinstalled && $htaccesswritable));

    }
/**
 * Step 3: データベースの接続設定
 * @return void
 * @access public
 */
    function step3(){

        $this->pageTitle = 'BaserCMSのインストール [ステップ３]';
        $postdata= $this->data;
        $params = $this->params;
        $blDBSettingsOK = false;

		/* 戻るボタンクリック時 */
        if (isset($params['form']['step2'])) {
            $this->redirect('step4');
        }

		/* DBソース */
        $dbsource = array( 'mysql' => 'MySQL', 'postgres' => 'PostgreSQL');
        $sqlite = false;
        if(function_exists('sqlite_libversion') && is_writable(APP.'db')  &&
				class_exists('PDO') &&
            	version_compare ( preg_replace('/[a-z-]/','', phpversion()),'5','>=')){
        	$pdoDrivers = PDO::getAvailableDrivers();
            if(in_array('sqlite',$pdoDrivers)){

                /* /app/db/sqliteフォルダ確認＆生成 */
                if(is_dir(APP.'db'.DS.'sqlite')){
                    if(is_writable(APP.'db'.DS.'sqlite')){
                        $sqlite = true;
                    }else{
                        if(chmod(APP.'db'.DS.'sqlite',0777)){
                            $sqlite = true;
                        }
                    }
                }else{
                    if(mkdir(APP.'db'.DS.'sqlite') && chmod(APP.'db'.DS.'sqlite',0777)){
                        $sqlite = true;
                    }
                }
                if($sqlite){
                    $dbsource['sqlite3'] = 'SQLite3';
                }

            }else{
                // TODO SQLite2 は AlTER TABLE できないので、実装には、テーブル構造の解析が必要になる。
                // 一度一時テーブルを作成し、データを移動させた上で、DROP。
                // 新しい状態のテーブルを作成し、一時テーブルよりデータを移行。
                // その後、一時テーブルを削除する必要がある。
                // 【参考】http://seclan.dll.jp/dtdiary/2007/dt20070228.htm
                // プラグインコンテンツのアカウント変更時、メールフォームのフィールド変更時の処理を実装する。
                //$dbsource['sqlite'] = 'SQLite';
            }
        }

        $csv = false;
        if(is_writable(APP.'db')){
            /* /app/db/csvフォルダ確認＆生成 */
            if(is_dir(APP.'db'.DS.'csv')){
                if(is_writable(APP.'db'.DS.'csv')){
                    $csvWritable = true;
                }else{
                    if(chmod(APP.'db'.DS.'csv',0777)){
                        $csvWritable = true;
                    }
                }
            }else{
                if(mkdir(APP.'db'.DS.'csv') && chmod(APP.'db'.DS.'csv',0777)){
                    $csvWritable = true;
                }
            }
            /* /app/db/csv/baserフォルダ確認＆生成 */
            if(is_dir(APP.'db'.DS.'csv'.DS.'baser')){
                if(is_writable(APP.'db'.DS.'csv'.DS.'baser')){
                    $csv = true;
                }else{
                    if(chmod(APP.'db'.DS.'csv'.DS.'baser',0777)){
                        $csv = true;
                    }
                }
            }else{
                if(mkdir(APP.'db'.DS.'csv'.DS.'baser') && chmod(APP.'db'.DS.'csv'.DS.'baser',0777)){
                    $csv = true;
                }
            }

            if($csv){
                $dbsource['csv'] = 'CSV';
            }
		}

		$this->set('dbsource', $dbsource);

		/* DBタイプ */
        if (isset($postdata['installation']['dbType'])) {
            $this->set('defaultdb', $postdata['installation']['dbType']);
        } else {
            $this->set('defaultdb', 'mysql');
        }

		/* DBポート */
        if (isset($postdata['installation']['dbPort'])) {
            $this->set('dbPort', $postdata['installation']['dbPort']);
        } else {
            $this->set('dbPort', '3306');
        }

        if (isset($postdata['installation']['dbHost'])){
            $this->set('dbHost', $postdata['installation']['dbHost']);
        } else {
            $this->set('dbHost', 'localhost');
        }

        if (isset($postdata['installation']['dbDBName'])){
            $this->set('dbDBName', $postdata['installation']['dbDBName']);
        } else {
            $this->set('dbDBName', 'baser');
        }

		/* DBプレフィックス */
        $dbPrefix = '';
        if (isset($postdata['installation']['dbPrefix'])) {
            $this->set('dbPrefix', $postdata['installation']['dbPrefix']);
        } else {
			$this->set('dbPrefix','bc_');
        }

        if($postdata['installation']['dbType'] == 'postgres'){
            $postdata['installation']['dbSchema'] = 'public'; // TODO とりあえずpublic固定
        }else{
            $postdata['installation']['dbSchema'] = '';
        }

		if(!empty($postdata['installation']['dbType']) && !empty($postdata['installation']['dbDBName'])){
			$dbType = str_replace('_ex','',$postdata['installation']['dbType']);
			if($dbType == 'sqlite' || $dbType == 'sqlite3'){
				$database = APP.'db'.DS.'sqlite'.DS.'baser.db';
			}elseif($dbType == 'csv'){
				$database = APP.'db'.DS.'csv'.DS.'baser';
			}else{
				$database = $postdata['installation']['dbDBName'];
			}
		}

        /* 接続テスト */
        // CSV , SQLite は 接続テストが不要な為スキップする
        if (isset($params['data']['buttonclicked']) &&
                $params['data']['buttonclicked']=='checkdb') {

            set_error_handler(array($this, "dbErrorHandler"));

            /* ポート指定がない場合は空欄にする */
            if ($postdata['installation']['dbPort']=='default') {
                $postdata['installation']['dbPort']='';
            }

			/* データベース接続生成 */
            App::import('ConnectionManager');
            $db = &ConnectionManager::create('test',array(  'driver' => $postdata['installation']['dbType'],
                                                            'persistent' => false,
                                                            'host' => $postdata['installation']['dbHost'],
                                                            'port' => $postdata['installation']['dbPort'],
                                                            'login' => $postdata['installation']['dbUsername'],
                                                            'password' => $postdata['installation']['dbPassword'],
                                                            'database' => $database,
                                                            'schema' => $postdata['installation']['dbSchema'],
                                                            'prefix' =>  $dbPrefix,
                                                            'encoding' => 'utf8'));

            if ($db->connected) {

                /* 一時的にテーブルを作成できるかテスト */
                $randomtablename='deleteme'.rand(100,100000);
                $result = $db->execute("CREATE TABLE $randomtablename (a varchar(10))");

                if (!isset($db->error)) {
                    $result = $db->execute("drop TABLE $randomtablename");
                }

                if (!isset($db->error)) {
                    $blDBSettingsOK = true;
                    $this->Session->setFlash('データベースへの接続に成功しました。');
                    $this->set('blDBSettingsOK',$blDBSettingsOK);
                }

            }



        /* 「次のステップへ」クリック時 */
        } elseif (isset($params['data']['buttonclicked']) &&
                $params['data']['buttonclicked']=='createdb') {

            /* データベース設定をセッションに保存する */
            $this->Session->write('dbType', $postdata['installation']['dbType']);
            $this->Session->write('dbHost', $postdata['installation']['dbHost']);
            $this->Session->write('dbPort', $postdata['installation']['dbPort']);
            $this->Session->write('dbUsername', $postdata['installation']['dbUsername']);
            $this->Session->write('dbPassword', $postdata['installation']['dbPassword']);
            $this->Session->write('dbPrefix', $postdata['installation']['dbPrefix']);
            $this->Session->write('dbDBName', $database);
            $this->Session->write('dbSchema',$postdata['installation']['dbSchema']);
            $this->autoRender = false;
            $this->redirect('step4');

        }

    }
/**
 * Step 4: データベース生成／管理者ユーザー作成
 *
 * @return void
 * @access public
 */
    function step4()     {

        $this->pageTitle = 'BaserCMSのインストール [ステップ４]';
        $dbType = $this->Session->read('dbType');
        App::import('ConnectionManager');
        App::import('Vendor','dbrestore');

        if (isset($postdata['installation']['admin_username'])){
            $this->set('adminUsername', $postdata['installation']['admin_username']);
        } else {
            $this->set('adminUsername', 'admin');
        }
		if (isset($postdata['installation']['admin_email'])){
			$this->set('adminEmail', $postdata['installation']['admin_email']);
		} else {
			$this->set('adminEmail', '');
		}

        $db = &ConnectionManager::create('baser',array( 'driver' => $dbType,
                                                        'persistent' => false,
                                                        'host' => $this->Session->read('dbHost'),
                                                        'port' => $this->Session->read('dbPort'),
                                                        'login' => $this->Session->read('dbUsername'),
                                                        'password' => $this->Session->read('dbPassword'),
                                                        'database' => $this->Session->read('dbDBName'),
                                                        'schema' => $this->Session->read('dbSchema'),
                                                        'prefix' =>  $this->Session->read('dbPrefix'),
                                                        'encoding' => 'utf8'));

        /* データベースを構築する */
        if ($db->connected || $dbType=='csv') {
            switch ($dbType) {
            case 'mysql':
            case 'mysqli':
                // 文字コードの設定を行う
                $db->execute('ALTER DATABASE '.$db->startQuote.$this->Session->read('dbDBName').$db->endQuote.' CHARACTER SET utf8 COLLATE utf8_unicode_ci');
                $dbRestore = new DbRestore($dbType);
                $dbRestore->connect($this->Session->read('dbDBName'), $this->Session->read('dbHost'), $this->Session->read('dbUsername'), $this->Session->read('dbPassword'),$this->Session->read('dbPort'));
                $ret = $dbRestore->doRestore(BASER_CONFIGS.'sql'.DS.'baser_'.$dbType.'.sql');
                if($ret) $ret = $dbRestore->doRestore(BASER_PLUGINS.'mail'.DS.'config'.DS.'sql'.DS.'mail_'.$dbType.'.sql');
                if($ret) $ret = $dbRestore->doRestore(BASER_PLUGINS.'feed'.DS.'config'.DS.'sql'.DS.'feed_'.$dbType.'.sql');
                if($ret) $ret = $dbRestore->doRestore(BASER_PLUGINS.'blog'.DS.'config'.DS.'sql'.DS.'blog_'.$dbType.'.sql');
                break;

            case 'postgres':
                // 文字コードの設定を行う
                $dbRestore = new DbRestore($dbType);
                $dbRestore->connect($this->Session->read('dbDBName'), $this->Session->read('dbHost'), $this->Session->read('dbUsername'), $this->Session->read('dbPassword'),$this->Session->read('dbPort'));
                $ret = $dbRestore->doRestore(BASER_CONFIGS.'sql'.DS.'baser_'.$dbType.'.sql');
                if($ret) $ret = $dbRestore->doRestore(BASER_PLUGINS.'mail'.DS.'config'.DS.'sql'.DS.'mail_'.$dbType.'.sql');
                if($ret) $ret = $dbRestore->doRestore(BASER_PLUGINS.'feed'.DS.'config'.DS.'sql'.DS.'feed_'.$dbType.'.sql');
                if($ret) $ret = $dbRestore->doRestore(BASER_PLUGINS.'blog'.DS.'config'.DS.'sql'.DS.'blog_'.$dbType.'.sql');
                break;

            case 'sqlite':
            case 'sqlite3':

                $dbRestore = new DbRestore($dbType);
                $dbRestore->connect($this->Session->read('dbDBName'));
                $ret = $dbRestore->doRestore(BASER_CONFIGS.'sql'.DS.'baser_sqlite.sql');
                if($ret) $ret = $dbRestore->doRestore(BASER_PLUGINS.'mail'.DS.'config'.DS.'sql'.DS.'mail_sqlite.sql');
                if($ret) $ret = $dbRestore->doRestore(BASER_PLUGINS.'feed'.DS.'config'.DS.'sql'.DS.'feed_sqlite.sql');
                if($ret) $ret = $dbRestore->doRestore(BASER_PLUGINS.'blog'.DS.'config'.DS.'sql'.DS.'blog_sqlite.sql');
                chmod($this->Session->read('dbDBName'),0666);
                break;

            case 'csv':
                $dbPrefix = $this->Session->read('dbPrefix');
                $targetDir = $this->Session->read('dbDBName').DS;
                if(!is_dir(APP.'db'.DS.'csv')){
                    mkdir(APP.'db'.DS.'csv',0777);
					chmod(APP.'db'.DS.'csv',0777);
                }
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
                /* BaserプラグインのCSVファイルをコピー */
				$plugins = array('blog','feed','mail');
				foreach($plugins as $plugin){
					$sourceDir = BASER_PLUGINS.$plugin.DS.'config'.DS.'csv'.DS.$plugin.DS;
                    $folder = new Folder($sourceDir);
                    $files = $folder->read(true,true);
                    foreach($files[1] as $file){
                        if($file != 'empty' && $ret){
                            if (!file_exists($targetDir.$dbPrefix.'_'.$file)) {
                                $_ret = copy($sourceDir.$file,$targetDir.$dbPrefix.'_'.$file);
                                if ($_ret) {
                                    chmod($targetDir.$dbPrefix.'_'.$file,0666);
                                }else{
                                    $ret = $_ret;
                                }
                            }
                        }
                    }
				}
                break;

            }

            if($ret){
                $this->Session->setFlash("データベースの構築に成功しました。");
            }else{
                $this->Session->setFlash("データベースの構築中にエラーが発生しました。");
            }

         }else{
			 $this->Session->setFlash("データベースに接続できませんでした。");
		 }

    }
/**
 * Step 5: 設定ファイルの生成
 *
 * データベース設定ファイル[database.php]
 * インストールファイル[install.php]
 * @return void
 * @access public
 */
    function step5()     {

        $this->pageTitle = 'BaserCMSのインストール完了！';
        $postdata= $this->data;

		/* セキュリティsaltの設定 */
        $salt = $this->createKey(40);
        Configure::write('Security.salt',$salt);
        $installCoreData = array("<?php","Configure::write('Security.salt', '".$salt ."');");

        // データベース設定ファイルに設定内容を書き込む
        $this->_writeDatabaseConfig($this->Session->read('dbType'),
                                    $this->Session->read('dbHost'),
                                    $this->Session->read('dbPort'),
                                    $this->Session->read('dbUsername'),
                                    $this->Session->read('dbPassword'),
                                    $this->Session->read('dbDBName'),
                                    $this->Session->read('dbPrefix'),
                                    $this->Session->read('dbSchema'));

        /* ユーザーを生成 */
        App::import('ConnectionManager');
        $db = &ConnectionManager::create('baser',array('driver' => $this -> Session->read('dbType'),
                                                        'persistent' => false,
                                                        'host' => $this->Session->read('dbHost'),
                                                        'port' => $this->Session->read('dbPort'),
                                                        'login' => $this->Session->read('dbUsername'),
                                                        'password' => $this->Session->read('dbPassword'),
                                                        'database' => $this->Session->read('dbDBName'),
                                                        'schema' => $this->Session->read('dbSchema'),
                                                        'prefix' =>  $this->Session->read('dbPrefix'),
                                                        'encoding' => 'utf8'));

        if ($db->connected ||  $this->Session->read('dbType') == 'csv') {

            App::import('Model','SiteConfig');
            $siteConfig['SiteConfig']['email'] = $postdata['installation']['admin_email'];
            $SiteConfigClass = new SiteConfig();
            $SiteConfigClass->saveKeyValue($siteConfig);

            // 管理ユーザー登録
            // TODO モデルでの処理に書き換える
            $admin = $postdata['installation']['admin_username'];
            $password = $postdata['installation']['admin_password'];
            $hashedPassword = Security::hash($password,null,true);
            if ($this->Session->read('dbType')=='sqlite' || $this->Session->read('dbType') =='sqlite3') {
                $date = date('Y/m/d H:i:s');
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $db->execute("insert into ".$this->Session->read('dbPrefix')."users (name,real_name_1,real_name_2,password,email,user_group_id,created,modified) values ('{$admin}','{$admin}','','".$hashedPassword."','',1,'".$date."','".$date."')");

            // CSVの際には、作成日の更新を行う
            if($this->Session->read('dbType') == 'csv'){
                $folder = new Folder($this->Session->read('dbDBName'));
                $files = $folder->read(true,true);
                foreach($files[1] as $file){
                    // TODO 上記で一度インサートしてハンドリングしているせいか、usersに更新をかけるとヘッダーの定義がsite_configsになってしまう。
                    // 日付の更新は上でできているのでとりあえず除外する。
                    if($file != 'empty' && $file != $this->Session->read('dbPrefix').'users.csv'){
                        if($file == $this->Session->read('dbPrefix').'_blog_posts.csv'){
                            $sql = "UPDATE ".str_replace(".csv",'',$file)." SET posts_date='".date('Y-m-d H:i:s')."',created='".date('Y-m-d H:i:s')."', modified='".date('Y-m-d H:i:s')."' WHERE 1=1";
                        }else{
                            $sql = "UPDATE ".str_replace(".csv",'',$file)." SET created='".date('Y-m-d H:i:s')."', modified='".date('Y-m-d H:i:s')."' WHERE 1=1";
                        }
                        $db->execute($sql);
                    }
                }
            }

            // ログイン
            $extra['data']['User']['name'] = $admin;
            $extra['data']['User']['password'] = $password;
            $this->requestAction('/admin/users/login_exec', $extra);

            if ($db->error) {
                $this->set('usercreateerror','管理ユーザーを作成できませんでした。: '.$db->error);
            }

        }



        // demo用テーマを配置する
        /*$targetPath = WWW_ROOT.'themed'.DS.'demo'.DS;
        $sourcePath = BASER_CONFIGS.'themed'.DS.'demo'.DS;
        $folder = new Folder();
        $folder->create($targetPath,0777);
        $folder = new Folder($sourcePath);
        $files = $folder->read(true, true);
        foreach($files[0] as $file){
            $folder->copy(array('to'=>$targetPath.$file,'from'=>$sourcePath.$file,'mode'=>0777));
        }
        foreach($files[1] as $file){
            copy($sourcePath.$file,$targetPath.$file);
            chmod($targetPath,0666);
        }*/

        // demoテーマ用のpagesファイルを生成する
        App::import('Model','Page');
        $Page = new Page(null, null, 'baser');
        $pages = $Page->findAll();
        foreach($pages as $page){
            $Page->data = $page;
            $Page->afterSave();
        }

        /* mod_rewrite 設定 */
		$htaccessEnabled = $htaccess1 = $htaccess3 = false;

		if (!file_exists(dirname(APP).DS.'.htaccess') && !file_exists(APP.'webroot'.DS.'.htaccess')) {

			if ($this->Session->read('modrewritesupport')) {

                /* /.htaccess */
                if (copy(dirname(APP).DS.'htaccess.txt',dirname(APP).DS.'.htaccess')===false) {
                    $this->set('modrewriteenableerror','/htaccess.txt ファイル名の変更ができませんでした。パーミッションを確認して下さい。');
                } else {
                    chmod(dirname(APP).DS.'.htaccess',0666);
                    $htaccess1 = true;
                }

                /* /app/webroot/.htaccess */
                if (ROOT.DS != WWW_ROOT) {
                    if (!copy(APP.'webroot'.DS.'htaccess.txt',WWW_ROOT.'.htaccess')) {
                        $this->set('modrewriteenableerror','/app/webroot/htaccess.txt ファイル名の変更ができませんでした。パーミッションを確認して下さい。');
                    } else {
                        chmod(WWW_ROOT.'.htaccess',0666);
                        $htaccess3 = true;
                    }
                } else {
                    $htaccess3 = true;
                }

                if ($htaccess1 && $htaccess3) {
                    $htaccessEnabled = true;
                }

            }

        } else {
			/* 既に有効な場合 */
			$this->set('modrewritealreadyenabled',true);
			$htaccessEnabled = true;
		}

		if($htaccessEnabled){
			$this->set('fancyurl', true);
			$installCoreData[] = "Configure::write('App.baseUrl', '');";
		}

        /* インストールファイル生成 */
        $installCoreData[] = "?>";
        $corefilename=CONFIGS.'install.php';
        $status = file_put_contents($corefilename, implode("\n", $installCoreData));
		chmod($corefilename,0666);

        /* デバッグモードを0に変更 */
        $this->writeDebug(0);

		if ($status === FALSE) {
			$this->set('modrewriteenableerror',"/app/config/install.php インストール設定ファイルの設定ができませんでした。パーミションの確認をして下さい。");
		}

        $this->set('fancybase', str_replace("/index.php", "", $this->base));

        if (strncasecmp(PHP_OS, 'win', 3) != 0) {

            $file1 = CONFIGS. 'database.php';
            $file2 = CONFIGS. 'core.php';
            $file3 = CONFIGS. 'install.php';

            $stat = stat($file1);
            $fileMode1 = substr(sprintf('%o', $stat['mode']), -3);
            $stat = stat($file2);
            $fileMode2 = substr(sprintf('%o', $stat['mode']), -3);
            $stat = stat(CONFIGS);
            $dirMode = substr(sprintf('%o', $stat['mode']), -3);

            if (file_exists($file3)) {
                $stat = stat($file2);
                $fileMode3 = substr(sprintf('%o', $stat['mode']), -3);
                $fileModesecure3 = !((int)$fileMode3{1} & 2 || (int)$fileMode3{2} & 2);
            } else {
                $fileModesecure3 = true;
            }

            $fileModesecure1 = !((int)$fileMode1{1} & 2 || (int)$fileMode1{2} & 2);
            $fileModesecure2 = !((int)$fileMode2{1} & 2 || (int)$fileMode2{2} & 2);
            $dirModesecure = !((int)$dirMode{1} & 2 || (int)$dirMode{2} & 2);
            $this->set('secure', $fileModesecure1 && $fileModesecure2 && $fileModesecure3 && $dirModesecure);

        } else {
            $this->set('secure',true);
        }

    }
/**
 * データベース設定ファイル[database.php]を保存する
 *
 * @param string データベースタイプ
 * @param string ホスト
 * @param string ユーザー名
 * @param string パスワード
 * @param string データベース名
 * @param string Table Prefix
 * @return boolean
 * @access private
 */
    function _writeDatabaseConfig($dbType, $dbHost, $dbPort, $dbUsername, $dbPassword, $dbDBName, $dbPrefix, $dbSchema, $dbEncoding='utf8')
    {

        App::import('File');

        $dbfilename=CONFIGS.'database.php';
        $dbfilehandler = & new File($dbfilename);

        if ($dbfilehandler!==false) {

            if ($dbfilehandler->exists()) {
                $dbfilehandler->delete();
            }

            if($dbType == 'mysql' || $dbType == 'sqlite3' || $dbType == 'postgres'){
                $dbType .= '_ex';
            }

            $dbfilehandler->create();
            $dbfilehandler->open('w',true);
            $dbfilehandler->write("<?php\n");
            $dbfilehandler->write("//\n");
            $dbfilehandler->write("// Database Configuration File created by BaserCMS Installation\n");
            $dbfilehandler->write("//\n");
            $dbfilehandler->write("class DATABASE_CONFIG {\n");
            $dbfilehandler->write('var $baser = array('."\n");
            $dbfilehandler->write("\t'driver' => '".$dbType."',\n");
            $dbfilehandler->write("\t'persistent' => false,\n");
            $dbfilehandler->write("\t'host' => '".$dbHost."',\n");
            if ($dbPort=='default') { $dbPort=''; }
            $dbfilehandler->write("\t'port' => '".$dbPort."',\n");
            $dbfilehandler->write("\t'login' => '".$dbUsername."',\n");
            $dbfilehandler->write("\t'password' => '".$dbPassword."',\n");
            $dbfilehandler->write("\t'database' => '".$dbDBName."',\n");
            $dbfilehandler->write("\t'schema' => '".$dbSchema."',\n");
            $dbfilehandler->write("\t'prefix' => '".$dbPrefix."',\n");
            $dbfilehandler->write("\t'encoding' => '".$dbEncoding."'\n");
            $dbfilehandler->write(");\n");
            $dbfilehandler->write('var $plugin = array('."\n");
            $dbfilehandler->write("\t'driver' => '".$dbType."',\n");
            $dbfilehandler->write("\t'persistent' => false,\n");
            $dbfilehandler->write("\t'host' => '".$dbHost."',\n");
            if ($dbPort=='default') { $dbPort=''; }
            $dbfilehandler->write("\t'port' => '".$dbPort."',\n");
            $dbfilehandler->write("\t'login' => '".$dbUsername."',\n");
            $dbfilehandler->write("\t'password' => '".$dbPassword."',\n");
            $dbfilehandler->write("\t'database' => '".$dbDBName."',\n");
            $dbfilehandler->write("\t'schema' => '".$dbSchema."',\n");
            $dbfilehandler->write("\t'prefix' => '".$dbPrefix."_',\n");
            $dbfilehandler->write("\t'encoding' => '".$dbEncoding."'\n");
            $dbfilehandler->write(");\n");
            $dbfilehandler->write("}\n");
            $dbfilehandler->write("?>\n");

            $dbfilehandler->close();
            return true;

        } else {
            return false;
        }

    }
/**
 * セキュリティ用のキーを生成する
 *
 * @param int $length
 * @return string   キー
 */
    function createKey($length){

        $keyset = "abcdefghijklmABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $randkey = "";
        for ($i=0; $i<$length; $i++)
            $randkey .= substr($keyset, rand(0,strlen($keyset)-1), 1);
        return $randkey;

    }
/**
 * BaserCMSを初期化する
 * @return void
 */
    function reset(){

        $this->pageTitle = 'BaserCMSの初期化';

        if($this->data['Installation']['reset']){

           if(file_exists(CONFIGS.'database.php')){
                // データベースのデータを削除
                $this->_resetDatabase();
                unlink(CONFIGS.'database.php');
           }
           if(file_exists(CONFIGS.'install.php')){
                unlink(CONFIGS.'install.php');
           }

           $messages = array();
           if(file_exists(dirname(APP).DS.'.htaccess')){
               if(@!unlink(dirname(APP).DS.'.htaccess')){
                   $messages[] = str_replace($_SERVER['DOCUMENT_ROOT'],'',dirname(APP)).DS.'.htaccess は削除できませんでした。';
               }
           }
           if(file_exists(WWW_ROOT.'.htaccess')){
               if(@!unlink(WWW_ROOT.'.htaccess')){
                   $messages[] = str_replace($_SERVER['DOCUMENT_ROOT'],'',WWW_ROOT).'.htaccess は削除できませんでした。';
               }
           }

           if($messages){
               $messages[] = '手動でサーバー上より上記ファイルを削除して初期化を完了させてください。';
           }

           $messages = am(array('BaserCMSを初期化しました。',''),$messages);

           $message = implode('<br />', $messages);

           $this->deleteCache();
           $this->Session->setFlash($message);
           $complete = true;

        }else{
            $complete = false;
        }

        $this->set('complete', $complete);

    }
/**
 * データベースを初期化する
 * @param array $dbConfig
 */
    function _resetDatabase(){

        /* データベース設定を取得 */
        $dbType = $this->Session->read('dbType');
        if($dbType){
            // インストール途中の場合はセッションから取得
            $db = &ConnectionManager::create('test',array(  'driver' => $this->Session->read('dbType'),
                                                            'persistent' => false,
                                                            'host' => $this->Session->read('dbHost'),
                                                            'port' => $this->Session->read('dbPort'),
                                                            'login' => $this->Session->read('dbUsername'),
                                                            'password' => $this->Session->read('dbPassword'),
                                                            'database' => $this->Session->read('dbDBName'),
                                                            'schema' => $this->Session->read('dbSchema'),
                                                            'prefix' =>  $this->Session->read('dbPrefix'),
                                                            'encoding' => 'utf8'));
            $dbConfig = $db->config;
        }elseif(class_exists('DATABASE_CONFIG')){
            $dbConfig = new DATABASE_CONFIG();
            $dbConfig = $dbConfig->baser;
            if(empty($dbConfig['driver'])){
                return;
            }
            $db =& ConnectionManager::getDataSource('baser');
        }

        /* 削除実行 */
        // TODO schemaを有効活用すればここはスッキリしそうだが見送り
        $dbType = str_replace('_ex','',$dbConfig['driver']);
        switch ($dbType) {
        case 'mysql':
            $sources = $db->listSources();
            foreach($sources as $source){
                $sql = 'DROP TABLE '.$source;
                $db->execute($sql);
            }
            break;

        case 'postgres':
            $sources = $db->listSources();
            foreach($sources as $source){
                $sql = 'DROP TABLE '.$source;
                $db->execute($sql);
            }
            // シーケンスも削除
            $sql = "SELECT sequence_name FROM INFORMATION_SCHEMA.sequences WHERE sequence_schema = '{$dbConfig['schema']}';";
            $sequences = $db->query($sql);
            $sequences = Set::extract('/0/sequence_name',$sequences);
            foreach($sequences as $sequence){
                $sql = 'DROP SEQUENCE '.$sequence;
                $db->execute($sql);
            }
            break;

        case 'sqlite':
        case 'sqlite3':
            @unlink($dbConfig['database']);
            break;

        case 'csv':
            $folder = new Folder($dbConfig['database']);
            $files = $folder->read(true,true,true);
            foreach($files[1] as $file){
                if(basename($file) != 'empty'){
                    @unlink($file);
                }
            }
            break;

        }

    }
/**
 * アップデートを実行する
 */
    function update(){

        $this->pageTitle = 'WEBサイトアップデート';

		$this->deleteCache();

        /* バージョンを解析 */
        $baserVersion = $this->getBaserVersion();
        $baserVer = preg_replace("/BaserCMS ([0-9\.]+?[\sa-z]*)/is","$1",$baserVersion);
        $baserVerpoint = verpoint($baserVersion);
        if(isset($this->siteConfigs['version'])){
            $siteVer = preg_replace("/BaserCMS ([0-9\.]+?[\sa-z]*)/is","$1",$this->siteConfigs['version']);
            $siteVerpoint = verpoint($this->siteConfigs['version']);
        }else{
            $siteVer = 'バージョンなし';
            $siteVerpoint = 0;
        }

        /* スクリプトを走査 */
        $folder = new Folder(BASER_CONFIGS.'update');
        $files = $folder->read(true,true);
        $scriptNum = 0;
        $scripts = array();
        if(!empty($files[1])){
            foreach ($files[1] as $file){
                if(preg_match("/(.*?)\.php$/is", $file , $matches)){
                    $scriptRev = $matches[1];
                    if($scriptRev > $siteVerpoint && $scriptRev <= $baserVerpoint){
                        $scriptNum++;
                        $scripts[] = $file;
                    }
                }
            }
        }

        /* スクリプト実行 */
        if($this->data){
            asort($scripts);
            $updateMessage = array();
            foreach($scripts as $script){
                include BASER_CONFIGS.'update'.DS.$script;
            }

            /* サイト基本設定にバージョンを保存 */
            $SiteConfigClass = ClassRegistry::getObject('SiteConfig');
            $data['SiteConfig']['version'] = $baserVersion;
            $SiteConfigClass->saveKeyValue($data);

            if($updateMessage){
                $updateMessage[] = '';
            }
            $updateMessage[] = 'アップデートが完了しました。';
            $this->Session->setFlash(implode('<br />',$updateMessage));

            $this->redirect(array('action'=>'update'));
        }

        $this->set('siteVer',$siteVer);
        $this->set('baserVer',$baserVer);
        $this->set('scriptNum',$scriptNum);

    }
}
?>