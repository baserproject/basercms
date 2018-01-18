<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Uploader.Controller
 * @since			baserCMS v 3.0.10
 * @license			http://basercms.net/license/index.html
 */

/**
 * ファイルアップローダーコントローラー
 *
 * @package			Uploader.Controller
 */
class UploaderFilesController extends AppController {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
	public $name = 'UploaderFiles';
/**
 * コンポーネント
 *
 * @var		array
 * @access	public
 */
	public $components = array('BcAuth','Cookie','BcAuthConfigure','RequestHandler');
/**
 * ヘルパー
 *
 * @var		array
 * @access	public
 */
	public $helpers = array('BcText', 'BcTime', 'BcForm', 'Uploader.Uploader', 'BcUpload');
/**
 * ページタイトル
 *
 * @var		string
 * @access	public
 */
	public $pageTitle = 'アップローダープラグイン';
/**
 * モデル
 *
 * @var		array
 * @access	public
 */
	public $uses = array('Plugin','Uploader.UploaderFile', 'Uploader.UploaderConfig');
/**
 * ぱんくずナビ
 *
 * @var array
 */
	public $crumbs = array(
		array('name' => 'プラグイン管理', 'url' => array('plugin' => '', 'controller' => 'plugins', 'action' => 'index')),
		array('name' => 'アップロードファイル管理', 'url' => array('controller' => 'uploader_files', 'action' => 'index'))
	);
/**
 * サブメニューエレメント
 *
 * @var 	array
 * @access 	public
 */
	public $subMenuElements = array('uploader');
	public function beforeFilter() {
		$this->BcAuth->allow('view_limited_file');
		parent::beforeFilter();
	}
/**
 * [ADMIN] ファイル一覧
 *
 * @param	int		$id		呼び出し元 識別ID
 * @param	string	$filter
 * @return	void
 * @access	public
 */
	public function admin_index($id='') {

		if(!isset($this->siteConfigs['admin_list_num'])) {
			$this->siteConfigs['admin_list_num'] = 10;
		}
		$default = array('named' => array('num' => $this->siteConfigs['admin_list_num']));
		$this->setViewConditions('UploadFile', array('default' => $default));
		$this->set('uploaderConfigs', $this->UploaderConfig->findExpanded());
		$this->set('installMessage', $this->checkInstall());
		
		if($this->RequestHandler->isAjax()) {
			$settings = $this->UploaderFile->Behaviors->BcUpload->settings;
			$this->set('listId', $id);
			$this->set('imageSettings', $settings['UploaderFile']['fields']['name']['imagecopy']);
		} else {
			$this->search = 'uploader_files_index';
			$this->pageTitle = 'アップロードファイル一覧';
		}

	}
/**
 * インストール状態の確認
 *
 * @return	string	インストールメッセージ
 */
	protected function checkInstall() {

		// インストール確認
		$installMessage = '';
		$viewFilesPath = str_replace(ROOT,'',WWW_ROOT).'files';
		$viewSavePath = $viewFilesPath.DS.$this->UploaderFile->actsAs['BcUpload']['saveDir'];
		$filesPath = WWW_ROOT.'files';
		$savePath = $filesPath.DS.$this->UploaderFile->actsAs['BcUpload']['saveDir'];
		if(!is_dir($savePath)) {
			$ret = mkdir($savePath,0777);
			if(!$ret) {
				if(is_writable($filesPath)) {
					$installMessage = $viewSavePath.' を作成し、書き込み権限を与えてください';
				}else {
					if(!is_dir($filesPath)) {
						$installMessage = $viewFilesPath.' 作成し、に書き込み権限を与えてください';
					}else {
						$installMessage = $viewFilesPath.' に書き込み権限を与えてください';
					}
				}
			}
		}else {
			if(!is_writable($savePath)) {
				$installMessage = $viewSavePath.' に書き込み権限を与えてください';
			}else {

			}
		}
		return $installMessage;

	}
/**
 * [ADMIN] ファイル一覧を表示
 *
 * ファイルアップロード時にリダイレクトされた場合、
 * RequestHandlerコンポーネントが作動しないので明示的に
 * レイアウト、デバッグフラグの設定をする
 *
 * @param	int		$id		呼び出し元 識別ID
 * @param	string	$filter
 * @return	void
 * @access	public
 */
	public function admin_ajax_list($id='') {

		Configure::write('debug',0);
		
		$default = array('named' => array('num' => $this->siteConfigs['admin_list_num']));
		$this->setViewConditions('UploadFile', array('default' => $default, 'type' => 'get'));

		$this->request->data['Filter'] = $this->passedArgs;
		if(empty($this->request->data['Filter']['uploader_type'])) {
			$this->request->data['Filter']['uploader_type'] = 'all';
		}
		if(!empty($this->request->data['Filter']['name'])) {
			$this->request->data['Filter']['name'] = urldecode($this->request->data['Filter']['name']);
		}
			
		// =====================================================================
		// setViewConditions で type を get に指定した場合、
		// 自動的に $this->passedArgs['num'] 設定されないので明示的に取得
		// TODO setViewConditions の仕様を見直す
		// =====================================================================
		if($this->params['named']['num']) {
			$this->Session->write('UploaderFilesAdminAjaxList.named.num', $this->params['named']['num']);
		}
		if($this->Session->read('UploaderFilesAdminAjaxList.named.num')) {
			$num = $this->Session->read('UploaderFilesAdminAjaxList.named.num');
		} else {
			$num = $this->siteConfigs['admin_list_num'];
		}

		$conditions = $this->_createAdminIndexConditions($this->request->data['Filter']);

		// 管理ユーザ以外が利用時、ユーザ制限がOnになっていれば一覧に表示しない
		$uploaderConfig = $this->UploaderConfig->findExpanded();
		if(isset($uploaderConfig['use_permission']) && $uploaderConfig['use_permission'] && !BcUtil::isAdminUser()) {
			$user = BcUtil::loginUser();
			if ($user) $conditions['UploaderFile.user_id'] = $user['id'];
		}

		$this->paginate = array('conditions'=>$conditions,
				'fields'=>array(),
				'order'=>'created DESC',
				'limit'=>$num
		);
		
		$dbDatas = $this->paginate('UploaderFile');
		
		foreach($dbDatas as $key => $dbData) {
			$limited = (!empty($dbData['UploaderFile']['publish_begin']) || !empty($dbData['UploaderFile']['publish_end']));
			$files = $this->UploaderFile->filesExists($dbData['UploaderFile']['name'], $limited);
			$dbData = Set::merge($dbData,array('UploaderFile'=>$files));
			$dbDatas[$key] = $dbData;
		}
		
		$this->set('installMessage', $this->checkInstall());
		$uploaderConfig = $this->UploaderConfig->findExpanded();
		$this->set('listId', $id);
		$this->set('files',$dbDatas);
		if(empty($uploaderConfig['layout_type'])) {
			$layoutType = 'panel';
		} else {
			$layoutType = 'table';
		}
		$this->set('layoutType', $uploaderConfig['layout_type']);
		
	}
/**
 * 一覧の検索条件を生成する
 * 
 * @param array $data
 * @return array 
 */
	protected function _createAdminIndexConditions($data) {
		
		$conditions = array();
		if(!empty($data['uploader_category_id'])) {
			$conditions = array('UploaderFile.uploader_category_id' => $data['uploader_category_id']);
			$this->request->data['Filter']['uploader_category_id'] = $data['uploader_category_id'];
		}
		if(!empty($data['uploader_type'])) {
			switch ($data['uploader_type']) {
				case 'img':
					$conditions['or'][] = array('UploaderFile.name LIKE' => '%.png');
					$conditions['or'][] = array('UploaderFile.name LIKE' => '%.jpg');
					$conditions['or'][] = array('UploaderFile.name LIKE' => '%.gif');
					break;
				case 'etc':
					$conditions['and'][] = array('UploaderFile.name NOT LIKE' => '%.png');
					$conditions['and'][] = array('UploaderFile.name NOT LIKE' => '%.jpg');
					$conditions['and'][] = array('UploaderFile.name NOT LIKE' => '%.gif');
					break;
				case 'all':
				case '':
			}
		}
		if(!empty($data['name'])) {
			$conditions['and']['or'][] = array('UploaderFile.name LIKE' => '%' . $data['name'] . '%');
			$conditions['and']['or'][] = array('UploaderFile.alt LIKE' => '%' . $data['name'] . '%');
		}
		
		return $conditions;
		
	}
/**
 * [ADMIN] Ajaxファイルアップロード
 *
 * jQueryのAjaxによるファイルアップロードの際、
 * RequestHandlerコンポーネントが作動しないので明示的に
 * レイアウト、デバッグフラグの設定をする
 *
 * @return 成功時：true　／　失敗時：null
 */
	public function admin_ajax_upload() {

		$this->layout = 'ajax';
		Configure::write('debug',0);

		if(!$this->request->data) {
			$this->ajaxError(500, '無効な処理です。');
		}
		
		// 2014.08.10 yuse fixed 4777 php.iniに定義されたサイズチェックエラーの場合はエラー(UPLOAD_ERR_INI_SIZE)
		if($this->request->data['UploaderFile']['file']['error'] == 1){
			echo null;
			die;
		}

		$user = $this->BcAuth->user();
		if(!empty($user['id'])) {
			$this->request->data['UploaderFile']['user_id'] = $user['id'];
		}
		$this->request->data['UploaderFile']['name'] = $this->request->data['UploaderFile']['file'];
		$this->request->data['UploaderFile']['alt'] = $this->request->data['UploaderFile']['name']['name'];
		$this->UploaderFile->create($this->request->data);

		if($this->UploaderFile->save()) {
			echo true;
		}
		
		exit();

	}
/**
 * [ADMIN] サイズを指定して画像タグを取得する
 *
 * @param	string	$name
 * @param	string	$size
 * @return	void
 * @access	public
 */
	public function admin_ajax_image($name, $size='small') {
		
		$file = $this->UploaderFile->findByName(urldecode($name));
		$this->set('file', $file);
		$this->set('size', $size);

	}
/**
 * [ADMIN] 各サイズごとの画像の存在チェックを行う
 *
 * @param	string	$name
 * @return	void
 * @access	public
 */
	public function admin_ajax_exists_images($name) {

		Configure::write('debug', 0);
		$this->RequestHandler->setContent('json');
		$this->RequestHandler->respondAs('application/json; charset=UTF-8');
		$files = $this->UploaderFile->filesExists($name);
		$this->set('result',$files);
		$this->render('json_result');
		
	}
/**
 * [ADMIN] 編集処理
 *
 * @return	mixed
 */
	public function admin_edit($id = null) {
		$this->autoRender = false;
		if (!$this->request->data && $this->request->is('ajax')) {
			$this->ajaxError(500, '無効な処理です。');
		} elseif(!$this->request->is('ajax') && !$id) {
			$this->notFound();
		}
		
		$user = $this->BcAuth->user();
		$uploaderConfig = $this->UploaderConfig->findExpanded();
		if($uploaderConfig['use_permission']) {
			if($user['user_group_id'] != 1 && $this->request->data['UploaderFile']['user_id'] != $user['id']) {
				$this->notFound();
			}
		}
		
		if(!$this->request->data) {
			$this->request->data = $this->UploaderFile->read(null, $id);
		} else {
			$this->UploaderFile->set($this->request->data);
			$result = $this->UploaderFile->save();
			if ($this->request->is('ajax')) {
				if($result) {
					return true;
				} else {
					return false;
				}
			} else {
				if($result) {
					$this->setMessage('ファイルの内容を保存しました。');
					$this->redirect(array('action' => 'index'));
				} else {
					$this->setMessage('保存中にエラーが発生しました。');
				}
			}	
		}

		$this->render('../Elements/admin/uploader_files/form');

	}
/**
 * [ADMIN] 削除処理
 *
 * @return	void
 * @access	public
 */
	public function admin_delete($id) {
		$this->_checkSubmitToken();
		if(!$id) {
			$this->notFound();
		}

		$user = $this->BcAuth->user();
		$uploaderConfig = $this->UploaderConfig->findExpanded();
		$uploaderFile = $this->UploaderFile->read(null, $id);

		if(!$uploaderFile) {
			$this->notFound();
		}

		if($uploaderConfig['use_permission']) {
			if($user['user_group_id'] != 1 && $uploaderFile['UploaderFile']['user_id'] != $user['id']) {
				$this->notFound();
			}
		}

		$result = $this->UploaderFile->delete($id);
		if ($this->RequestHandler->isAjax()) {
			echo $result;
			exit();
		} else {
			if($result) {
				$this->setMessage($uploaderFile['UploaderFile']['name']. ' を削除しました。', false, true);
			} else {
				$this->setMessage('削除中にエラーが発生しました。', true);
			}
			$this->redirect(array('action' => 'index'));
		}

	}
/**
 * 検索ボックスを取得する
 * 
 * @param string $listid
 */
	public function admin_ajax_get_search_box($listId = "") {
		
		$this->set('listId', $listId);
		$this->render('../Elements/admin/searches/uploader_files_index');

	}
	
/**
 * 公開期間のチェックを行う
 * 
 */
	public function view_limited_file($filename) {
		
		$display = false;
		if(!empty($_SESSION['Auth'][Configure::read('BcAuthPrefix.admin.sessionKey')])) {
			$display = true;
		} else {
			$conditions = array(
				'UploaderFile.name' => $this->UploaderFile->getSourceFileName($filename),
				array('or'=> array(array('UploaderFile.publish_begin <=' => date('Y-m-d H:i:s')),
									array('UploaderFile.publish_begin' => NULL),
									array('UploaderFile.publish_begin' => '0000-00-00 00:00:00'))),
				array('or'=> array(array('UploaderFile.publish_end >=' => date('Y-m-d H:i:s')),
									array('UploaderFile.publish_end' => NULL),
									array('UploaderFile.publish_end' => '0000-00-00 00:00:00')))
			);
			$data = $this->UploaderFile->find('first', array('conditions' => $conditions));
			if($data) {
				$display = true;
			}
		}
		
		if($display) {
			$info = pathinfo($filename);
			$ext = $info['extension'];
			$contentsMaping=array(
					"gif"	=> "image/gif",
					"jpg"	=> "image/jpeg",
					"jpeg"	=> "image/jpeg",
					"png"	=> "image/png",
					"swf"	=> "application/x-shockwave-flash",
					"pdf"	=> "application/pdf",
					"sig"	=> "application/pgp-signature",
					"spl"	=> "application/futuresplash",
					"doc"	=> "application/msword",
					"ai"	=> "application/postscript",
					"torrent"=> "application/x-bittorrent",
					"dvi"	=> "application/x-dvi",
					"gz"	=> "application/x-gzip",
					"pac"	=> "application/x-ns-proxy-autoconfig",
					"tar.gz"=> "application/x-tgz",
					"tar"	=> "application/x-tar",
					"zip"	=> "application/zip",
					"mp3"	=> "audio/mpeg",
					"m3u"	=> "audio/x-mpegurl",
					"wma"	=> "audio/x-ms-wma",
					"wax"	=> "audio/x-ms-wax",
					"wav"	=> "audio/x-wav",
					"xbm"	=> "image/x-xbitmap",
					"xpm"	=> "image/x-xpixmap",
					"xwd"	=> "image/x-xwindowdump",
					"css"	=> "text/css",
					"html"	=> "text/html",
					"js"	=> "text/javascript",
					"txt"	=> "text/plain",
					"xml"	=> "text/xml",
					"mpeg"	=> "video/mpeg",
					"mov"	=> "video/quicktime",
					"avi"	=> "video/x-msvideo",
					"asf"	=> "video/x-ms-asf",
					"wmv"	=> "video/x-ms-wmv"
			);
			header("Content-type: " . $contentsMaping[$ext]);
			readfile(WWW_ROOT . 'files' . DS . 'uploads' . DS . 'limited' . DS . $filename);
			exit();
		} else {
			$this->notFound();
		}
		
	}
	
}

