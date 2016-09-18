<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * コンテンツモデル
 *
 * @package Baser.Model
 * @property Site $Site
 */
class Content extends AppModel {

/**
 * ビヘイビア
 *
 * @var array
 */
	public $actsAs = array(
		'Tree' => ['level' => 'level'],
		'BcCache',
		'SoftDelete',
		'BcUpload' => array(
    		'saveDir'  => "contents",
    		'fields'  => array(
      			'eyecatch'  => array(
        			'type'		=> 'image',
        			'namefield' => 'id',
        			'nameadd'	=> true,
        			'nameformat'  => '%08d',
					'subdirDateFormat' => 'Y/m',
        			//'imageresize' => array('width' => '800', 'height' => '800'),
					'imagecopy' => array(
						'thumb' => array('suffix' => '_thumb', 'width' => '300', 'height' => '300'),
						'medium'=> array('suffix' => '_midium', 'width' => '800', 'height' => '800')
					)
      			)
    		)
  		)
	);

/**
 * Belongs To
 * 
 * @var array
 */
	public $belongsTo = [
		'Site'	=> [
			'className' => 'Site',
			'foreignKey' => 'site_id'
		],
		'User' => [
			'className' => 'User',
			'foreignKey' => 'author_id'
		]	
	];

/**
 * バリデーション
 *
 * @var array
 */
	public $validate = [
		'name' => [
			['rule' => ['bcUtileUrlencodeBlank'],
				'message' => 'URLはスペース、全角スペース及び、指定の記号(\\\'|`^"(){}[];/?:@&=+$,%<>#!)だけの名前は付けられません。'],
			['rule' => ['notBlank'],
				'message' => 'スラッグを入力してください。'],
			['rule' => ['maxLength', 2083],
				'message' => 'タイトルは230文字以内で入力してください。'],
			['rule' => ['duplicateRelatedSiteContent'],
				'message' => '連携しているサブサイトでスラッグが重複するコンテンツが存在します。重複するコンテンツのスラッグ名を先に変更してください。']
		],
		'title' => [
			['rule' => ['bcUtileUrlencodeBlank'],
				'message' => 'タイトルはスペース、全角スペース及び、指定の記号(\\\'|`^"(){}[];/?:@&=+$,%<>#!)だけの名前は付けられません。'],
			['rule' => ['notBlank'],
				'message' => 'タイトルを入力してください。'],
			['rule' => ['maxLength', 230],
				'message' => 'タイトルは230文字以内で入力してください。'],
		],
	];

/**
 * 関連データを更新する
 * 
 * @var bool
 */
	public $updatingRelated = true;
	
/**
 * 保存前の親ID
 * 
 * IDの変更比較に利用
 * 
 * @var null
 */
	public $beforeSaveParentId = null;

/**
 * Implemented Events
 *
 * beforeDelete の優先順位を SoftDeleteBehaviorより高くする為に調整
 *
 * @return array
 */
	public function implementedEvents() {
		return array(
			'Model.beforeFind' => array('callable' => 'beforeFind', 'passParams' => true),
			'Model.afterFind' => array('callable' => 'afterFind', 'passParams' => true),
			'Model.beforeValidate' => array('callable' => 'beforeValidate', 'passParams' => true),
			'Model.afterValidate' => array('callable' => 'afterValidate'),
			'Model.beforeSave' => array('callable' => 'beforeSave', 'passParams' => true),
			'Model.afterSave' => array('callable' => 'afterSave', 'passParams' => true),
			'Model.beforeDelete' => array('callable' => 'beforeDelete', 'passParams' => true, 'priority' => 1),
			'Model.afterDelete' => array('callable' => 'afterDelete'),
		);
	}

/**
 * 関連するサブサイトで、関連コンテンツを作成する際、同階層に重複名称のコンテンツがないか確認する
 *
 * 新規の際は、存在するだけでエラー
 * 編集の際は、main_site_content_id が自身のIDでない、alias_id が自身のIDでない場合エラー
 * @param $check
 * @return bool
 */
	public function duplicateRelatedSiteContent($check) {
		$name = $check[key($check)];
		if(!$this->Site->isMain($this->data['Content']['site_id'])) {
			return true;
		}
		$parents = $this->getPath($this->data['Content']['parent_id'], ['name'], -1);
		$parents = Hash::extract($parents, "{n}.Content.name");
		unset($parents[0]);
		if($this->data['Content']['site_id']) {
			unset($parents[1]);
		}
		$baseUrl = '/' . implode('/', $parents) . '/';
		$sites = $this->Site->find('all', ['conditions' => ['Site.main_site_id' => $this->data['Content']['site_id'], 'relate_main_site' => true]]);
		// URLを取得
		$urlAry = [];
		foreach($sites as $site) {
			$prefix = $site['Site']['name'];
			if($site['Site']['alias']) {
				$prefix = $site['Site']['alias'];
			}
			$urlAry[] = '/' . $prefix . $baseUrl . $name;
		}
		$conditions = ['Content.url' => $urlAry];
		if(!empty($this->data['Content']['id'])) {
			$conditions = array_merge($conditions, [
				['or' => ['Content.alias_id <>' => $this->data['Content']['id'], 'Content.alias_id' => null]],
				['or' => ['Content.main_site_content_id <>' => $this->data['Content']['id'], 'Content.main_site_content_id' => null]]
			]);
		}
		if($this->find('count', ['conditions' => $conditions])) {
			return false;
		}
		return true;
	}

/**
 * Before Validate
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 */
	public function beforeValidate($options = []) {
		// コンテンツ一覧にて、コンテンツを登録した直後のリネーム処理までは新規追加とみなして処理を行う為、$create で判定させる
		$create = false;
		if(empty($this->data['Content']['id']) || !empty($options['firstCreate'])) {
			$create = true;
		}
		// タイトルは強制的に255文字でカット
		if(!empty($this->data['Content']['title'])) {
			$this->data['Content']['title'] = mb_substr($this->data['Content']['title'], 0, 254, 'UTF-8');
		}
		if($create) {
			// IEのURL制限が2083文字の為、全て全角文字を想定し231文字でカット
			if(!isset($this->data['Content']['name'])) {
				$this->data['Content']['name'] = BcUtil::urlencode(mb_substr($this->data['Content']['title'], 0, 230, 'UTF-8'));
			}
			if(!isset($this->data['Content']['self_status'])) {
				$this->data['Content']['self_status'] = false;
			}
			if(!isset($this->data['Content']['self_publish_begin'])) {
				$this->data['Content']['self_publish_begin'] = null;
			}
			if(!isset($this->data['Content']['self_publish_end'])) {
				$this->data['Content']['self_publish_end'] = null;
			}
			if(!isset($this->data['Content']['deleted'])) {
				$this->data['Content']['deleted'] = false;
			}
			if(!isset($this->data['Content']['created_date'])) {
				$this->data['Content']['created_date'] = date('Y-m-d H:i:s');
			}
			if(!isset($this->data['Content']['site_root'])) {
				$this->data['Content']['site_root'] = 0;
			}
			if(!isset($this->data['Content']['exclude_search'])) {
				$this->data['Content']['exclude_search'] = 0;
			}
			if(!isset($this->data['Content']['author_id'])) {
				$user = BcUtil::loginUser('admin');
				$this->data['Content']['author_id'] = $user['id'];
			}
		} else {
			if(empty($this->data['Content']['modified_date'])) {
				$this->data['Content']['modified_date'] = date('Y-m-d H:i:s');
			}
			if(isset($this->data['Content']['name'])) {
				$this->data['Content']['name'] = BcUtil::urlencode(mb_substr($this->data['Content']['name'], 0, 230, 'UTF-8'));
			}
			if($this->data['Content']['id'] == 1) {
				unset($this->validate['name']);
			}
		}
		// name の 重複チェック＆リネーム
		if(!empty($this->data['Content']['name'])) {
			$contentId = null;
			if(!empty($this->data['Content']['id'])) {
				$contentId = $this->data['Content']['id'];
			}
			$this->data['Content']['name'] = $this->getUniqueName($this->data['Content']['name'], $this->data['Content']['parent_id'], $contentId);
		}
		return true;
	}

/**
 * After Validate 
 */
	public function afterValidate() {
		parent::afterValidate();
		// 新規追加の際、name は、title より自動設定される為、バリデーションエラーが発生してもエラーメッセージを表示しない
		if(empty($this->data['Content']['id']) && !empty($this->validationErrors['name'])) {
			unset($this->validationErrors['name']);
		}
	}

/**
 * 一意の name 値を取得する
 *
 * @param string $name name フィールドの値
 * @return string
 */
	public function getUniqueName($name, $parentId, $contentId = null) {

		// 先頭が同じ名前のリストを取得し、後方プレフィックス付きのフィールド名を取得する
		$conditions = array(
			'Content.name LIKE' => $name . '%',
			'Content.parent_id' => $parentId
		);
		if($contentId) {
			$conditions['Content.id <>'] = $contentId;
		}
		$datas = $this->find('all', array('conditions' => $conditions, 'fields' => array('name'), 'order' => "Content.name", 'recursive' => -1));
		$datas = Hash::extract($datas, "{n}.Content.name");
		$numbers = array();

		if ($datas) {
			foreach($datas as $data) {
				$lastPrefix = preg_replace('/^' . preg_quote($name, '/') . '/', '', $data);
				if(!$lastPrefix) {
					$numbers[1] = 1;
				} elseif (preg_match("/^_([0-9]+)$/s", $lastPrefix, $matches)) {
					$numbers[$matches[1]] = true;
				}
			}
			if($numbers) {
				$prefixNo = 1;
				while(true) {
					if(!isset($numbers[$prefixNo])) {
						break;
					}
					$prefixNo++;
				}
				if($prefixNo == 1) {
					return $name;
				} else {
					return $name . '_' . ($prefixNo);
				}
			} else {
				return $name;
			}
		} else {
			return $name;
		}

	}

/**
 * Before Save
 * 
 * @param array $options
 * @return bool
 */
	public function beforeSave($options = []) {
		if(!empty($this->data['Content']['id'])) {
			$this->beforeSaveParentId = $this->field('parent_id', ['Content.id' => $this->data['Content']['id']]);	
		}
		return parent::beforeSave($options);
	}
	
/**
 * After Save
 *
 * @param bool $created
 * @param array $options
 * @return void
 */
	public function afterSave($created, $options = array()) {
		parent::afterSave($created, $options);

		$this->updateSystemData($this->data);
		if($this->updatingRelated) {
			// ゴミ箱から戻す場合、 type の定義がないが問題なし
			if(!empty($this->data['Content']['type']) && $this->data['Content']['type'] == 'ContentFolder') {
				$this->updateChildren($this->data['Content']['id']);
			}
			$this->updateRelateSubSiteContent($this->data);
			if(!empty($this->data['Content']['parent_id']) && $this->beforeSaveParentId != $this->data['Content']['parent_id']) {
				$SiteConfig = ClassRegistry::init('SiteConfig');
				$SiteConfig->updateContentsSortLastModified();
				$this->beforeSaveParentId = null;
			}
		}
	}

/**
 * Before delete
 *
 * @param Model $model
 * @param bool $cascade
 * @return bool
 */
	public function beforeDelete($cascade = true) {
		if(!parent::beforeDelete($cascade)) {
			return false;
		}
		if(!$this->softDelete(null)) {
			return true;
		}
		$data = $this->find('first', array(
			'conditions' => array($this->alias . '.id' => $this->id)
		));
		if($data) {
			$this->deleteRelateSubSiteContent($data);
			$this->deleteAlias($data);
		}
		return true;
	}

/**
 * エイリアスを削除する
 *
 * @param $data
 */
	public function deleteAlias($data) {
		//自身がエイリアスか確認し、エイリアスの場合は終了
		if($data['Content']['alias_id']) {
			return;
		}
		$contents = $this->find('all', ['conditions' => ['Content.alias_id' => $data['Content']['id']], 'recursive' => -1]);
		if(!$contents) {
			return;
		}
		foreach($contents as $content) {
			$this->softDeleteFromTree($content['Content']['id']);
		}
		$this->data = $data;
		$this->id = $data['Content']['id'];
	}

/**
 * メインサイトの場合、連携設定がされている子サイトのエイリアス削除する
 *
 * @param $data
 */
	public function deleteRelateSubSiteContent($data) {
		// 自身がエイリアスか確認し、エイリアスの場合は終了
		if($data['Content']['alias_id']) {
			return;
		}
		// メインサイトか確認し、メインサイトでない場合は終了
		if(!$this->Site->isMain($data['Content']['site_id'])) {
			return;
		}
		// 連携設定となっている小サイトを取得
		$sites = $this->Site->find('all', ['conditions' => ['Site.main_site_id' => $data['Content']['site_id'], 'relate_main_site' => true], 'recursive' => -1]);
		if(!$sites) {
			return;
		}
		// 同階層に同名のコンテンツがあるか確認
		foreach($sites as $site) {
			$content = $this->find('first', ['conditions' => [
				'Content.site_id' => $site['Site']['id'],
				'Content.main_site_content_id' => $data['Content']['id']
			], 'recursive' => -1]);
			if($content) {
				// 存在する場合は、自身のエイリアスかどうか確認し削除する
				if($content['Content']['alias_id'] == $data['Content']['id']) {
					$this->softDelete(false);
					$this->removeFromTree($content['Content']['id'], true);
					$this->softDelete(true);
				} elseif($content['Content']['type'] == 'ContentFolder') {
					$this->updateChildren($content['Content']['type']);
				}
			}
		}
		$this->data = $data;
		$this->id = $data['Content']['id'];
	}

/**
 * メインサイトの場合、連携設定がされている子サイトのエイリアスを追加・更新する
 *
 * @param $data
 */
	public function updateRelateSubSiteContent($data) {
		// 他のデータを更新する為、一旦退避
		$dataTmp = $this->data;
		$idTmp = $this->id;
		// 自身がエイリアスか確認し、エイリアスの場合は終了
		if(!empty($data['Content']['alias_id']) || !isset($data['Content']['site_id'])) {
			return true;
		}
		
		$isContentFolder = false;
		if(!empty($data['Content']['type']) && $data['Content']['type'] == 'ContentFolder') {
			$isContentFolder = true;
		}
		
		// メインサイトか確認し、メインサイトでない場合は終了
		if(!$this->Site->isMain($data['Content']['site_id'])) {
			return true;
		}
		// 連携設定となっている小サイトを取得
		$sites = $this->Site->find('all', ['conditions' => ['Site.main_site_id' => $data['Content']['site_id'], 'relate_main_site' => true]]);
		if(!$sites) {
			return true;
		}

		$_data = $this->find('first', ['conditions' => ['Content.id' => $data['Content']['id']], 'recursive' => -1]);
		if($_data) {
			$data = ['Content' => array_merge($_data['Content'], $data['Content'])];
		}

		$CreateModel = $this;
		if($isContentFolder) {
			$CreateModel = ClassRegistry::init('ContentFolder');
		}

		$pureUrl = $this->pureUrl($data['Content']['url'], $data['Content']['site_id']);
		// 同階層に同名のコンテンツがあるか確認
		$result = true;
		foreach($sites as $site) {
			$url = $pureUrl;
			$prefix = $this->Site->getPrefix($site);
			if($prefix) {
				$url = '/' . $prefix . $url;
			}
			$content = $this->find('first', ['conditions' => [
				'Content.site_id' => $site['Site']['id'],
				'or' => [
					'Content.main_site_content_id' => $data['Content']['id'],
					'Content.url' => $url
				]
			], 'recursive' => -1]);
			if($content) {
				// 存在する場合は、自身のエイリアスかどうか確認し、エイリアスの場合は、公開状態とタイトル、説明文、アイキャッチ、更新日を更新
				// フォルダの場合も更新する
				if($content['Content']['alias_id'] == $data['Content']['id'] || ($content['Content']['type'] == 'ContentFolder' && $isContentFolder)) {
					$content['Content']['name'] = urldecode($data['Content']['name']);
					$content['Content']['title'] = $data['Content']['title'];
					$content['Content']['description'] = $data['Content']['description'];
					$content['Content']['self_status'] = $data['Content']['self_status'];
					$content['Content']['self_publish_begin'] = $data['Content']['self_publish_begin'];
					$content['Content']['self_publish_end'] = $data['Content']['self_publish_end'];
					$content['Content']['created_date'] = $data['Content']['created_date'];
					$content['Content']['modified_date'] = $data['Content']['modified_date'];
					$content['Content']['exclude_search'] = $data['Content']['exclude_search'];
					if(!empty($data['Content']['eyecatch'])) {
						$content['Content']['eyecatch'] = $data['Content']['eyecatch'];
					}
					$url = $data['Content']['url'];
					if($content['Content']['type'] == 'ContentFolder') {
						$url = preg_replace('/\/[^\/]+\/$/', '/', $url);
					}
					$content['Content']['parent_id'] = $this->copyContentFolderPath($url, $site['Site']['id']);
				} else {
					$content['Content']['name'] = urldecode($data['Content']['name']);
				}
				if(!$this->save($content)) {
					$result = false;
				}
			} else {
				// 存在しない場合はエイリアスを作成
				// フォルダの場合は実体として作成
				$content = $data;
				unset($content['Content']['id']);
				unset($content['Content']['name']);
				unset($content['Content']['url']);
				unset($content['Content']['lft']);
				unset($content['Content']['rght']);
				unset($content['Content']['created_date']);
				unset($content['Content']['modified_date']);
				unset($content['Content']['created']);
				unset($content['Content']['modified']);
				unset($content['Content']['layout_template']);
				$content['Content']['name'] = $data['Content']['name'];
				$content['Content']['main_site_content_id'] = $data['Content']['id'];
				$content['Content']['site_id'] = $site['Site']['id'];
				$url = $data['Content']['url'];
				if($content['Content']['type'] == 'ContentFolder') {
					$url = preg_replace('/\/[^\/]+\/$/', '/', $url);
					unset($content['Content']['entity_id']);
				} else {
					$content['Content']['alias_id'] = $data['Content']['id'];
				}
				$content['Content']['parent_id'] = $this->copyContentFolderPath($url, $site['Site']['id']);
				$CreateModel->create($content);
				if(!$CreateModel->save()) {
					$result = false;
				}
			}
		}
		// 退避したデータを戻す
		$this->data = $dataTmp;
		$this->id = $idTmp;
		return $result;
	}

/**
 * サブサイトのプレフィックスがついていない純粋なURLを取得
 *
 * @param string $url
 * @param int $siteId
 * @return mixed
 */
	public function pureUrl($url, $siteId) {
		$prefix = $this->Site->getPrefix($siteId);
		if($prefix) {
			$url = preg_replace('/^\/' . preg_quote($prefix, '/') . '\//', '/', $url);
		}
		return $url;
	}

/**
 * Content data を作成して保存する
 *
 * @param array $content
 * @param string $plugin
 * @param string $type
 * @param int $entityId
 */
	public function createContent($content, $plugin, $type, $entityId = null, $validate = true) {
		if(isset($content['Content'])) {
			$content = $content['Content'];
		}
		$content['plugin'] = $plugin;
		$content['type'] = $type;
		$content['entity_id'] = $entityId;
		if(!isset($content['deleted'])) {
			$content['deleted'] = false;	
		}
		if(!isset($content['site_root'])) {
			$content['site_root'] = 0;
		}
		if(!isset($content['exclude_search'])) {
			$content['exclude_search'] = 0;
		}
		if(!isset($content['created_date'])) {
			$content['created_date'] = date('Y-m-d H:i:s');
		}
		$this->create($content);
		return $this->save(null, $validate);
	}

/**
 * コンテンツデータよりURLを生成する
 * 
 * @param int $id コンテンツID
 * @param string $type タイプ
 * @return string URL
 */
	public function createUrl($id, $isContentFolder) {
		if($id == 1) {
			$url = '/';
		} else {
			$parents = $this->getPath($id, ['name'], -1);
			unset($parents[0]);
			$names = array();
			foreach($parents as $parent) {
				$names[] = $parent['Content']['name'];
			}
			$url = '/' . implode('/', $names);
			if($isContentFolder) {
				$url .= '/';
			}
		}
		return $url;
	}
	
/**
 * システムデータを更新する
 *
 * URL / 公開状態 / メインサイトの関連コンテンツID
 *
 * @param array $data
 * @return mixed
 */
	public function updateSystemData($data) {
		if(empty($data['Content']['name'])) {
			if($data['Content']['id'] != 1) {
				return false;	
			}
		}
		
		$isContentFolder = false;
		if(!empty($data['Content']['type']) && $data['Content']['type'] == 'ContentFolder') {
			$isContentFolder = true;
		}

		$site = $this->Site->find('first', ['conditions' => ['Site.id' => $data['Content']['site_id']]]);

		// URLを更新
		$data['Content']['url'] = $this->createUrl($data['Content']['id'], $isContentFolder);

		// 親フォルダの公開状態に合わせて公開状態を更新（自身も含める）
		if(isset($data['Content']['self_status'])) {
			$data['Content']['status'] = $data['Content']['self_status'];	
		}
		if(isset($data['Content']['self_publish_begin'])) {
			$data['Content']['publish_begin'] = $data['Content']['self_publish_begin'];	
		}
		if(isset($data['Content']['self_publish_end'])) {
			$data['Content']['publish_end'] = $data['Content']['self_publish_end'];
		}
		if($data['Content']['parent_id']) {
			$parent = $this->find('first', [
				'fields' => ['name', 'status', 'publish_begin', 'publish_end'], 
				'conditions' => ['Content.id' => $data['Content']['parent_id']], 
				'recursive' => -1
			]);
			if(!$parent['Content']['status'] || $parent['Content']['publish_begin'] || $parent['Content']['publish_begin']) {
				$data['Content']['status'] = $parent['Content']['status'];
				$data['Content']['publish_begin'] = $parent['Content']['publish_begin'];
				$data['Content']['publish_end'] = $parent['Content']['publish_end'];
			}
		}

		// 主サイトの関連コンテンツIDを更新
		if($site) {
			// 主サイトの同一階層のコンテンツを特定
			$prefix = $site['Site']['name'];
			if($site['Site']['alias']) {
				$prefix = $site['Site']['alias'];
			}
			$url = preg_replace('/^\/' . preg_quote($prefix, '/') . '\//', '/', $data['Content']['url']);
			$mainSitePrefix = $this->Site->getPrefix($site['Site']['main_site_id']);
			if($mainSitePrefix) {
				$url = '/' . $mainSitePrefix . $url;
			}
			$mainSiteContentId = $this->field('id', [
				'site_id'	=> $site['Site']['main_site_id'],
				'url'		=> $url
			]);
			// main_site_content_id を更新
			if($mainSiteContentId) {
				$data['Content']['main_site_content_id'] = $mainSiteContentId;
			} else {
				$data['Content']['main_site_content_id'] = null;
			}
		}
		$data = $this->save($data, array('validate' => false, 'callbacks' => false));
		$this->data = $data;
		return (bool) ($data);
	}

/**
 * ID を指定して公開状態かどうか判定する
 *
 * @param $id
 * @return bool
 */
	public function isPublishById($id) {
		$conditions = array_merge(['Content.id' => $id], $this->getConditionAllowPublish());
		return (bool) $this->find('first', ['conditions' => $conditions, 'recursive' => -1]);
	}

/**
 * 子ノードのURLを全て更新する
 * 
 * @param $id
 * @return bool
 */
	public function updateChildren($id) {
		// 他のデータを更新する為一旦退避
		$dataTmp = $this->data;
		$idTmp = $this->id;
		$children = $this->children($id, false, null, 'Content.lft');
		$result = true;
		if($children) {
			foreach($children as $child) {
				if(!$this->updateSystemData($child)) {
					$result = false;
				}
			}
		}
		// 退避したデータを戻す
		$this->data = $dataTmp;
		$this->id = $idTmp;
		return $result;
	}

/**
 * タイプよりコンテンツを取得する
 *
 * @param string $type 例）Blog.BlogContent
 * @param int $entityId
 * @return array
 */
	public function findByType($type, $entityId = null) {
		list($plugin, $type) = pluginSplit($type);
		if(!$plugin) {
			$plugin = 'Core';
		}
		$conditions = array(
			'plugin' => $plugin,
			'type'	=> $type,
			'alias_id' => null
		);
		if($entityId) {
			$conditions['Content.entity_id'] = $entityId;
		}
		return $this->find('first', array('conditions' => $conditions));
	}

/**
 * コンテンツフォルダーのリストを取得
 * コンボボックス用
 *
 * @param int $siteId
 * @param array $options
 * @return array|bool
 */
	public function getContentFolderList($siteId = null, $options = array()) {
		$options = array_merge(array(
			'excludeId' => null
		), $options);

		$conditions = [
			'type' => 'ContentFolder', 
			'alias_id' => null
		];
		if(!is_null($siteId)) {
			$conditions['site_id'] = $siteId;
		}
		if($options['excludeId']) {
			$conditions['id <>'] = $options['excludeId'];
		}
		if(!empty($options['conditions'])) {
			$conditions = array_merge($conditions, $options['conditions']);
		}
		$folders = $this->generateTreeList($conditions);
		if($folders) {
			return $this->convertTreeList($folders);
		}
		return false;
	}

/**
 * ツリー構造のデータを コンボボックスのデータ用に変換する
 * @param $nodes
 * @return array
 */
	public function convertTreeList($nodes) {
		if(!$nodes) {
			return array();
		}
		foreach ($nodes as $key => $value) {
			if (preg_match("/^([_]+)/i", $value, $matches)) {
				$value = preg_replace("/^[_]+/i", '', $value);
				$prefix = str_replace('_', '&nbsp;&nbsp;&nbsp;', $matches[1]);
				$value = $prefix . '└' . $value;
			}
			$nodes[$key] = $value;
		}
		return $nodes;
	}

/**
 * ツリー構造より論理削除する
 *
 * @param $id
 * @return bool
 */
	public function softDeleteFromTree($id) {
		$this->softDelete(true);
		$this->Behaviors->unload('BcCache');
		$result = $this->deleteRecursive($id);
		$this->Behaviors->load('BcCache');
		$this->delAssockCache();
		return $result;
	}

/**
 * 再帰的に削除
 *
 * @param $id
 * @return bool
 */
	public function deleteRecursive($id) {
		if(!$id) {
			return false;
		}
		$children = $this->children($id, true);
		$result = true;
		if($children) {
			foreach($children as $child) {
				if(!$this->deleteRecursive($child['Content']['id'])) {
					$result = false;
				}
			}
		}
		if($result) {
			$content = $this->find('first', array('conditions' => array('Content.id' => $id), 'recursive' => -1));
			if(empty($content['Content']['alias_id'])) {
				$content['Content']['parent_id'] = null;
				$content['Content']['url'] = '';
				$content['Content']['status'] = false;
				$content['Content']['self_status'] = false;
				unset($content['Content']['lft']);
				unset($content['Content']['rght']);
				$this->save($content, array('validate' => false, 'callbacks' => false));
				return $this->delete($id);
			} else {
				$this->softDelete(false);
				$result = $this->removeFromTree($content['Content']['id'], true);
				$this->softDelete(true);
				return $result;
			}
		}
		return false;
	}

/**
 * ゴミ箱より元に戻す
 *
 * @param $id
 */
	public function trashReturn($id) {
		return $this->trashReturnRecursive($id, true);
	}

/**
 * 再帰的にゴミ箱より元に戻す
 *
 * @param $id
 * @return bool|int
 */
	public function trashReturnRecursive($id, $top = false) {
		$this->softDelete(false);
		$children = $this->children($id, true);
		$this->softDelete(true);
		$result = true;
		if($children) {
			foreach($children as $child) {
				if(!$this->trashReturnRecursive($child['Content']['id'])) {
					$result = false;
				}
			}
		}
		$this->Behaviors->unload('Tree');
		$this->updatingRelated = false;
		if($result && $this->undelete($id)) {
			$this->Behaviors->load('Tree');
			$this->updatingRelated = true;
			$content = $this->find('first', ['conditions' => ['Content.id' => $id], 'recursive' => -1]);
			if($top) {
				$siteRootId = $this->field('id', array('Content.site_id' => $content['Content']['site_id'], 'site_root' => true));
				$content['Content']['parent_id'] = $siteRootId;
			}
			unset($content['Content']['name']);
			unset($content['Content']['lft']);
			unset($content['Content']['rght']);
			if($this->save($content, true)) {
				return $content['Content']['site_id'];
			} else {
				$result = false;
			}
		} else {
			$this->Behaviors->load('Tree');
			$result = false;
		}
		return $result;
	}

/**
 * タイプよりコンテンツを削除する
 *
 * @param string $type 例）Blog.BlogContent
 * @param int $entityId
 * @return bool
 */
	public function deleteByType($type, $entityId = null) {
		list($plugin, $type) = pluginSplit($type);
		if(!$plugin) {
			$plugin = 'Core';
		}
		$conditions = array(
			'plugin'	=> $plugin,
			'type'		=> $type,
			'alias_id'	=> null
		);
		if($entityId) {
			$conditions['Content.entity_id'] = $entityId;
		}
		$this->softDelete(false);
		$id = $this->field('id', $conditions);
		return $this->removeFromTree($id, true);
	}

/**
 * コンテンツIDよりURLを取得する
 *
 * @param $id
 * @return string
 */
	public function getUrlById($id, $full = false) {
		$data = $this->find('first', ['conditions' => ['Content.id' => $id]]);
		return $this->getUrl($data['Content']['url'], $full, $data['Site']['use_subdomain']);
	}

/**
 * URLを取得する
 *
 * @param $url
 * @param bool $useSubDomain
 * @return string
 */
	public function getUrl($url, $full = false, $useSubDomain = false) {
		if($useSubDomain && !is_array($url)) {
			$urlArray = explode('/', preg_replace('/(^\/|\/$)/', '', $url));
			$subDomain = $urlArray[0];
			unset($urlArray[0]);
			$originUrl = '/' . implode('/', $urlArray);
			if(preg_match('/\/$/', $url) && count($urlArray) > 0) {
				$originUrl .= '/';
			}
			if($full) {
				$fullUrl = fullUrl($originUrl);
				if (BcUtil::isAdminSystem()) {
					$furllUrlArray = explode('//', $fullUrl);
					return $furllUrlArray[0] . '//' . $subDomain . '.' . $furllUrlArray[1];
				} else {
					return $fullUrl;
				}
			} else {
				return Router::url($originUrl);
			}
		} else {
			if(BC_INSTALLED) {
				$site = BcSite::findCurrent(false);
				if($site && $site->sameMainUrl) {
					$url = $site->getPureUrl($url);
				}
			}
			$params = explode('?', $url);
			$url = preg_replace('/\/index$/', '/', $params[0]);
			if(!empty($params[1])) {
				$url .= '?' . $params[1];
			}
			if($full) {
				return fullUrl($url);
			} else {
				return Router::url($url);
			}
		}
	}

/**
 * 現在のフォルダのURLを元に別サイトにフォルダを生成する
 * 最下層のIDを返却する
 *
 * @param $currentUrl
 * @param $targetSiteId
 * @return bool|null
 */
	public function copyContentFolderPath($currentUrl, $targetSiteId) {
		$currentId = $this->field('id', ['Content.url' => $currentUrl]);
		if(!$currentId) {
			return false;
		}
		$prefix = $this->Site->getPrefix($targetSiteId);
		$path = $this->getPath($currentId, null, -1);
		if(!$path) {
			return false;
		}
		$url = '/';
		if($prefix) {
			$url .= $prefix . '/';
		}
		unset($path[0]);
		$parentId = $this->Site->getRootContentId($targetSiteId);
		$ContentFolder = ClassRegistry::init('ContentFolder');
		foreach($path as $currentContentFolder) {
			if($currentContentFolder['Content']['type'] != 'ContentFolder') {
				break;
			}
			if($currentContentFolder['Content']['site_root']) {
				continue;
			}
			$url .= $currentContentFolder['Content']['name'];
			if($this->find('first', ['conditions' => ['Content.url' => $url], 'recursive' => -1])) {
				return false;
			}
			$url .= '/';
			$targetContentFolder = $this->find('first', ['conditions' => ['Content.url' => $url], 'recursive' => -1]);
			if($targetContentFolder) {
				$parentId = $targetContentFolder['Content']['id'];
			} else {
				$data = [
					'Content' => [
						'name'		=> $currentContentFolder['Content']['name'],
						'title' 	=> $currentContentFolder['Content']['title'],
						'parent_id' => $parentId,
						'plugin'	=> 'Core',
						'type' 		=> 'ContentFolder',
						'site_id' 	=> $targetSiteId,
						'self_status' 	=> true
					]
				];
				$ContentFolder->create($data);
				if($ContentFolder->save()) {
					$parentId = $ContentFolder->Content->id;
				} else {
					return false;
				}
			}
		}
		return $parentId;
	}

/**
 * コピーする
 *
 * @param $id
 * @param $newTitle
 * @param $newAuthorId
 * @param $entityId
 * @return mixed
 */
	public function copy($id, $entityId, $newTitle, $newAuthorId, $newSiteId = null) {

		$data = $this->find('first', array('conditions' => array('Content.id' => $id)));
		$url = $data['Content']['url'];
		if(!is_null($newSiteId) && $data['Site']['id'] != $newSiteId) {
			$data['Content']['site_id'] = $newSiteId;
			$data['Content']['parent_id'] = $this->copyContentFolderPath($url, $newSiteId);
		}
		unset($data['Content']['id']);
		unset($data['Content']['modified_date']);
		unset($data['Content']['created']);
		unset($data['Content']['modified']);
		unset($data['Content']['main_site_content']);
		if($newTitle) {
			$data['Content']['title'] = $newTitle;
		} else {
			$data['Content']['title'] .= 'のコピー';
		}
		$data['Content']['self_publish_begin'] = null;
		$data['Content']['self_publish_end'] = null;
		$data['Content']['self_status'] = false;
		$data['Content']['author_id'] = $newAuthorId;
		$data['Content']['created_date'] = date('Y-m-d H:i:s');
		$data['Content']['entity_id'] = $entityId;
		unset($data['Site']);
		$this->create($data);
		return $this->save($data);

	}

/**
 * 公開済の conditions を取得
 *
 * @return array 公開条件（conditions 形式）
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
 * 公開状態を取得する
 *
 * @param array $data コンテンツデータ
 * @return boolean 公開状態
 */
	public function isAllowPublish($data, $self = false) {

		if (isset($data['Content'])) {
			$data = $data['Content'];
		}
		
		$fields = [
			'status' => 'status',
			'publish_begin' => 'publish_begin',
			'publish_end' => 'publish_end'
		];
		if($self) {
			foreach($fields as $key => $field) {
				$fields[$key] = 'self_' . $field;
			}
		}
		
		$allowPublish = (int) $data[$fields['status']];

		// 期限を設定している場合に条件に該当しない場合は強制的に非公開とする
		if (($data[$fields['publish_begin']] != 0 && $data[$fields['publish_begin']] >= date('Y-m-d H:i:s')) ||
			($data[$fields['publish_end']] != 0 && $data[$fields['publish_end']] <= date('Y-m-d H:i:s'))) {
			$allowPublish = false;
		}

		return $allowPublish;
	}

/**
 * 指定したURLのパス上のコンテンツでフォルダ以外が存在するか確認
 *
 * @param $url
 * @return bool
 */
	public function existsContentByUrl($url) {
		$urlAry = explode('/', preg_replace('/(^\/|\/$)/', '', $url));
		if(!$url) {
			return false;
		}
		$url = '/';
		$last = count($urlAry);
		foreach($urlAry as $key => $name) {
			$url .= $name;
			$conditions = ['Content.url' => $url];
			if(($key + 1) != $last) {
				$conditions['Content.type <>'] = 'ContentFolder';
			}
			if($this->find('first', ['conditions' => ['Content.url' => $url, 'Content.type <>' => 'ContentFolder'], 'recursive' => -1])) {
				return true;
			}
			$url .= '/';
		}
		return false;
	}

/**
 * 公開されたURLが存在するか確認する
 * 
 * @param string $url
 * @return bool
 */
	public function existsPublishUrl($url) {
		$conditions = $this->getConditionAllowPublish();
		$conditions['url'] = $url;
		return (boolean) $this->find('count', ['conditions' => $conditions]);
	}

/**
 * データが公開済みかどうかチェックする
 *
 * @param boolean $status 公開ステータス
 * @param string $publishBegin 公開開始日時
 * @param string $publishEnd 公開終了日時
 * @return	bool
 */
	public function isPublish($status, $publishBegin, $publishEnd) {
		if (!$status) {
			return false;
		}
		if ($publishBegin && $publishBegin != '0000-00-00 00:00:00') {
			if ($publishBegin > date('Y-m-d H:i:s')) {
				return false;
			}
		}
		if ($publishEnd && $publishEnd != '0000-00-00 00:00:00') {
			if ($publishEnd < date('Y-m-d H:i:s')) {
				return false;
			}
		}
		return true;
	}
	
/**
 * 移動元のコンテンツと移動先のディレクトリから移動が可能かチェックする
 * 
 * @param $currentId int 移動元コンテンツID
 * @param $targetParentId int 移動先コンテンツID (ContentFolder)
 * @return bool
 */
	public function isMovable($currentId, $targetParentId) {
		$currentContent = $this->find('first', [
			'conditions' => ['id' => $currentId],
			'recursive' => -1
		]);
		$parentCuntent = $this->find('first', [
			'conditions' => ['id' => $targetParentId],
			'recursive' => -1
		]);
		
		// 指定コンテンツがない
		if (!$currentContent || !$parentCuntent) {
			return false;
		}
		
		// 移動先に同一コンテンツが存在する
		$movedUrl = $parentCuntent['Content']['url'] . $currentContent['Content']['name'];
		$movedContent = $this->find('first', [
			'conditions' => ['url' => $movedUrl],
			'recursive' => -1
		]);
		if ($movedContent) {
			return false;
		}
		
		return true;
	}

/**
 * タイトル、URL、公開状態が更新されているか確認する
 *
 * @param int $id コンテンツID
 * @param array $newData 新しいコンテンツデータ
 */
	public function isChangedStatus($id, $newData)	{
		$before = $this->find('first', ['conditions' => ['Content.id' => $id]]);
		if(!$before) {
			return true;
		}
		$beforeStatus = $this->isPublish($before['Content']['self_status'], $before['Content']['self_publish_begin'], $before['Content']['self_publish_end']);
		$afterStatus = $this->isPublish($newData['Content']['self_status'], $newData['Content']['self_publish_begin'], $newData['Content']['self_publish_end']);
		if ($beforeStatus != $afterStatus || $before['Content']['title'] != $newData['Content']['title'] || $before['Content']['url'] != $newData['Content']['url']) {
			return true;
		}
		return false;
	}

/**
 * サイトルートコンテンツを取得する
 * 
 * @param $siteId
 * @return array|null
 */
	public function getSiteRoot($siteId) {
		return $this->find('first', [
			'conditions' => [
				'Content.site_id' => $siteId, 
				'Content.site_root' => true
		], 'recursive' => -1]);
	}

/**
 * 親のテンプレートを取得する
 *
 * @param $id
 */
	public function getParentTemplate($id) {
		$contents = $this->getPath($id, null, -1);
		$contents = array_reverse($contents);
		unset($contents[0]);
		$parentTemplates = Hash::extract($contents, '{n}.Content.layout_template');
		$parentTemplate = '';
		foreach($parentTemplates as $parentTemplate) {
			if($parentTemplate) {
				break;
			}
		}
		if(!$parentTemplate) {
			$parentTemplate = 'default';
		}
		return $parentTemplate;
	}

/**
 * コンテンツを移動する
 * 
 * 基本的に targetId の上に移動する前提となる
 * targetId が空の場合は、同親中、一番下に移動する
 * 
 * @param $currentId
 * @param $type
 * @param $targetSiteId
 * @param $targetParentId
 * @param $targetId
 * @return array|bool|false
 */
	public function move($currentId, $currentParentId, $targetSiteId, $targetParentId, $targetId) {
		$this->moveRelateSubSiteContent($currentId, $targetParentId, $targetId);
		$targetSort = $this->getOrderSameParent($targetId, $targetParentId);
		if($currentParentId != $targetParentId) {
			$data = $this->find('first', [
				'conditions' => ['Content.id' => $currentId],
				'recursive' => -1
			]);
			// 親を変更
			$data = $this->save(['Content' => [
				'id'		=> $currentId,
				'name'		=> $data['Content']['name'],
				'type' 		=> $data['Content']['type'],
				'parent_id' => $targetParentId,
				'site_id'	=> $targetSiteId
			]], false);
			// フォルダにコンテンツがない場合、targetId が空で一番後を指定の場合は、親を変更して終了
			if(!$targetSort || !$targetId) {
				return $data;
			}
			$currentSort = $this->getOrderSameParent(null, $targetParentId);
		} else {
			$currentSort = $this->getOrderSameParent($currentId, $targetParentId);	
		}
		// 親変更後のオフセットを取得
		$offset = $targetSort - $currentSort;
		// オフセットを元に移動
		return $this->moveOffset($currentId, $offset);
	}

/**
 * メインサイトの場合、連携設定がされている子サイトも移動する
 *
 * @param $data
 */
	public function moveRelateSubSiteContent($mainCurrentId, $mainTargetParentId, $mainTargetId) {
		// 他のデータを更新する為、一旦退避
		$dataTmp = $this->data;
		$idTmp = $this->id;
		$data = $this->find('first', ['conditions' => ['Content.id' => $mainCurrentId], 'recursive' => -1]);
		// 自身がエイリアスか確認し、エイリアスの場合は終了
		if(!empty($data['Content']['alias_id']) || !isset($data['Content']['site_id']) || !isset($data['Content']['type'])) {
			return true;
		}
		// メインサイトか確認し、メインサイトでない場合は終了
		if(!$this->Site->isMain($data['Content']['site_id'])) {
			return true;
		}
		// 連携設定となっている小サイトを取得
		$sites = $this->Site->find('all', ['conditions' => ['Site.main_site_id' => $data['Content']['site_id'], 'relate_main_site' => true]]);
		if(!$sites) {
			return true;
		}
		$result = true;
		foreach($sites as $site) {
			// 自信をメインコンテンツとしているデータを取得
			$current = $this->find('first', ['conditions' => ['Content.main_site_content_id' => $mainCurrentId, 'Content.site_id' => $site['Site']['id']], 'recursive' => -1]);
			if(!$current) {
				continue;
			}
			$currentId = $current['Content']['id'];
			$currentParentId = $current['Content']['parent_id'];
			$target = null;
			$targetId = "";
			$targetParentId = "";
			if($mainTargetId) {
				$target = $this->find('first', ['conditions' => ['Content.main_site_content_id' => $mainTargetId, 'Content.site_id' => $site['Site']['id']], 'recursive' => -1]);
				if($target) {
					$targetId = $target['Content']['id'];
					$targetParentId = $target['Content']['parent_id'];
				}	
			}
			if(!$target) {
				// ターゲットが見つからない場合は親IDより取得
				$target = $this->find('first', ['conditions' => ['Content.main_site_content_id' => $mainTargetParentId, 'Content.site_id' => $site['Site']['id']], 'recursive' => -1]);
				if($target) {
					$targetParentId = $target['Content']['id'];
				}
			}
			if(!$target) {
				continue;
			}
			$targetSiteId = $target['Content']['site_id'];
			if(!$this->move($currentId, $currentParentId, $targetSiteId, $targetParentId, $targetId)) {
				$result = false;
			}	
		}
		// 退避したデータを戻す
		$this->data = $dataTmp;
		$this->id = $idTmp;
		return $result;
	}
	
/**
 * オフセットを元にコンテンツを移動する
 *
 * @param $id
 * @param $offset
 * @return array|false
 */
	public function moveOffset($id, $offset) {
		$offset = (int) $offset;
		if($offset > 0) {
			$result = $this->moveDown($id, abs($offset));
		} elseif($offset < 0) {
			$result = $this->moveUp($id, abs($offset));
		} else {
			$result = true;
		}
		if($result) {
			return $this->find('first', [
				'conditions' => ['Content.id' => $id],
				'recursive' => -1
			]);
		} else {
			return false;
		}
	}

/**
 * 同じ階層における並び順を取得
 * 
 * id が空の場合は、一番最後とみなす
 * 
 * @param $id
 * @param $parentId
 * @return bool|int|null|string
 */
	public function getOrderSameParent($id, $parentId) {
		$contents = $this->find('all', array(
			'fields' => array('Content.id', 'Content.parent_id', 'Content.title'),
			'order' => 'lft',
			'conditions' => ['Content.parent_id' => $parentId],
			'recursive' => -1
		));
		$order = null;
		if($contents) {
			if($id) {
				foreach($contents as $key => $data) {
					if($id == $data['Content']['id']) {
						$order = $key + 1;
						break;
					}
				}
			} else {
				return count($contents);
			}
		} else {
			return false;
		}
		return $order;
	}

/**
 * 関連サイトの関連コンテンツを取得する
 * 
 * @param int $id
 * @return array|false
 */
	public function getRelatedSiteContents($id) {
		$conditions = [
			'OR' => [
				['Content.id' => $id],
				['Content.main_site_content_id' => $id]
			]
		];
		$conditions = array_merge($conditions, $this->getConditionAllowPublish());
		$contents = $this->find('all', [
			'conditions' => $conditions,
			'recursive' => 0
		]);
		$mainSite = $this->Site->getRootMain();
		foreach($contents as $key => $content) {
			if($content['Content']['site_id'] == 0) {
				$contents[$key]['Site'] = $mainSite['Site'];
			}
		}
		return $contents;
	}

/**
 * キャッシュ時間を取得する
 *
 * @param mixed $id | $data
 * @return mixed int or false
 */
	public function getCacheTime($data) {
		if(!is_array($data)) {
			$data = $this->find('first', array('conditions' => array('Content.id' => $data), 'recursive' => 0));
		}
		if(isset($data['Content'])) {
			$data = $data['Content'];
		}
		if (!$data) {
			return false;
		}
		if ($data['status'] && $data['publish_end'] && $data['publish_end'] != '0000-00-00 00:00:00') {
			return strtotime($data['publish_end']) - time();
		} else {
			// #10680 Modify 2016/01/22 gondoh
			// 3.0.10 で追加されたViewキャッシュ分離の設定値を、後方互換のため存在しない場合は旧情報で取り込む 
			$duration = Configure::read('BcCache.viewDuration');
			if (empty($duration)) $duration = Configure::read('BcCache.duration');
			return $duration;
		}
	}
	
}