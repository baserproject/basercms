<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * ページモデル
 * 
 * @package Baser.Model
 * @property Content $Content
 */
class Page extends AppModel {

/**
 * クラス名
 * @var string
 */
	public $name = 'Page';

/**
 * データベース接続
 * 
 * @var string
 */
	public $useDbConfig = 'baser';

/**
 * belongsTo
 * 
 * @var array
 */
	public $belongsTo = array(
		'PageCategory' => array('className' => 'PageCategory',
			'foreignKey' => 'page_category_id'),
		'User' => array('className' => 'User',
			'foreignKey' => 'author_id'));

/**
 * ビヘイビア
 *
 * @var array
 */
	public $actsAs = array('BcSearchIndexManager', 'BcCache', 'BcContents');

/**
 * 更新前のページファイルのパス
 * 
 * @var string
 */
	public $oldPath = '';

/**
 * ファイル保存可否
 * true の場合、ページデータ保存の際、ページテンプレートファイルにも内容を保存する
 * テンプレート読み込み時などはfalseにして保存しないようにする
 * 
 * @var boolean
 */
	public $fileSave = true;

/**
 * 検索テーブルへの保存可否
 *
 * @var boolean
 */
	public $searchIndexSaving = true;

/**
 * 公開WebページURLリスト
 * キャッシュ用
 * 
 * @var mixed
 */
	protected $_publishes = -1;

/**
 * WebページURLリスト
 * キャッシュ用
 * 
 * @var mixed
 */
	protected $_pages = -1;

/**
 * 最終登録ID
 * モバイルページへのコピー処理でスーパークラスの最終登録IDが上書きされ、
 * コントローラーからは正常なIDが取得できないのでモバイルページへのコピー以外の場合だけ保存する
 *
 * @var int
 */
	private $__pageInsertID = null;

/**
 * バリデーション
 *
 * @var array
 */
	public $validate = array(
		'contents' => array(
			array('rule' => array('phpValidSyntax'),
				'message' => 'PHPの構文エラーが発生しました。'),
			array('rule' => array('maxByte', 64000),
				'message' => '本稿欄に保存できるデータ量を超えています。')
		),
		'draft' => array(
			array('rule' => array('phpValidSyntax'),
				'message' => 'PHPの構文エラーが発生しました。'),
			array('rule' => array('maxByte', 64000),
				'message' => '草稿欄に保存できるデータ量を超えています。')
		),
	);

/**
 * コンストラクタ
 *
 * @param boolean $id
 * @param string $table
 * @param string $ds
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		if (isConsole() && !isInstalled()) {
			App::uses('PageCategory', 'Model');
			$this->PageCategory = new PageCategory(null, null, $ds);
		}
	}

/**
 * フォームの初期値を設定する
 * 
 * @return	array	初期値データ
 */
	public function getDefaultValue() {
		$key = Configure::read('BcAuthPrefix.admin.sessionKey');
		if (!empty($_SESSION['Auth'][$key])) {
			$data[$this->name]['author_id'] = $_SESSION['Auth'][$key]['id'];
		}
		$data[$this->name]['sort'] = $this->getMax('sort') + 1;
		$data[$this->name]['status'] = false;
		return $data;
	}

/**
 * beforeSave
 *
 * @param array $options
 * @return boolean
 */
	public function beforeSave($options = array()) {
		if (!$this->fileSave) {
			return true;
		}

		// 保存前のページファイルのパスを取得
		if ($this->exists() && !empty($this->data['Content'])) {
			$this->oldPath = $this->getPageFilePath(
				$this->find('first', array(
					'conditions' => array('Page.id' => $this->data['Page']['id']),
					'recursive' => 0)
				)
			);
		} else {
			$this->oldPath = '';
		}
		
		// 新しいページファイルのパスが開けるかチェックする
		$result = true;
		if(!empty($this->data['Content'])) {
			if (!$this->checkOpenPageFile($this->data)) {
				$result = false;
			}
		}
		return $result;
	}

/**
 * URLよりモバイルやスマートフォン等のプレフィックスを取り除く
 * 
 * @param string $url 変換対象のURL
 * @return string $url 変換後のURL
 */
	public function removeAgentPrefixFromUrl($url) {
		if (preg_match('/^\/' . Configure::read('BcAgent.mobile.prefix') . '\//', $url)) {
			$url = preg_replace('/^\/' . Configure::read('BcAgent.mobile.prefix') . '\//', '/', $url);
		} elseif (preg_match('/^\/' . Configure::read('BcAgent.smartphone.prefix') . '\//', $url)) {
			$url = preg_replace('/^\/' . Configure::read('BcAgent.smartphone.prefix') . '\//', '/', $url);
		}
		return $url;
	}

/**
 * 最終登録IDを取得する
 *
 * @return	int
 */
	public function getInsertID() {
		if (!$this->__pageInsertID) {
			$this->__pageInsertID = parent::getInsertID();
		}
		return $this->__pageInsertID;
	}

/**
 * ページテンプレートファイルが開けるかチェックする
 * 
 * @param	array	$data	ページデータ
 * @return	boolean
 */
	public function checkOpenPageFile($data) {
		$path = $this->getPageFilePath($data);
		$File = new File($path);
		if ($File->open('w')) {
			$File->close();
			$File = null;
			return true;
		} else {
			return false;
		}
	}

/**
 * afterSave
 * 
 * @param boolean $created
 * @param array $options
 * @return boolean
 */
	public function afterSave($created, $options = array()) {

		$data = $this->data;
		// タイトルタグと説明文を追加
		if (empty($data['Page']['id'])) {
			$data['Page']['id'] = $this->id;
		}

		if ($this->fileSave) {
			$this->createPageTemplate($data);
		}

		// 検索用テーブルに登録
		if ($this->searchIndexSaving) {
			if (empty($data['Content']['exclude_search'])) {
				$this->saveSearchIndex($this->createSearchIndex($data));
			} else {
				$this->deleteSearchIndex($data['Page']['id']);
			}
		}

	}

/**
 * 検索用データを生成する
 *
 * @param array $data
 * @return array
 */
	public function createSearchIndex($data) {
		if (!isset($data['Page']) || !isset($data['Content'])) {
			return false;
		}
		$page = $data['Page'];
		$content = $data['Content'];
		if (!isset($content['publish_begin'])) {
			$content['publish_begin'] = '';
		}
		if (!isset($content['publish_end'])) {
			$content['publish_end'] = '';
		}

		if (!$content['title']) {
			$content['title'] = Inflector::camelize($content['name']);
		}

		// モバイル未対応の為除外
		// インストール時取得できないのでハードコーディング
		// TODO 検討
//		$excludeIds = array_merge($this->PageCategory->getAgentCategoryIds('mobile'), $this->PageCategory->getAgentCategoryIds('smartphone'));
//		if (!$excludeIds) {
//			$excludeIds = array(1, 2);
//		}
//		if (in_array($content['site_id'], $excludeIds)) {
//			return array();
//		}
		
		// $this->idに値が入ってない場合もあるので
		if (!empty($page['id'])) {
			$mobileId = $page['id'];
		} else {
			$mobileId = $this->id;
		}
		$category = '';
		if (!empty($page['page_category_id'])) {
			$categoryPath = $this->PageCategory->getPath($page['page_category_id'], array('title'));
			if ($categoryPath) {
				$category = $categoryPath[0]['PageCategory']['title'];
			}
		}
		$parameters = explode('/', preg_replace("/^\//", '', $content['url']));
		$detail = $this->requestAction(array('admin' => false, 'plugin' => false, 'controller' => 'pages', 'action' => 'display'), array('pass' => $parameters, 'return'));
		$detail = preg_replace('/<!-- BaserPageTagBegin -->.*?<!-- BaserPageTagEnd -->/is', '', $detail);
		$description = '';
		if(!empty($content['description'])) {
			$description = $content['description'];
		}
		$searchIndex = ['SearchIndex' => [
			'model_id'	=> $page['id'],
			'type'		=> 'ページ',
			'mobile_id' => $mobileId,
			'category'	=> $category,
			'title'		=> $content['title'],
			'detail'	=> $description . ' ' . $detail,
			'url'		=> $content['url'],
			'status'	=> $this->isPublish($content['status'], $content['publish_begin'], $content['publish_end'])
		]];
		return $searchIndex;
	}

/**
 * beforeDelete
 *
 * @param $cascade
 * @return boolean
 */
	public function beforeDelete($cascade = true) {
		return $this->deleteSearchIndex($this->id);
	}

/**
 * DBデータを元にページテンプレートを全て生成する
 * 
 * @return boolean
 */
	public function createAllPageTemplate() {
		set_time_limit(0);
		if (function_exists('ini_set')) {
			ini_set('memory_limit ', '-1');
		}

		$pages = $this->find('all', array('recursive' => -1));
		$result = true;
		foreach ($pages as $page) {
			if (!$this->createPageTemplate($page)) {
				$result = false;
			}
		}
		return $result;
	}

/**
 * ページテンプレートを生成する
 * 
 * @param array $data ページデータ
 * @return boolean
 */
	public function createPageTemplate($data) {
		set_time_limit(0);
		if (function_exists('ini_set')) {
			ini_set('memory_limit ', '-1');
		}
		
		$data['Page'] = array_merge(['id' => '', 'contents' => '', 'title' => '', 'description' => '', 'code' => ''], $data['Page']);
		if (isset($data['Page'])) {
			$page = $data['Page'];
		}
		$contents = $this->addBaserPageTag($page['id'], $page['contents'], $page['title'], $page['description'], $page['code']);

		// 新しいページファイルのパスを取得する
		$newPath = $this->getPageFilePath($data);

		// テーマやファイル名が変更された場合は元ファイルを削除する
		if ($this->oldPath && ($newPath != $this->oldPath)) {
			$oldFile = new File($this->oldPath);
			$oldFile->delete();
			unset($oldFile);
		}

		// ファイルに保存
		$newFile = new File($newPath, true);
		if ($newFile->open('w')) {
			$newFile->append($contents);
			$newFile->close();
			unset($newFile);
			@chmod($newPath, 0666);
			return true;
		} else {
			return false;
		}
	}

/**
 * ページファイルのディレクトリを取得する
 * 
 * @param array $data
 * @return string
 */
	protected function getPageFilePath($data) {
		
		$SiteConfig = ClassRegistry::init('SiteConfig');
		$SiteConfig->cacheQueries = false;
		$siteConfig = $SiteConfig->findExpanded();
		$theme = $siteConfig['theme'];

		// Pagesディレクトリのパスを取得
		if ($theme) {
			$path = BASER_THEMES . $theme . DS . 'Pages' . DS;
		} else {
			$path = APP . 'View' . DS . 'Pages' . DS;
		}
		
		if (!is_dir($path)) {
			mkdir($path);
			chmod($path, 0777);
		}
		
		$url = $this->Content->createUrl($data['Content']['parent_id'], 'ContentFolder');
		if($url != '/') {
			$urlAry = explode('/', preg_replace('/(^\/|\/$)/', '', $url));
			if($data['Content']['site_id'] !== 0) {
				$urlAry[0] = $this->Content->Site->field('name', ['Site.id' => $data['Content']['site_id']]);
			}
			foreach ($urlAry as $value) {
				$path .= $value . DS;
				if (!is_dir($path)) {
					mkdir($path, 0777);
					chmod($path, 0777);
				}
			}
		}
		return $path . $data['Content']['name'] . Configure::read('BcApp.templateExt');
	}

/**
 * ページファイルを削除する
 * 
 * @param array $data 削除対象となるレコードデータ
 * @return boolean
 */
	public function delFile($data) {
		$path = $this->getPageFilePath($data);
		if ($path) {
			return unlink($path);
		}
		return true;
	}
	
/**
 * 固定ページのURLを表示用のURLに変換する
 * 
 * 《変換例》
 * /mobile/index → /m/
 * 
 * @param string $url 変換対象のURL
 * @return string 表示の用のURL
 */
	public function convertViewUrl($url) {
		$url = preg_replace('/\/index$/', '/', $url);
		if (preg_match('/^\/' . Configure::read('BcAgent.mobile.prefix') . '\//is', $url)) {
			$url = preg_replace('/^\/' . Configure::read('BcAgent.mobile.prefix') . '\//is', '/' . Configure::read('BcAgent.mobile.alias') . '/', $url);
		} elseif (preg_match('/^\/' . Configure::read('BcAgent.smartphone.prefix') . '\//is', $url)) {
			$url = preg_replace('/^\/' . Configure::read('BcAgent.smartphone.prefix') . '\//is', '/' . Configure::read('BcAgent.smartphone.alias') . '/', $url);
		}
		return $url;
	}

/**
 * 本文にbaserが管理するタグを追加する
 * 
 * @param string $id ID
 * @param string $contents 本文
 * @param string $title タイトル
 * @param string $description 説明文
 * @param string $code コード
 * @return string 本文の先頭にbaserCMSが管理するタグを付加したデータ
 */
	public function addBaserPageTag($id, $contents, $title, $description, $code) {
		$tag = array();
		$tag[] = '<!-- BaserPageTagBegin -->';
		$title = str_replace("'", "\'", str_replace("\\", "\\\\'", $title));
		$description = str_replace("'", "\'", str_replace("\\", "\\\\'", $description));
		$tag[] = '<?php $this->BcBaser->setTitle(\'' . $title . '\') ?>';
		$tag[] = '<?php $this->BcBaser->setDescription(\'' . $description . '\') ?>';

		if ($id) {
			$tag[] = '<?php $this->BcBaser->setPageEditLink(' . $id . ') ?>';
		}
		if ($code) {
			$tag[] = trim($code);
		}
		$tag[] = '<!-- BaserPageTagEnd -->';
		return implode("\n", $tag) . "\n\n" . $contents;
	}

/**
 * コントロールソースを取得する
 *
 * @param string $field フィールド名
 * @param array $options
 * @return mixed $controlSource コントロールソース
 */
	public function getControlSource($field, $options = array()) {
		switch ($field) {

			case 'page_category_id':

				$catOption = array();
				$isSuperAdmin = false;
				$agentRoot = true;

				extract($options);

				if (!empty($userGroupId)) {

					if (!isset($pageCategoryId)) {
						$pageCategoryId = '';
					}

					if ($userGroupId == 1) {
						$isSuperAdmin = true;
					}

					// 現在のページが編集不可の場合、現在表示しているカテゴリも取得する
					if (!$pageEditable && $pageCategoryId) {
						$catOption = array('conditions' => array('OR' => array('PageCategory.id' => $pageCategoryId)));
					}

					// super admin でない場合は、管理許可のあるカテゴリのみ取得
					if (!$isSuperAdmin) {
						$catOption['ownerId'] = $userGroupId;
					}

					if ($pageEditable && !$rootEditable && !$isSuperAdmin) {
						unset($empty);
						$agentRoot = false;
					}
				}

				$options = am($options, $catOption);
				$categories = $this->PageCategory->getControlSource('parent_id', $options);

				// 「指定しない」追加
				if (isset($empty)) {
					if ($categories) {
						$categories = array('' => $empty) + $categories;
					} else {
						$categories = array('' => $empty);
					}
				}
				if (!$agentRoot) {
					// TODO 整理
					$agentId = $this->PageCategory->getAgentId('mobile');
					if (isset($categories[$agentId])) {
						unset($categories[$agentId]);
					}
					$agentId = $this->PageCategory->getAgentId('smartphone');
					if (isset($categories[$agentId])) {
						unset($categories[$agentId]);
					}
				}

				$controlSources['page_category_id'] = $categories;

				break;

			case 'user_id':
			case 'author_id':
				$controlSources[$field] = $this->User->getUserList($options);
				break;
		}

		if (isset($controlSources[$field])) {
			return $controlSources[$field];
		} else {
			return false;
		}
	}

/**
 * キャッシュ時間を取得する
 * 
 * @param string $url
 * @return mixed int or false
 */
	public function getCacheTime($url) {
		if (preg_match('/\/$/', $url)) {
			$url .= 'index';
		}

		$url = preg_replace('/^\/' . Configure::read('BcRequest.agentAlias') . '\//', '/' . Configure::read('BcRequest.agentPrefix') . '/', $url);
		$page = $this->find('first', array('conditions' => array('Page.url' => $url), 'recursive' => -1));
		if (!$page) {
			return false;
		}
		if ($page['Page']['status'] && $page['Page']['publish_end'] && $page['Page']['publish_end'] != '0000-00-00 00:00:00') {
			return strtotime($page['Page']['publish_end']) - time();
		} else {
			// #10680 Modify 2016/01/22 gondoh
			// 3.1.0で追加されたViewキャッシュ分離の設定値を、後方互換のため存在しない場合は旧情報で取り込む 
			$duration = Configure::read('BcCache.viewDuration');
			if (empty($duration)) $duration = Configure::read('BcCache.duration');
			return $duration;
		}
	}

/**
 * 公開チェックを行う
 * 
 * @param string $url
 * @return boolean
 */
	public function checkPublish($url) {
		if (preg_match('/\/$/', $url)) {
			$url .= 'index';
		}
		$url = preg_replace('/^\/' . Configure::read('BcRequest.agentAlias') . '\//', '/' . Configure::read('BcRequest.agentPrefix') . '/', $url);

		if ($this->_publishes == -1) {
			$conditions = $this->getConditionAllowPublish();
			// 毎秒抽出条件が違うのでキャッシュしない
			$pages = $this->find('all', array(
				'fields' => 'url',
				'conditions' => $conditions,
				'recursive' => -1,
				'cache' => false
			));
			if (!$pages) {
				$this->_publishes = array();
				return false;
			}
			$this->_publishes = Hash::extract($pages, '{n}.Page.url');
		}
		return in_array($url, $this->_publishes);
	}

/**
 * 公開済を取得するための conditions を取得
 *
 * @return array
 */
	public function getConditionAllowPublish() {
		$conditions[$this->alias . '.status'] = true;
		$conditions[] = array('or' => array(array($this->alias . '.publish_begin <=' => date('Y-m-d H:i:s')),
				array($this->alias . '.publish_begin' => null),
				array($this->alias . '.publish_begin' => '0000-00-00 00:00:00')));
		$conditions[] = array('or' => array(array($this->alias . '.publish_end >=' => date('Y-m-d H:i:s')),
				array($this->alias . '.publish_end' => null),
				array($this->alias . '.publish_end' => '0000-00-00 00:00:00')));
		return $conditions;
	}

/**
 * ページファイルを登録する
 * ※ 再帰処理
 *
 * @param string $targetPath
 * @param string $parentCategoryId
 * @return array 処理結果 all / success
 */
	public function entryPageFiles($targetPath, $parentCategoryId = '') {
		if ($this->Behaviors->loaded('BcCache')) {
			$this->Behaviors->unload('BcCache');
		}
		if ($this->PageCategory->Behaviors->loaded('BcCache')) {
			$this->PageCategory->Behaviors->unload('BcCache');
		}

		$this->fileSave = false;
		$Folder = new Folder($targetPath);
		$files = $Folder->read(true, true, true);
		$Folder = null;
		$insert = 0;
		$update = 0;
		$all = 0;

		// カテゴリの取得・登録
		$categoryName = basename($targetPath);

		$specialCategoryIds = array(
			'',
			$this->PageCategory->getAgentId('mobile'),
			$this->PageCategory->getAgentId('smartphone')
		);

		if (in_array($parentCategoryId, $specialCategoryIds) && $categoryName == 'templates') {
			return array('all' => 0, 'insert' => 0, 'update' => 0);
		}

		$pageCategoryId = '';
		$this->PageCategory->updateRelatedPage = false;
		if ($categoryName != 'Pages') {

			// カテゴリ名の取得
			// 標準では設定されてないので、利用する場合は、あらかじめ bootstrap 等で宣言しておく
			$categoryTitles = Configure::read('Baser.pageCategoryTitles');
			$categoryTitle = -1;
			if ($categoryTitles) {
				$categoryNames = explode('/', str_replace(getViewPath() . 'Pages' . DS, '', $targetPath));
				foreach ($categoryNames as $key => $value) {
					if (isset($categoryTitles[$value])) {
						if (count($categoryNames) == ($key + 1)) {
							$categoryTitle = $categoryTitles[$value]['title'];
						} elseif (isset($categoryTitles[$value]['children'])) {
							$categoryTitles = $categoryTitles[$value]['children'];
						}
					}
				}
			}

			$categoryId = $this->PageCategory->getIdByPath($targetPath);
			if ($categoryId) {
				$pageCategoryId = $categoryId;
				if ($categoryTitle != -1) {
					$pageCategory = $this->PageCategory->find('first', array('conditions' => array('PageCategory.id' => $pageCategoryId), 'recursive' => -1));
					$pageCategory['PageCategory']['title'] = $categoryTitle;
					$this->PageCategory->set($pageCategory);
					$this->PageCategory->save();
				}
			} else {
				$pageCategory['PageCategory']['parent_id'] = $parentCategoryId;
				$pageCategory['PageCategory']['name'] = $categoryName;
				if ($categoryTitle == -1) {
					$pageCategory['PageCategory']['title'] = $categoryName;
				} else {
					$pageCategory['PageCategory']['title'] = $categoryTitle;
				}
				$pageCategory['PageCategory']['sort'] = $this->PageCategory->getMax('sort') + 1;
				$this->PageCategory->cacheQueries = false;
				$this->PageCategory->create($pageCategory);
				if ($this->PageCategory->save()) {
					$pageCategoryId = $this->PageCategory->getInsertID();
				}
			}
		} else {
			$categoryName = '';
		}

		// ファイル読み込み・ページ登録
		if (!$files[1]) {
			$files[1] = array();
		}
		foreach ($files[1] as $path) {

			if (preg_match('/' . preg_quote(Configure::read('BcApp.templateExt')) . '$/is', $path) == false) {
				continue;
			}

			$pageName = basename($path, Configure::read('BcApp.templateExt'));
			$file = new File($path);
			$contents = $file->read();
			$file->close();
			$file = null;

			// タイトル取得・置換
			$titleReg = '/<\?php\s+?\$this->BcBaser->setTitle\(\'(.*?)\'\)\s+?\?>/is';
			if (preg_match($titleReg, $contents, $matches)) {
				$title = trim($matches[1]);
				$contents = preg_replace($titleReg, '', $contents);
			} else {
				$title = Inflector::camelize($pageName);
			}

			// 説明文取得・置換
			$descriptionReg = '/<\?php\s+?\$this->BcBaser->setDescription\(\'(.*?)\'\)\s+?\?>/is';
			if (preg_match($descriptionReg, $contents, $matches)) {
				$description = trim($matches[1]);
				$contents = preg_replace($descriptionReg, '', $contents);
			} else {
				$description = '';
			}

			// PageTagコメントの削除
			$pageTagReg = '/<\!\-\- BaserPageTagBegin \-\->.*?<\!\-\- BaserPageTagEnd \-\->/is';
			$contents = preg_replace($pageTagReg, '', $contents);

			$conditions['Page.name'] = $pageName;
			if ($pageCategoryId) {
				$conditions['Page.page_category_id'] = $pageCategoryId;
			} else {
				$conditions['Page.page_category_id'] = null;
			}
			$page = $this->find('first', array('conditions' => $conditions, 'recursive' => -1));
			if ($page) {
				$chage = false;
				if ($title != $page['Page']['title']) {
					$chage = true;
				}
				if ($description != $page['Page']['description']) {
					$chage = true;
				}
				if (trim($contents) != trim($page['Page']['contents'])) {
					$chage = true;
				}
				if ($chage) {
					$page['Page']['title'] = $title;
					$page['Page']['description'] = $description;
					$page['Page']['contents'] = $contents;
					$this->set($page);
					if ($this->save()) {
						$update++;
					}
				}
			} else {
				$page = $this->getDefaultValue();
				$page['Page']['contents'] = $contents;
				$this->create($page);
				if ($this->save()) {
					$insert++;
				}
			}
			$contents = $page = $pageName = $title = $description = $conditions = $descriptionReg = $titleReg = $pageTagReg = null;
			$all++;
		}

		// フォルダー内の登録
		if (!$files[0]) {
			$files[0] = array();
		}
		foreach ($files[0] as $file) {
			$folderName = basename($file);
			if ($folderName != '_notes' && $folderName != 'admin') {
				$result = $this->entryPageFiles($file, $pageCategoryId);
				$insert += $result['insert'];
				$update += $result['update'];
				$all += $result['all'];
			}
		}

		return array('all' => $all, 'insert' => $insert, 'update' => $update);
	}

/**
 * 関連ページの存在チェック
 * 存在する場合は、ページIDを返す
 *
 * @param string $type エージェントタイプ
 * @param array $data ページデータ
 * @return mixed ページID / false
 */
	public function agentExists($type, $data) {
		if (isset($data['Page'])) {
			$data = $data['Page'];
		}
		$url = $this->removeAgentPrefixFromUrl($data['url']);
		if (preg_match('/^\/' . Configure::read('BcAgent.' . $type . '.prefix') . '\//is', $url)) {
			// 対象ページがモバイルページの場合はfalseを返す
			return false;
		}
		return $this->field('id', array('Page.url' => '/' . Configure::read('BcAgent.' . $type . '.prefix') . $url));
	}

/**
 * 固定ページとして管理されているURLかチェックする
 * 
 * $this->_pages をキャッシュとして利用する
 * URL に、拡張子 .html がついている場合も存在するとみなす
 * 
 * @param string $url URL
 * @return boolean
 */
	public function isPageUrl($url) {
		if (preg_match('/\/$/', $url)) {
			$url .= 'index';
		}
		$url = preg_replace('/^\/' . Configure::read('BcRequest.agentAlias') . '\//', '/' . Configure::read('BcRequest.agentPrefix') . '/', $url);

		if ($this->_pages == -1) {
			$pages = $this->find('all', array(
				'fields'	=> 'url',
				'recursive' => -1
			));
			if (!$pages) {
				$this->_pages = array();
				return false;
			}
			$this->_pages = Hash::extract($pages, '{n}.Page.url');
		}
		if(in_array($url, $this->_pages)) {
			return true;
		}
		if(preg_match('/\.html$/', $url)) {
			$url = preg_replace('/\.html$/', '', $url);
			if(in_array($url, $this->_pages)) {
				return true;
			}
		}
		return false;
	}

/**
 * delete
 *
 * @param mixed $id ページID
 * @param boolean $cascade Set to true to delete records that depend on this record
 * @return boolean True on success
 */
	public function delete($id = null, $cascade = true) {
		// メッセージ用にデータを取得
		$page = $this->read(null, $id);

		/* 削除処理 */
		if (parent::delete($id, $cascade)) {

			// ページテンプレートを削除
			$this->delFile($page);

			// 公開状態だった場合、サイトマップのキャッシュを削除
			// 公開期間のチェックは行わず確実に削除
			if ($page['Page']['status']) {
				clearViewCache();
			}
			return true;
		} else {

			return false;
		}
	}

/**
 * ページデータをコピーする
 *
 * 固定ページテンプレートの生成処理を実行する必要がある為、
 * Content::copy() は利用しない
 * 
 * @param int $id ページID
 * @param int $newParentId 新しい親コンテンツID
 * @param string $newTitle 新しいタイトル
 * @param int $newAuthorId 新しいユーザーID
 * @param int $newSiteId 新しいサイトID
 * @return mixed page Or false
 */
	public function copy($id, $newParentId, $newTitle, $newAuthorId, $newSiteId = null) {
		$data = $this->find('first', ['conditions' => ['Page.id' => $id], 'recursive' => 0]);
		$url = $data['Content']['url'];
		$siteId = $data['Content']['site_id'];
		unset($data['Page']['id']);
		unset($data['Page']['created']);
		unset($data['Page']['modified']);
		unset($data['Content']);
		$data['Content'] = [
			'parent_id'	=> $newParentId,
			'title'		=> $newTitle,
			'author_id' => $newAuthorId,
			'site_id' 	=> $siteId
		];
		if(!is_null($newSiteId) && $siteId != $newSiteId) {
			$data['Content']['site_id'] = $newSiteId;
			$data['Content']['parent_id'] = $this->Content->copyContentFolderPath($url, $newSiteId);
		}
		$this->getDataSource()->begin();
		if ($this->save($data)) {
			$this->getDataSource()->commit();
			return true;
		}
		$this->getDataSource()->rollback();
		return false;
	}

/**
 * 連携チェック
 * 
 * @param string $agentPrefix
 * @param string $url
 * @return boolean 
 */
	public function isLinked($agentPrefix, $url) {
		if (!$agentPrefix) {
			return false;
		}

		if (!Configure::read('BcApp.' . $agentPrefix)) {
			return false;
		}

		$siteConfig = Configure::read('BcSite');
		$linked = false;

		if (isset($siteConfig['linked_pages_' . $agentPrefix])) {
			$linked = $siteConfig['linked_pages_' . $agentPrefix];
		}

		if (preg_match('/\/$/', $url)) {
			$url .= 'index';
		}

		if ($this->field('unlinked_' . $agentPrefix, array('Page.url' => $url))) {
			$linked = false;
		}

		return $linked;
	}

/**
 * treeList
 * ページカテゴリーに関連したデータを取得する
 * 
 * @param int $categoryId ページカテゴリーID
 */
	public function treeList($categoryId) {
		return $this->_treeList($categoryId);
	}

	protected function _treeList($categoryId) {
		$datas = array();
		$pages = $this->find('all', array(
			'conditions' => array('Page.page_category_id' => $categoryId),
			'recursive' => -1,
			'order' => 'sort'
		));

		$conditions = array('PageCategory.parent_id' => $categoryId);
		if (!$categoryId) {
			$conditions['PageCategory.id NOT IN'] = array(1, 2);
		}
		$pageCategories = $this->PageCategory->find('all', array(
			'conditions' => $conditions,
			'recursive' => -1,
			'order' => 'lft'
		));
		foreach ($pageCategories as $key => $pageCategory) {
			$children = $this->_treeList($pageCategory['PageCategory']['id']);
			if ($children) {
				$pageCategories[$key]['children'] = $children;
			}
		}
		$datas = array(
			'pageCategories' => $pageCategories,
			'pages' => $pages
		);
		return $datas;
	}

/**
 * PHP構文チェック
 *
 * @param array $check チェック対象文字列
 * @return bool
 */
	public function phpValidSyntax($check) {
		if(empty($check[key($check)])) {
			return true;
		}

		if(!function_exists('exec')) {
			return true;
		}

		// CL版 php がインストールされてない場合はシンタックスチェックできないので true を返す
		exec('php --version 2>&1', $output, $exit);
		if($exit !== 0) {
			return true;
		}

		if(isWindows()) {
			$tmpName = tempnam(TMP, "syntax");
			$tmp = new File($tmpName);
			$tmp->open("w");
			$tmp->write($check[key($check)]);
			$tmp->close();
			$command = sprintf("php -l %s 2>&1", escapeshellarg($tmpName));
			exec($command, $output, $exit);
			$tmp->delete();
		} else {
			$format = 'echo %s | php -l 2>&1';
			$command = sprintf($format, escapeshellarg($check[key($check)]));
			exec($command, $output, $exit);
		}

		if($exit === 0) {
			return true;
		}
		$message = 'PHPの構文エラーです： ' . PHP_EOL . implode(' ' . PHP_EOL, $output);
		return $message;
	}
}
