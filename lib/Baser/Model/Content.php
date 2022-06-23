<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Model
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * Class Content
 *
 * コンテンツモデル
 *
 * @package Baser.Model
 * @property Site $Site
 */
class Content extends AppModel
{

	/**
	 * ビヘイビア
	 *
	 * @var array
	 */
	public $actsAs = [
		'Tree' => ['level' => 'level'],
		'BcCache',
		'SoftDelete',
		'BcUpload' => [
			'saveDir' => "contents",
			'fields' => [
				'eyecatch' => [
					'type' => 'image',
					'namefield' => 'id',
					'nameadd' => true,
					'nameformat' => '%08d',
					'subdirDateFormat' => 'Y/m',
					//'imageresize' => array('width' => '800', 'height' => '800'),
					'imagecopy' => [
						'thumb' => ['suffix' => '_thumb', 'width' => '300', 'height' => '300'],
						'medium' => ['suffix' => '_midium', 'width' => '800', 'height' => '800']
					]
				]
			]
		]
	];

	/**
	 * Belongs To
	 *
	 * @var array
	 */
	public $belongsTo = [
		'Site' => [
			'className' => 'Site',
			'foreignKey' => 'site_id'
		],
		'User' => [
			'className' => 'User',
			'foreignKey' => 'author_id'
		]
	];

	/**
	 * 関連データを更新する
	 *
	 * @var bool
	 */
	public $updatingRelated = true;

	/**
	 * システムデータを更新する
	 *
	 * @var bool
	 */
	public $updatingSystemData = true;

	/**
	 * 保存前の親ID
	 *
	 * IDの変更比較に利用
	 *
	 * @var null
	 */
	public $beforeSaveParentId = null;

	/**
	 * 削除時の削除対象レコード
	 *
	 * afterDelete で利用する為、beforeDelete で取得し保存する
	 * @var []
	 */
	private $__deleteTarget;

	/**
	 * Implemented Events
	 *
	 * beforeDelete の優先順位を SoftDeleteBehaviorより高くする為に調整
	 *
	 * @return array
	 */
	public function implementedEvents()
	{
		return [
			'Model.beforeFind' => ['callable' => 'beforeFind', 'passParams' => true],
			'Model.afterFind' => ['callable' => 'afterFind', 'passParams' => true],
			'Model.beforeValidate' => ['callable' => 'beforeValidate', 'passParams' => true],
			'Model.afterValidate' => ['callable' => 'afterValidate'],
			'Model.beforeSave' => ['callable' => 'beforeSave', 'passParams' => true],
			'Model.afterSave' => ['callable' => 'afterSave', 'passParams' => true],
			'Model.beforeDelete' => ['callable' => 'beforeDelete', 'passParams' => true, 'priority' => 1],
			'Model.afterDelete' => ['callable' => 'afterDelete'],
		];
	}

	/**
	 * Content constructor.
	 *
	 * @param bool $id
	 * @param null $table
	 * @param null $ds
	 */
	public function __construct($id = false, $table = null, $ds = null)
	{
		parent::__construct($id, $table, $ds);
		$this->validate = [
			'id' => [
				['rule' => 'numeric', 'on' => 'update', 'message' => __d('baser', 'IDに不正な値が利用されています。')]],
			'name' => [
				['rule' => ['notBlank'], 'message' => __d('baser', 'URLを入力してください。')],
				['rule' => ['bcUtileUrlencodeBlank'], 'message' => __d('baser', 'URLはスペース、全角スペース及び、指定の記号(\\\'|`^"(){}[];/?:@&=+$,%<>#!)だけの名前は付けられません。')],
				['rule' => ['notBlank'], 'message' => __d('baser', 'スラッグを入力してください。')],
				['rule' => ['maxLength', 2083], 'message' => __d('baser', 'タイトルは230文字以内で入力してください。')],
				['rule' => ['duplicateRelatedSiteContent'], 'message' => __d('baser', '連携しているサブサイトでスラッグが重複するコンテンツが存在します。重複するコンテンツのスラッグ名を先に変更してください。')]],
			'title' => [
				['rule' => ['bcUtileUrlencodeBlank'], 'message' => __d('baser', 'タイトルはスペース、全角スペース及び、指定の記号(\\\'|`^"(){}[];/?:@&=+$,%<>#!)だけの名前は付けられません。')],
				['rule' => '/\A(?!.*(\t)).*\z/', 'message' => __d('baser', 'タイトルはタブを含む名前は付けられません。')],
				['rule' => ['notBlank'], 'message' => __d('baser', 'タイトルを入力してください。')],
				['rule' => ['maxLength', 230], 'message' => __d('baser', 'タイトルは230文字以内で入力してください。')]],
			'eyecatch' => [
				['rule' => ['fileCheck', $this->convertSize(ini_get('upload_max_filesize'))], 'message' => __d('baser', 'ファイルのアップロードに失敗しました。')]],
			'self_publish_begin' => [
				['rule' => ['checkDate'], 'allowEmpty' => true, 'message' => __d('baser', '公開開始日に不正な文字列が入っています。')]],
			'self_publish_end' => [
				['rule' => ['checkDate'], 'allowEmpty' => true, 'message' => __d('baser', '公開終了日に不正な文字列が入っています。')],
				['rule' => ['checkDateAfterThan', 'self_publish_begin'], 'message' => __d('baser', '公開終了日は、公開開始日より新しい日付で入力してください。')]],
			'created_date' => [
				['rule' => ['checkDate'], 'allowEmpty' => true, 'message' => __d('baser', '作成日に不正な文字列が入っています。')]],
			'modified_date' => [
				['rule' => ['checkDate'], 'allowEmpty' => true, 'message' => __d('baser', '更新日に不正な文字列が入っています。')]],
		];
	}

	/**
	 * サイト設定にて、エイリアスを利用してメインサイトと自動連携するオプションを利用時に、
	 * 関連するサブサイトで、関連コンテンツを作成する際、同階層に重複名称のコンテンツがないか確認する
	 *
	 *    - 新規の際は、存在するだけでエラー
	 *    - 編集の際は、main_site_content_id が自身のIDでない、alias_id が自身のIDでない場合エラー
	 *
	 * @param $check
	 * @return bool
	 */
	public function duplicateRelatedSiteContent($check)
	{
		$name = $check[key($check)];
		if (!$this->Site->isMain($this->data['Content']['site_id'])) {
			return true;
		}
		$parents = $this->getPath($this->data['Content']['parent_id'], ['name'], -1);
		$parents = Hash::extract($parents, "{n}.Content.name");
		unset($parents[0]);
		if ($this->data['Content']['site_id']) {
			unset($parents[1]);
		}
		$baseUrl = '/';
		if ($parents) {
			$baseUrl = '/' . implode('/', $parents) . '/';
		}
		$sites = $this->Site->find('all', ['conditions' => ['Site.main_site_id' => $this->data['Content']['site_id'], 'relate_main_site' => true]]);
		// URLを取得
		$urlAry = [];
		foreach($sites as $site) {
			$prefix = $site['Site']['name'];
			if ($site['Site']['alias']) {
				$prefix = $site['Site']['alias'];
			}
			$urlAry[] = '/' . $prefix . $baseUrl . $name;
		}
		$conditions = ['Content.url' => $urlAry];
		if (!empty($this->data['Content']['id'])) {
			$conditions = array_merge($conditions, [
				['or' => ['Content.alias_id <>' => $this->data['Content']['id'], 'Content.alias_id' => null]],
				['or' => ['Content.main_site_content_id <>' => $this->data['Content']['id'], 'Content.main_site_content_id' => null]]
			]);
		}
		if ($this->find('count', ['conditions' => $conditions])) {
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
	public function beforeValidate($options = [])
	{
		// コンテンツ一覧にて、コンテンツを登録した直後のリネーム処理までは新規追加とみなして処理を行う為、$create で判定させる
		$create = false;
		if (empty($this->data['Content']['id']) || !empty($options['firstCreate'])) {
			$create = true;
		}
		// タイトルは強制的に255文字でカット
		if (!empty($this->data['Content']['title'])) {
			$this->data['Content']['title'] = mb_substr($this->data['Content']['title'], 0, 254, 'UTF-8');
		}
		if ($create) {
			// IEのURL制限が2083文字の為、全て全角文字を想定し231文字でカット
			if (!isset($this->data['Content']['name'])) {
				$this->data['Content']['name'] = BcUtil::urlencode(mb_substr($this->data['Content']['title'], 0, 230, 'UTF-8'));
			}
			if (!isset($this->data['Content']['self_status'])) {
				$this->data['Content']['self_status'] = false;
			}
			if (!isset($this->data['Content']['self_publish_begin'])) {
				$this->data['Content']['self_publish_begin'] = null;
			}
			if (!isset($this->data['Content']['self_publish_end'])) {
				$this->data['Content']['self_publish_end'] = null;
			}
			if (!isset($this->data['Content']['deleted'])) {
				$this->data['Content']['deleted'] = false;
			}
			if (!isset($this->data['Content']['created_date'])) {
				$this->data['Content']['created_date'] = date('Y-m-d H:i:s');
			}
			if (!isset($this->data['Content']['site_root'])) {
				$this->data['Content']['site_root'] = 0;
			}
			if (!isset($this->data['Content']['exclude_search'])) {
				$this->data['Content']['exclude_search'] = 0;
			}
			if (!isset($this->data['Content']['author_id'])) {
				$user = BcUtil::loginUser('admin');
				$this->data['Content']['author_id'] = $user['id'];
			}
		} else {
			if (empty($this->data['Content']['modified_date'])) {
				$this->data['Content']['modified_date'] = date('Y-m-d H:i:s');
			}
			if (isset($this->data['Content']['name'])) {
				$this->data['Content']['name'] = BcUtil::urlencode(mb_substr($this->data['Content']['name'], 0, 230, 'UTF-8'));
			}
			if ($this->data['Content']['id'] == 1) {
				unset($this->validate['name']);
			}
		}
		// name の 重複チェック＆リネーム
		if (!empty($this->data['Content']['name'])) {
			$contentId = null;
			if (!empty($this->data['Content']['id'])) {
				$contentId = $this->data['Content']['id'];
			}
			$this->data['Content']['name'] = $this->getUniqueName($this->data['Content']['name'], $this->data['Content']['parent_id'], $contentId);
		}
		return true;
	}

	/**
	 * After Validate
	 */
	public function afterValidate()
	{
		parent::afterValidate();
		// 新規追加の際、name は、title より自動設定される為、バリデーションエラーが発生してもエラーメッセージを表示しない
		if (empty($this->data['Content']['id']) && !empty($this->validationErrors['name'])) {
			unset($this->validationErrors['name']);
		}
	}

	/**
	 * 一意の name 値を取得する
	 *
	 * @param string $name name フィールドの値
	 * @return string
	 */
	public function getUniqueName($name, $parentId, $contentId = null)
	{

		// 先頭が同じ名前のリストを取得し、後方プレフィックス付きのフィールド名を取得する
		$conditions = [
			'Content.name LIKE' => $name . '%',
			'Content.parent_id' => $parentId,
			'site_root' => false
		];
		if ($contentId) {
			$conditions['Content.id <>'] = $contentId;
		}
		$datas = $this->find('all', ['conditions' => $conditions, 'fields' => ['name'], 'order' => "Content.name", 'recursive' => -1]);
		$datas = Hash::extract($datas, "{n}.Content.name");
		$numbers = [];

		if ($datas) {
			foreach($datas as $data) {
				if ($name === $data) {
					$numbers[1] = 1;
				} elseif (preg_match("/^" . preg_quote($name, '/') . "_([0-9]+)$/s", $data, $matches)) {
					$numbers[$matches[1]] = true;
				}
			}
			if ($numbers) {
				$prefixNo = 1;
				while(true) {
					if (!isset($numbers[$prefixNo])) {
						break;
					}
					$prefixNo++;
				}
				if ($prefixNo == 1) {
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
	public function beforeSave($options = [])
	{
		if (!empty($this->data['Content']['id'])) {
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
	public function afterSave($created, $options = [])
	{
		parent::afterSave($created, $options);
		$this->deleteAssocCache($this->data);
		if ($this->updatingSystemData) {
			$this->updateSystemData($this->data);
		}
		if ($this->updatingRelated) {
			// ゴミ箱から戻す場合、 type の定義がないが問題なし
			if (!empty($this->data['Content']['type']) && $this->data['Content']['type'] == 'ContentFolder') {
				$this->updateChildren($this->data['Content']['id']);
			}
			$this->updateRelateSubSiteContent($this->data);
			if (!empty($this->data['Content']['parent_id']) && $this->beforeSaveParentId != $this->data['Content']['parent_id']) {
				$SiteConfig = ClassRegistry::init('SiteConfig');
				$SiteConfig->updateContentsSortLastModified();
				$this->beforeSaveParentId = null;
			}
		}
	}

	/**
	 * 関連するコンテンツ本体のデータキャッシュを削除する
	 * @param $data
	 */
	public function deleteAssocCache($data)
	{
		if (empty($data['Content']['plugin']) || empty($data['Content']['type'])) {
			$data = $this->find('first', ['fields' => ['Content.plugin', 'Content.type'], 'conditions' => ['Content.id' => $data['Content']['id']], 'recursive' => -1]);
		}
		$assoc = $data['Content']['type'];
		if ($data['Content']['plugin'] != 'Core') {
			if (!CakePlugin::loaded($data['Content']['plugin'])) {
				return;
			}
			$assoc = $data['Content']['plugin'] . '.' . $assoc;
		}
		$AssocModel = ClassRegistry::init($assoc);
		if ($AssocModel && !empty($AssocModel->actsAs) && in_array('BcCache', $AssocModel->actsAs)) {
			if ($AssocModel->Behaviors->hasMethod('delCache')) {
				$AssocModel->delCache();
			}
		}
	}

	/**
	 * Before Delete
	 *
	 * 論理削除の場合、
	 * @param bool $cascade
	 * @return bool
	 */
	public function beforeDelete($cascade = true)
	{
		if (!parent::beforeDelete($cascade)) {
			return false;
		}
		$data = $this->find('first', [
			'conditions' => [$this->alias . '.id' => $this->id]
		]);
		$this->__deleteTarget = $data;
		if (!$this->softDelete(null)) {
			return true;
		}
		return true;
	}

	/**
	 * After Delete
	 *
	 * 関連コンテンツのキャッシュを削除する
	 */
	public function afterDelete()
	{
		parent::afterDelete();
		$data = $this->__deleteTarget;
		$this->__deleteTarget = null;
		if ($data) {
			$this->deleteRelateSubSiteContent($data);
			$this->deleteAlias($data);
		}
		$this->deleteAssocCache($data);
	}

	/**
	 * 自データのエイリアスを削除する
	 *
	 * 全サイトにおけるエイリアスを全て削除
	 *
	 * @param $data
	 */
	public function deleteAlias($data)
	{
		//自身がエイリアスか確認し、エイリアスの場合は終了
		if ($data['Content']['alias_id']) {
			return;
		}
		$contents = $this->find('all', [
			'fields' => ['Content.id'],
			'conditions' => [
				'Content.alias_id' => $data['Content']['id']
			], 'recursive' => -1]);
		if (!$contents) {
			return;
		}
		foreach($contents as $content) {
			$softDelete = $this->softDelete(null);
			$this->softDelete(false);
			$this->removeFromTree($content['Content']['id'], true);
			$this->softDelete($softDelete);
		}
		$this->data = $data;
		$this->id = $data['Content']['id'];
	}

	/**
	 * メインサイトの場合、連携設定がされている子サイトのエイリアス削除する
	 *
	 * @param $data
	 */
	public function deleteRelateSubSiteContent($data)
	{
		// 自身がエイリアスか確認し、エイリアスの場合は終了
		if ($data['Content']['alias_id']) {
			return;
		}
		// メインサイトか確認し、メインサイトでない場合は終了
		if (!$this->Site->isMain($data['Content']['site_id'])) {
			return;
		}
		// 連携設定となっている小サイトを取得
		$sites = $this->Site->find('all', ['conditions' => ['Site.main_site_id' => $data['Content']['site_id'], 'relate_main_site' => true], 'recursive' => -1]);
		if (!$sites) {
			return;
		}
		// 同階層に同名のコンテンツがあるか確認
		foreach($sites as $site) {
			$content = $this->find('first', ['conditions' => [
				'Content.site_id' => $site['Site']['id'],
				'Content.main_site_content_id' => $data['Content']['id']
			], 'recursive' => -1]);
			if ($content) {
				// 存在する場合は、自身のエイリアスかどうか確認し削除する
				if ($content['Content']['alias_id'] == $data['Content']['id']) {
					$softDelete = $this->softDelete(null);
					$this->softDelete(false);
					$this->removeFromTree($content['Content']['id'], true);
					$this->softDelete($softDelete);
				} elseif ($content['Content']['type'] == 'ContentFolder') {
					$this->updateChildren($content['Content']['id']);
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
	public function updateRelateSubSiteContent($data)
	{
		// 他のデータを更新する為、一旦退避
		$dataTmp = $this->data;
		$idTmp = $this->id;
		// 自身がエイリアスか確認し、エイリアスの場合は終了
		if (!empty($data['Content']['alias_id']) || !isset($data['Content']['site_id'])) {
			return true;
		}

		$isContentFolder = false;
		if (!empty($data['Content']['type']) && $data['Content']['type'] == 'ContentFolder') {
			$isContentFolder = true;
		}

		// メインサイトか確認し、メインサイトでない場合は終了
		if (!$this->Site->isMain($data['Content']['site_id'])) {
			return true;
		}
		// 連携設定となっている小サイトを取得
		$sites = $this->Site->find('all', ['conditions' => ['Site.main_site_id' => $data['Content']['site_id'], 'relate_main_site' => true]]);
		if (!$sites) {
			return true;
		}

		$_data = $this->find('first', ['conditions' => ['Content.id' => $data['Content']['id']], 'recursive' => -1]);
		if ($_data) {
			$data = ['Content' => array_merge($_data['Content'], $data['Content'])];
		}

		// URLが空の場合はゴミ箱へ移動する処理の為、連携更新を行わない
		if (!$data['Content']['url']) {
			return true;
		}

		$CreateModel = $this;
		if ($isContentFolder) {
			$CreateModel = ClassRegistry::init('ContentFolder');
		}

		$pureUrl = $this->pureUrl($data['Content']['url'], $data['Content']['site_id']);
		// 同階層に同名のコンテンツがあるか確認
		$result = true;
		foreach($sites as $site) {
			if (!$site['Site']['status']) {
				continue;
			}
			$url = $pureUrl;
			$prefix = $this->Site->getPrefix($site);
			if ($prefix) {
				$url = '/' . $prefix . $url;
			}
			$content = $this->find('first', ['conditions' => [
				'Content.site_id' => $site['Site']['id'],
				'or' => [
					'Content.main_site_content_id' => $data['Content']['id'],
					'Content.url' => $url
				]
			], 'recursive' => -1]);
			if ($content) {
				// 存在する場合は、自身のエイリアスかどうか確認し、エイリアスの場合は、公開状態とタイトル、説明文、アイキャッチ、更新日を更新
				// フォルダの場合も更新する
				if ($content['Content']['alias_id'] == $data['Content']['id'] || ($content['Content']['type'] == 'ContentFolder' && $isContentFolder)) {
					$content['Content']['name'] = urldecode($data['Content']['name']);
					$content['Content']['title'] = $data['Content']['title'];
					$content['Content']['description'] = $data['Content']['description'];
					$content['Content']['self_status'] = $data['Content']['self_status'];
					$content['Content']['self_publish_begin'] = $data['Content']['self_publish_begin'];
					$content['Content']['self_publish_end'] = $data['Content']['self_publish_end'];
					$content['Content']['created_date'] = $data['Content']['created_date'];
					$content['Content']['modified_date'] = $data['Content']['modified_date'];
					$content['Content']['exclude_search'] = $data['Content']['exclude_search'];
					if (!empty($data['Content']['eyecatch'])) {
						$content['Content']['eyecatch'] = $data['Content']['eyecatch'];
					}
					$url = $data['Content']['url'];
					if ($content['Content']['type'] == 'ContentFolder') {
						$url = preg_replace('/\/[^\/]+\/$/', '/', $url);
					}
					$content['Content']['parent_id'] = $this->copyContentFolderPath($url, $site['Site']['id']);
				} else {
					$content['Content']['name'] = urldecode($data['Content']['name']);
				}
				if (!$this->save($content)) {
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
				if ($content['Content']['type'] == 'ContentFolder') {
					$url = preg_replace('/\/[^\/]+\/$/', '/', $url);
					unset($content['Content']['entity_id']);
				} else {
					$content['Content']['alias_id'] = $data['Content']['id'];
				}
				$content['Content']['parent_id'] = $this->copyContentFolderPath($url, $site['Site']['id']);
				$CreateModel->create($content);
				if (!$CreateModel->save()) {
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
	public function pureUrl($url, $siteId)
	{
		$site = BcSite::findById($siteId);
		return $site->getPureUrl($url);
	}

	/**
	 * Content data を作成して保存する
	 *
	 * @param array $content
	 * @param string $plugin
	 * @param string $type
	 * @param int $entityId
	 */
	public function createContent($content, $plugin, $type, $entityId = null, $validate = true)
	{
		if (isset($content['Content'])) {
			$content = $content['Content'];
		}
		$content['plugin'] = $plugin;
		$content['type'] = $type;
		$content['entity_id'] = $entityId;
		if (!isset($content['deleted'])) {
			$content['deleted'] = false;
		}
		if (!isset($content['site_root'])) {
			$content['site_root'] = 0;
		}
		if (!isset($content['exclude_search'])) {
			$content['exclude_search'] = 0;
		}
		if (!isset($content['created_date'])) {
			$content['created_date'] = date('Y-m-d H:i:s');
		}
		$this->create($content);
		return $this->save(null, $validate);
	}

	/**
	 * コンテンツデータよりURLを生成する
	 *
	 * @param int $id コンテンツID
	 * @param string $plugin プラグイン
	 * @param string $type タイプ
	 * @return mixed URL | false
	 */
	public function createUrl($id, $plugin = null, $type = null)
	{
		$id = (int)$id;
		// @deprecated 5.0.0 since 4.0.2 $plugin / $type の引数は不要
		if (!$id) {
			return false;
		} elseif ($id == 1) {
			$url = '/';
		} else {
			// =========================================================================================================
			// サイト全体のURLを変更する場合、TreeBehavior::getPath() を利用するとかなりの時間がかかる為、DataSource::query() を利用する
			// 2018/02/04 ryuring
			// プリペアドステートメントを利用する為、fetchAll() を利用しようとしたが、SQLite のドライバが対応してない様子。
			// CakePHP３系に対応する際、SQLite を標準のドライバに変更してから、プリペアドステートメントに書き換えていく。
			// それまでは、SQLインジェクション対策として、値をチェックしてから利用する。
			// =========================================================================================================
			$db = $this->getDataSource();
			$sql = "SELECT lft, rght FROM {$this->tablePrefix}contents AS Content WHERE id = {$id} AND deleted = " . $db->value(false, 'boolean');
			$content = $db->query($sql, false);
			if (!$content) {
				return false;
			}
			if (isset($content[0]['Content'])) {
				$content = $content[0]['Content'];
			} else {
				$content = $content[0][0];
			}
			$sql = "SELECT name, plugin, type FROM {$this->tablePrefix}contents AS Content " .
				"WHERE lft <= {$db->value($content['lft'], 'integer')} AND rght >= {$db->value($content['rght'], 'integer')} AND deleted =  " . $db->value(false, 'boolean') . " " .
				"ORDER BY lft ASC";
			$parents = $db->query($sql, false);
			unset($parents[0]);
			if (!$parents) {
				return false;
			}
			$names = [];
			$content = null;
			foreach($parents as $parent) {
				if (isset($parent['Content'])) {
					$parent = $parent['Content'];
				} else {
					$parent = $parent[0];
				}
				$names[] = $parent['name'];
				$content = $parent;
			}
			$plugin = $content['plugin'];
			$type = $content['type'];
			$url = '/' . implode('/', $names);
			$setting = $omitViewAction = Configure::read('BcContents.items.' . $plugin . '.' . $type);
			if ($type == 'ContentFolder' || empty($setting['omitViewAction'])) {
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
	public function updateSystemData($data)
	{
		if (empty($data['Content']['name'])) {
			if ($data['Content']['id'] != 1) {
				return false;
			}
		}

		$site = $this->Site->find('first', ['conditions' => ['Site.id' => $data['Content']['site_id']]]);

		// URLを更新
		$data['Content']['url'] = $this->createUrl($data['Content']['id'], $data['Content']['plugin'], $data['Content']['type']);

		// 親フォルダの公開状態に合わせて公開状態を更新（自身も含める）
		if (isset($data['Content']['self_status'])) {
			$data['Content']['status'] = $data['Content']['self_status'];
		}
		// null の場合、isset で判定できないので array_key_exists を利用
		if (array_key_exists('self_publish_begin', $data['Content'])) {
			$data['Content']['publish_begin'] = $data['Content']['self_publish_begin'];
		}
		if (array_key_exists('self_publish_end', $data['Content'])) {
			$data['Content']['publish_end'] = $data['Content']['self_publish_end'];
		}
		if (!empty($data['Content']['parent_id'])) {
			$parent = $this->find('first', [
				'fields' => ['name', 'status', 'publish_begin', 'publish_end'],
				'conditions' => ['Content.id' => $data['Content']['parent_id']],
				'recursive' => -1
			]);
			// 親フォルダが非公開の場合は自身も非公開
			if (!$parent['Content']['status']) {
				$data['Content']['status'] = $parent['Content']['status'];
			}
			// 親フォルダに公開期間が設定されている場合は自身の公開期間を上書き
			if ($parent['Content']['publish_begin'] || $parent['Content']['publish_end']) {
				$data['Content']['publish_begin'] = $parent['Content']['publish_begin'];
				$data['Content']['publish_end'] = $parent['Content']['publish_end'];
			}
		}

		// 主サイトの関連コンテンツIDを更新
		if ($site) {
			// 主サイトの同一階層のコンテンツを特定
			$prefix = $site['Site']['name'];
			if ($site['Site']['alias']) {
				$prefix = $site['Site']['alias'];
			}
			$url = preg_replace('/^\/' . preg_quote($prefix, '/') . '\//', '/', $data['Content']['url']);
			$mainSitePrefix = $this->Site->getPrefix($site['Site']['main_site_id']);
			if ($mainSitePrefix) {
				$url = '/' . $mainSitePrefix . $url;
			}
			$mainSiteContentId = $this->field('id', [
				'site_id' => $site['Site']['main_site_id'],
				'url' => $url
			]);
			// main_site_content_id を更新
			if ($mainSiteContentId) {
				$data['Content']['main_site_content_id'] = $mainSiteContentId;
			} else {
				$data['Content']['main_site_content_id'] = null;
			}
		}
		$data = $this->save($data, ['validate' => false, 'callbacks' => false]);
		$this->data = $data;
		return (bool)($data);
	}

	/**
	 * ID を指定して公開状態かどうか判定する
	 *
	 * @param $id
	 * @return bool
	 */
	public function isPublishById($id)
	{
		$conditions = array_merge(['Content.id' => $id], $this->getConditionAllowPublish());
		return (bool)$this->find('first', ['conditions' => $conditions, 'recursive' => -1]);
	}

	/**
	 * 子ノードのURLを全て更新する
	 *
	 * @param $id
	 * @return bool
	 */
	public function updateChildren($id)
	{
		// 他のデータを更新する為一旦退避
		$dataTmp = $this->data;
		$idTmp = $this->id;
		$children = $this->children($id, false, null, 'Content.lft');
		$result = true;
		if ($children) {
			foreach($children as $child) {
				if (!$this->updateSystemData($child)) {
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
	public function findByType($type, $entityId = null)
	{
		list($plugin, $type) = pluginSplit($type);
		if (!$plugin) {
			$plugin = 'Core';
		}
		$conditions = [
			'Content.plugin' => $plugin,
			'Content.type' => $type,
			'Content.alias_id' => null
		];
		if ($entityId) {
			$conditions['Content.entity_id'] = $entityId;
		}
		return $this->find('first', ['conditions' => $conditions, 'order' => ['Content.id']]);
	}

	/**
	 * コンテンツフォルダーのリストを取得
	 * コンボボックス用
	 *
	 * @param int $siteId
	 * @param array $options
	 * @return array|bool
	 */
	public function getContentFolderList($siteId = null, $options = [])
	{
		$options = array_merge([
			'excludeId' => null
		], $options);

		$conditions = [
			'type' => 'ContentFolder',
			'alias_id' => null
		];
		if (!is_null($siteId)) {
			$conditions['site_id'] = $siteId;
		}
		if ($options['excludeId']) {
			$conditions['id <>'] = $options['excludeId'];
		}
		if (!empty($options['conditions'])) {
			$conditions = array_merge($conditions, $options['conditions']);
		}
		$folders = $this->generateTreeList($conditions);
		if ($folders) {
			return $this->convertTreeList($folders);
		}
		return false;
	}

	/**
	 * ツリー構造のデータを コンボボックスのデータ用に変換する
	 * @param $nodes
	 * @return array
	 */
	public function convertTreeList($nodes)
	{
		if (!$nodes) {
			return [];
		}
		foreach($nodes as $key => $value) {
			if (preg_match("/^([_]+)/i", $value, $matches)) {
				$value = preg_replace("/^[_]+/i", '', $value);
				$prefix = str_replace('_', '　　　', $matches[1]);
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
	public function softDeleteFromTree($id)
	{
		$this->softDelete(true);
		$this->Behaviors->unload('BcCache');
		$this->Behaviors->unload('BcUpload');
		$result = $this->deleteRecursive($id);
		$this->Behaviors->load('BcCache');
		$this->Behaviors->load('BcUpload');
		$this->delAssockCache();
		return $result;
	}

	/**
	 * 再帰的に削除
	 *
	 * エイリアスの場合
	 *
	 * @param $id
	 * @return bool
	 */
	public function deleteRecursive($id)
	{
		if (!$id) {
			return false;
		}
		$children = $this->children($id, true);
		$result = true;
		if ($children) {
			foreach($children as $child) {
				if (!$this->deleteRecursive($child['Content']['id'])) {
					$result = false;
				}
			}
		}
		if ($result) {
			$content = $this->find('first', [
				'conditions' => ['Content.id' => $id],
				'recursive' => -1
			]);
			if (empty($content['Content']['alias_id'])) {
				// エイリアス以外の場合
				// 一旦階層構造から除外しリセットしてゴミ箱に移動（論理削除）
				$content['Content']['parent_id'] = null;
				$content['Content']['url'] = '';
				$content['Content']['status'] = false;
				$content['Content']['self_status'] = false;
				unset($content['Content']['lft']);
				unset($content['Content']['rght']);
				$this->updatingSystemData = false;
				// ここでは callbacks を false にすると lft rght が更新されないので callbacks は true に設定する（default: true）
				$this->clear();
				$this->save($content, ['validate' => false]);
				$this->updatingSystemData = true;
				$result = $this->delete($id);
				// =====================================================================
				// 通常の削除の際、afterDelete で、関連コンテンツのキャッシュを削除しているが、
				// 論理削除の場合、afterDelete が呼ばれない為、ここで削除する
				// =====================================================================
				$this->deleteAssocCache($content);
				return $result;
			} else {
				// エイリアスの場合、直接削除
				$softDelete = $this->softDelete(null);
				$this->softDelete(false);
				$result = $this->removeFromTree($content['Content']['id'], true);
				$this->softDelete($softDelete);
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
	public function trashReturn($id)
	{
		return $this->trashReturnRecursive($id, true);
	}

	/**
	 * 再帰的にゴミ箱より元に戻す
	 *
	 * @param $id
	 * @return bool|int
	 */
	public function trashReturnRecursive($id, $top = false)
	{
		$this->softDelete(false);
		$children = $this->children($id, true);
		$this->softDelete(true);
		$result = true;
		if ($children) {
			foreach($children as $child) {
				if (!$this->trashReturnRecursive($child['Content']['id'])) {
					$result = false;
				}
			}
		}
		$this->Behaviors->unload('Tree');
		$this->updatingRelated = false;
		if ($result && $this->undelete($id)) {
			$this->Behaviors->load('Tree');
			$this->updatingRelated = true;
			$content = $this->find('first', ['conditions' => ['Content.id' => $id], 'recursive' => -1]);
			if ($top) {
				$siteRootId = $this->field('id', ['Content.site_id' => $content['Content']['site_id'], 'site_root' => true]);
				$content['Content']['parent_id'] = $siteRootId;
			}
			unset($content['Content']['lft']);
			unset($content['Content']['rght']);
			if ($this->save($content, true)) {
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
	public function deleteByType($type, $entityId = null)
	{
		list($plugin, $type) = pluginSplit($type);
		if (!$plugin) {
			$plugin = 'Core';
		}
		$conditions = [
			'plugin' => $plugin,
			'type' => $type,
			'alias_id' => null
		];
		if ($entityId) {
			$conditions['Content.entity_id'] = $entityId;
		}
		$softDelete = $this->softDelete(null);
		$this->softDelete(false);
		$id = $this->field('id', $conditions);
		$result = $this->removeFromTree($id, true);
		$this->softDelete($softDelete);
		return $result;
	}

	/**
	 * コンテンツIDよりURLを取得する
	 *
	 * @param int $id
	 * @return string URL
	 */
	public function getUrlById($id, $full = false)
	{
		if (!is_int($id)) {
			$id = (int)$id;
			if ($id === 0) {
				return '';
			}
		}
		$data = $this->find('first', ['conditions' => ['Content.id' => $id]]);
		if ($data) {
			return $this->getUrl($data['Content']['url'], $full, $data['Site']['use_subdomain']);
		}
		return '';
	}

	/**
	 * コンテンツ管理上のURLを元に正式なURLを取得する
	 *
	 * ドメインからのフルパスでない場合、デフォルトでは、
	 * サブフォルダ設置時等の baseUrl（サブフォルダまでのパス）は含まない
	 *
	 * @param string $url コンテンツ管理上のURL
	 * @param bool $full http からのフルのURLかどうか
	 * @param bool $useSubDomain サブドメインを利用しているかどうか
	 * @param bool $base $full が false の場合、ベースとなるURLを含めるかどうか
	 * @return string URL
	 */
	public function getUrl($url, $full = false, $useSubDomain = false, $base = false)
	{
		if ($useSubDomain && !is_array($url)) {
			$subDomain = '';
			$site = BcSite::findByUrl($url);
			$originUrl = $url;
			if ($site) {
				$subDomain = $site->alias;
				$originUrl = preg_replace('/^\/' . preg_quote($site->alias, '/') . '\//', '/', $url);
			}
			if ($full) {
				if ($site) {
					$fullUrl = topLevelUrl(false) . $originUrl;
					if ($site->domainType == 1) {
						$mainDomain = BcUtil::getMainDomain();
						$fullUrlArray = explode('//', $fullUrl);
						$fullPassArray = explode('/', $fullUrlArray[1]);
						unset($fullPassArray[0]);
						$url = $fullUrlArray[0] . '//' . $subDomain . '.' . $mainDomain . '/' . implode('/', $fullPassArray);
					} elseif ($site->domainType == 2) {
						$fullUrlArray = explode('//', $fullUrl);
						$urlArray = explode('/', $fullUrlArray[1]);
						unset($urlArray[0]);
						if ($site->sameMainUrl) {
							$mainSite = BcSite::findById($site->mainSiteId);
							$subDomain = $mainSite->alias;
						}
						$url = $fullUrlArray[0] . '//' . $subDomain . '/' . implode('/', $urlArray);
					}
				} else {
					$url = preg_replace('/\/$/', '', Configure::read('BcEnv.siteUrl')) . $originUrl;
				}
			} else {
				$url = $originUrl;
			}
		} else {
			if (BC_INSTALLED) {
				if (!is_array($url)) {
					$site = BcSite::findByUrl($url);
					if ($site && $site->sameMainUrl) {
						$mainSite = BcSite::findById($site->mainSiteId);
						$alias = $mainSite->alias;
						if ($alias) {
							$alias = '/' . $alias;
						}
						$url = $alias . $site->getPureUrl($url);
					}
				}
			}
			if ($full) {
				$mainDomain = BcUtil::getMainDomain();
				$fullUrlArray = explode('//', Configure::read('BcEnv.siteUrl'));
				$siteDomain = preg_replace('/\/$/', '', $fullUrlArray[1]);
				if (preg_match('/^www\./', $siteDomain) && str_replace('www.', '', $siteDomain) === $mainDomain) {
					$mainDomain = $siteDomain;
				}
				$url = $fullUrlArray[0] . '//' . $mainDomain . Router::url($url);
			}
		}
		$url = preg_replace('/\/index$/', '/', $url);
		if (!$full && $base) {
			$url = Router::url($url);
		}
		return $url;
	}

	/**
	 * 現在のフォルダのURLを元に別サイトにフォルダを生成する
	 * 最下層のIDを返却する
	 *
	 * @param $currentUrl
	 * @param $targetSiteId
	 * @return bool|null
	 */
	public function copyContentFolderPath($currentUrl, $targetSiteId)
	{
		$currentId = $this->field('id', ['Content.url' => $currentUrl]);
		if (!$currentId) {
			return false;
		}
		$prefix = $this->Site->getPrefix($targetSiteId);
		$path = $this->getPath($currentId, null, -1);
		if (!$path) {
			return false;
		}
		$url = '/';
		if ($prefix) {
			$url .= $prefix . '/';
		}
		unset($path[0]);
		$parentId = $this->Site->getRootContentId($targetSiteId);
		/* @var ContentFolder $ContentFolder */
		$ContentFolder = ClassRegistry::init('ContentFolder');
		foreach($path as $currentContentFolder) {
			if ($currentContentFolder['Content']['type'] != 'ContentFolder') {
				break;
			}
			if ($currentContentFolder['Content']['site_root']) {
				continue;
			}
			$url .= $currentContentFolder['Content']['name'];
			if ($this->find('first', ['conditions' => ['Content.url' => $url], 'recursive' => -1])) {
				return false;
			}
			$url .= '/';
			$targetContentFolder = $this->find('first', ['conditions' => ['Content.url' => $url], 'recursive' => -1]);
			if ($targetContentFolder) {
				$parentId = $targetContentFolder['Content']['id'];
			} else {
				$data = [
					'Content' => [
						'name' => $currentContentFolder['Content']['name'],
						'title' => $currentContentFolder['Content']['title'],
						'parent_id' => $parentId,
						'plugin' => 'Core',
						'type' => 'ContentFolder',
						'site_id' => $targetSiteId,
						'self_status' => true
					]
				];
				$ContentFolder->create($data);
				if ($ContentFolder->save()) {
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
	public function copy($id, $entityId, $newTitle, $newAuthorId, $newSiteId = null)
	{

		$data = $this->find('first', ['conditions' => ['Content.id' => $id]]);
		$url = $data['Content']['url'];
		if (!is_null($newSiteId) && $data['Site']['id'] != $newSiteId) {
			$data['Content']['site_id'] = $newSiteId;
			$data['Content']['parent_id'] = $this->copyContentFolderPath($url, $newSiteId);
		}
		unset($data['Content']['id']);
		unset($data['Content']['modified_date']);
		unset($data['Content']['created']);
		unset($data['Content']['modified']);
		unset($data['Content']['main_site_content']);
		if ($newTitle) {
			$data['Content']['title'] = $newTitle;
		} else {
			$data['Content']['title'] = sprintf(__d('baser', '%s のコピー'), $data['Content']['title']);
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
	public function getConditionAllowPublish()
	{
		$conditions[$this->alias . '.status'] = true;
		$conditions[] = ['or' => [[$this->alias . '.publish_begin <=' => date('Y-m-d H:i:s')],
			[$this->alias . '.publish_begin' => null]]];
		$conditions[] = ['or' => [[$this->alias . '.publish_end >=' => date('Y-m-d H:i:s')],
			[$this->alias . '.publish_end' => null]]];
		return $conditions;
	}

	/**
	 * 公開状態を取得する
	 *
	 * @param array $data コンテンツデータ
	 * @return boolean 公開状態
	 */
	public function isAllowPublish($data, $self = false)
	{

		if (isset($data['Content'])) {
			$data = $data['Content'];
		}

		$fields = [
			'status' => 'status',
			'publish_begin' => 'publish_begin',
			'publish_end' => 'publish_end'
		];
		if ($self) {
			foreach($fields as $key => $field) {
				$fields[$key] = 'self_' . $field;
			}
		}

		$allowPublish = (int)$data[$fields['status']];

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
	public function existsContentByUrl($url)
	{
		return (bool) $this->find('count', ['conditions' => ['Content.url' => $url]]);
	}

	/**
	 * 公開されたURLが存在するか確認する
	 *
	 * @param string $url
	 * @return bool
	 * @deprecated 5.0.0 since 4.2.2 Content::findByUrl() を利用してください。
	 */
	public function existsPublishUrl($url)
	{
		trigger_error(deprecatedMessage('メソッド：Content::existsPublishUrl()', '4.2.2', '5.0.0', 'Content::findByUrl() を利用してください。'), E_USER_DEPRECATED);
		$conditions = $this->getConditionAllowPublish();
		$conditions['url'] = $url;
		return (boolean)$this->find('count', ['conditions' => $conditions]);
	}

	/**
	 * データが公開済みかどうかチェックする
	 *
	 * @param boolean $status 公開ステータス
	 * @param string $publishBegin 公開開始日時
	 * @param string $publishEnd 公開終了日時
	 * @return    bool
	 */
	public function isPublish($status, $publishBegin, $publishEnd)
	{
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
	 * 公開されたURLを取得
	 *
	 * @param array $content
	 * @return string|bool
	 */
	public function getPublishUrl($content)
	{
		if (!$this->isPublish($content['status'], $content['publish_begin'], $content['publish_end'])) {
			return false;
		}
		$site = BcSite::findById($content['site_id']);
		return $this->getUrl($content['url'], true, $site->useSubDomain);
	}

	/**
	 * 移動元のコンテンツと移動先のディレクトリから移動が可能かチェックする
	 *
	 * @param $currentId int 移動元コンテンツID
	 * @param $targetParentId int 移動先コンテンツID (ContentFolder)
	 * @return bool
	 */
	public function isMovable($currentId, $targetParentId)
	{
		$currentContent = $this->find('first', [
			'conditions' => ['id' => $currentId],
			'recursive' => -1
		]);
		if ($currentContent['Content']['parent_id'] === $targetParentId) {
			return true;
		}
		$parentCuntent = $this->find('first', [
			'conditions' => ['id' => $targetParentId],
			'recursive' => -1
		]);

		// 指定コンテンツが存在しない
		if (!$currentContent || !$parentCuntent) {
			return false;
		}

		$parentId[] = $parentCuntent['Content']['id'];

		// 関連コンテンツで移動先と同じ階層のフォルダを確認
		$childrenSite = $this->Site->children($currentContent['Content']['site_id'], [
			'conditions' => ['relate_main_site' => true]
		]);
		if ($childrenSite) {
			$pureUrl = $this->pureUrl($parentCuntent['Content']['url'], $parentCuntent['Content']['site_id']);
			foreach($childrenSite as $site) {
				$site = BcSite::findById($site['Site']['id']);
				$url = $site->makeUrl(new CakeRequest($pureUrl));
				$id = $this->field('id', ['url' => $url]);
				if ($id) {
					$parentId[] = $id;
				}
			}
		}

		// 移動先に同一コンテンツが存在するか確認
		$movedContent = $this->find('first', [
			'conditions' => [
				'parent_id' => $parentId,
				'name' => $currentContent['Content']['name'],
				'id !=' => $currentContent['Content']['id']
			],
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
	public function isChangedStatus($id, $newData)
	{
		$before = $this->find('first', ['conditions' => ['Content.id' => $id]]);
		if (!$before) {
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
	public function getSiteRoot($siteId)
	{
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
	public function getParentTemplate($id)
	{
		$contents = $this->getPath($id, null, -1);
		$contents = array_reverse($contents);
		unset($contents[0]);
		$parentTemplates = Hash::extract($contents, '{n}.Content.layout_template');
		$parentTemplate = '';
		foreach($parentTemplates as $parentTemplate) {
			if ($parentTemplate) {
				break;
			}
		}
		if (!$parentTemplate) {
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
	public function move($currentId, $currentParentId, $targetSiteId, $targetParentId, $targetId)
	{
		$this->moveRelateSubSiteContent($currentId, $targetParentId, $targetId);
		$targetSort = $this->getOrderSameParent($targetId, $targetParentId);
		if ($currentParentId != $targetParentId) {
			$data = $this->find('first', [
				'conditions' => ['Content.id' => $currentId],
				'recursive' => -1
			]);
			// 親を変更
			$data = $this->save(['Content' => [
				'id' => $currentId,
				'name' => $data['Content']['name'],
				'title' => $data['Content']['title'],
				'plugin' => $data['Content']['plugin'],
				'type' => $data['Content']['type'],
				'parent_id' => $targetParentId,
				'site_id' => $targetSiteId
			]], false);
			// フォルダにコンテンツがない場合、targetId が空で一番後を指定の場合は、親を変更して終了
			if (!$targetSort || !$targetId) {
				return $data;
			}
			$currentSort = $this->getOrderSameParent(null, $targetParentId);
		} else {
			$currentSort = $this->getOrderSameParent($currentId, $targetParentId);
		}
		// 親変更後のオフセットを取得
		$offset = $targetSort - $currentSort;
		if ($currentParentId == $targetParentId && $targetId && $offset > 0) {
			$offset--;
		}
		// オフセットを元に移動
		return $this->moveOffset($currentId, $offset);
	}

	/**
	 * メインサイトの場合、連携設定がされている子サイトも移動する
	 *
	 * @param $data
	 */
	public function moveRelateSubSiteContent($mainCurrentId, $mainTargetParentId, $mainTargetId)
	{
		// 他のデータを更新する為、一旦退避
		$dataTmp = $this->data;
		$idTmp = $this->id;
		$data = $this->find('first', ['conditions' => ['Content.id' => $mainCurrentId], 'recursive' => -1]);
		// 自身がエイリアスか確認し、エイリアスの場合は終了
		if (!empty($data['Content']['alias_id']) || !isset($data['Content']['site_id']) || !isset($data['Content']['type'])) {
			return true;
		}
		// メインサイトか確認し、メインサイトでない場合は終了
		if (!$this->Site->isMain($data['Content']['site_id'])) {
			return true;
		}
		// 連携設定となっている小サイトを取得
		$sites = $this->Site->find('all', ['conditions' => ['Site.main_site_id' => $data['Content']['site_id'], 'relate_main_site' => true]]);
		if (!$sites) {
			return true;
		}
		$result = true;
		foreach($sites as $site) {
			// 自信をメインコンテンツとしているデータを取得
			$current = $this->find('first', ['conditions' => ['Content.main_site_content_id' => $mainCurrentId, 'Content.site_id' => $site['Site']['id']], 'recursive' => -1]);
			if (!$current) {
				continue;
			}
			$currentId = $current['Content']['id'];
			$currentParentId = $current['Content']['parent_id'];
			$target = null;
			$targetId = "";
			$targetParentId = "";
			if ($mainTargetId) {
				$target = $this->find('first', ['conditions' => ['Content.main_site_content_id' => $mainTargetId, 'Content.site_id' => $site['Site']['id']], 'recursive' => -1]);
				if ($target) {
					$targetId = $target['Content']['id'];
					$targetParentId = $target['Content']['parent_id'];
				}
			}
			if (!$target) {
				// ターゲットが見つからない場合は親IDより取得
				$target = $this->find('first', ['conditions' => ['Content.main_site_content_id' => $mainTargetParentId, 'Content.site_id' => $site['Site']['id']], 'recursive' => -1]);
				if ($target) {
					$targetParentId = $target['Content']['id'];
				}
			}
			if (!$target) {
				continue;
			}
			$targetSiteId = $target['Content']['site_id'];
			if (!$this->move($currentId, $currentParentId, $targetSiteId, $targetParentId, $targetId)) {
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
	public function moveOffset($id, $offset)
	{
		$offset = (int)$offset;
		if ($offset > 0) {
			$result = $this->moveDown($id, abs($offset));
		} elseif ($offset < 0) {
			$result = $this->moveUp($id, abs($offset));
		} else {
			$result = true;
		}
		if ($result) {
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
	public function getOrderSameParent($id, $parentId)
	{
		$contents = $this->find('all', [
			'fields' => ['Content.id', 'Content.parent_id', 'Content.title'],
			'order' => 'lft',
			'conditions' => ['Content.parent_id' => $parentId],
			'recursive' => -1
		]);
		$order = null;
		if ($contents) {
			if ($id) {
				foreach($contents as $key => $data) {
					if ($id == $data['Content']['id']) {
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
	public function getRelatedSiteContents($id, $options = [])
	{
		$options = array_merge([
			'excludeIds' => []
		], $options);
		$conditions = [
			['OR' => [
				['Content.id' => $id],
				['Content.main_site_content_id' => $id]
			]],
			['OR' => [
				['Site.status' => true],
				['Site.status' => null]    // ルートメインサイト
			]]
		];
		if ($options['excludeIds']) {
			if (count($options['excludeIds']) == 1) {
				$options['excludeIds'] = $options['excludeIds'][0];
			}
			$conditions['Content.site_id <>'] = $options['excludeIds'];
		}
		$conditions = array_merge($conditions, $this->getConditionAllowPublish());
		$contents = $this->find('all', [
			'conditions' => $conditions,
			'order' => ['Content.id'],
			'recursive' => 0
		]);
		$mainSite = $this->Site->getRootMain();
		foreach($contents as $key => $content) {
			if ($content['Content']['site_id'] == 0) {
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
	public function getCacheTime($data)
	{
		if (!is_array($data)) {
			$data = $this->find('first', ['conditions' => ['Content.id' => $data], 'recursive' => 0]);
		}
		if (isset($data['Content'])) {
			$data = $data['Content'];
		}
		if (!$data) {
			return false;
		}
		// #10680 Modify 2016/01/22 gondoh
		// 3.0.10 で追加されたViewキャッシュ分離の設定値を、後方互換のため存在しない場合は旧情報で取り込む
		$duration = Configure::read('BcCache.viewDuration');
		if (empty($duration)) {
			$duration = Configure::read('BcCache.duration');
		}
		// 固定ページなどの公開期限がviewDulationより短い場合
		if ($data['status'] && $data['publish_end'] && $data['publish_end'] != '0000-00-00 00:00:00') {
			if (strtotime($duration) - time() > (strtotime($data['publish_end']) - time())) {
				$duration = strtotime($data['publish_end']) - time();
			}
		}
		return $duration;
	}

	/**
	 * 全てのURLをデータの状況に合わせ更新する
	 *
	 * @return bool
	 */
	public function updateAllUrl()
	{
		$contents = $this->find('all', [
			'recursive' => -1,
			'order' => ['Content.lft']
		]);
		$result = true;
		$updatingRelated = $this->updatingRelated;
		$updatingSystemData = $this->updatingSystemData;
		$this->updatingRelated = false;
		$this->updatingSystemData = false;
		foreach($contents as $content) {
			$content['Content']['url'] = $this->createUrl($content['Content']['id'], $content['Content']['plugin'], $content['Content']['type']);
			if (!$this->save($content)) {
				$result = false;
			}
		}
		$this->updatingRelated = $updatingRelated;
		$this->updatingSystemData = $updatingSystemData;
		return $result;
	}

	/**
	 * 指定したコンテンツ配下のコンテンツのURLを一括更新する
	 * @param $id
	 */
	public function updateChildrenUrl($id)
	{
		set_time_limit(0);
		$children = $this->children($id, false, ['url', 'id'], 'Content.lft', null, null, -1);
		$db = $this->getDataSource();
		if ($children) {
			foreach($children as $key => $child) {
				// サイト全体を更新する為、サイト規模によってはかなり時間がかかる為、SQLを利用
				$sql = 'UPDATE ' . $this->tablePrefix . 'contents SET url = ' . $db->value($this->createUrl($child['Content']['id']), 'integer') . ' WHERE id = ' . $db->value($child['Content']['id'], 'integer');
				if (!$db->execute($sql)) {
					$this->getDataSource()->rollback();
					return false;
				}
			}
		}
		$this->getDataSource()->commit();
		return true;
	}

	/**
	 * コンテンツ管理のツリー構造をリセットする
	 */
	public function resetTree()
	{
		$this->Behaviors->unload('Tree');
		$this->updatingRelated = false;
		$siteRoots = $this->find('all', ['conditions' => ['Content.site_root' => true], 'order' => 'lft', 'recursive' => -1]);
		$count = 0;
		$mainSite = [];
		foreach($siteRoots as $siteRoot) {
			$count++;
			$siteRoot['Content']['lft'] = $count;
			$siteRoot['Content']['level'] = ($siteRoot['Content']['id'] == 1)? 0 : 1;
			$siteRoot['Content']['parent_id'] = ($siteRoot['Content']['id'] == 1)? null : 1;
			$contents = $this->find('all', ['conditions' => ['Content.site_id' => $siteRoot['Content']['site_id'], 'Content.site_root' => false], 'order' => 'lft', 'recursive' => -1]);
			if ($contents) {
				foreach($contents as $content) {
					$count++;
					$content['Content']['lft'] = $count;
					$count++;
					$content['Content']['rght'] = $count;
					$content['Content']['level'] = $siteRoot['Content']['level'] + 1;
					$content['Content']['parent_id'] = $siteRoot['Content']['id'];
					$this->save($content, false);
				}
			}
			if ($siteRoot['Content']['id'] == 1) {
				$mainSite = $siteRoot;
			} else {
				$count++;
				$siteRoot['Content']['rght'] = $count;
				$this->save($siteRoot, false);
			}
		}
		$count++;
		$mainSite['Content']['rght'] = $count;
		$this->save($mainSite, false);
		// ゴミ箱
		$this->Behaviors->unload('SoftDelete');
		$contents = $this->find('all', ['conditions' => ['Content.deleted' => true], 'order' => 'lft', 'recursive' => -1]);
		if ($contents) {
			foreach($contents as $content) {
				$count++;
				$content['Content']['lft'] = $count;
				$count++;
				$content['Content']['rght'] = $count;
				$content['Content']['level'] = 0;
				$content['Content']['parent_id'] = null;
				$content['Content']['site_id'] = null;
				$this->save($content, false);
			}
		}
		// 関連データ更新機能をオンにした状態で再度更新
		$this->Behaviors->load('Tree');
		$this->updatingRelated = true;
		$contents = $this->find('all', ['order' => 'lft', 'recursive' => -1]);
		if ($contents) {
			foreach($contents as $content) {
				// バリデーションをオンにする事で同名コンテンツを強制的にリネームする
				// beforeValidate でリネーム処理を入れている為
				// （第二引数を false に設定しない）
				$this->save($content);
			}
		}
		return true;
	}

	/**
	 * URLに関連するコンテンツ情報を取得する
	 *
	 * @param string $url 検索対象のURL
	 * @param bool $publish 公開状態かどうか
	 * @param bool $extend 拡張URLに対応するかどうか /news/ というコンテンツが存在する場合、/news/archives/1 で検索した際にヒットさせる
	 * @param bool $sameUrl 対象をメインサイトと同一URLで表示するサイト設定内のコンテンツするかどうか
	 * @param bool $useSubDomain 対象をサブドメインを利用しているサイト設定内のコンテンツをするかどうか
	 * @return mixed false|array Content データ
	 */
	public function findByUrl($url, $publish = true, $extend = false, $sameUrl = false, $useSubDomain = false)
	{
		$url = preg_replace('/^\//', '', $url);
		if ($extend) {
			$params = explode('/', $url);
			$condUrls = [];
			$condUrls[] = '/' . implode('/', $params);
			$count = count($params);
			for($i = $count; $i > 1; $i--) {
				unset($params[$i - 1]);
				$path = implode('/', $params);
				$condUrls[] = '/' . $path . '/';
				$condUrls[] = '/' . $path;
			}
			// 固定ページはURL拡張はしない
			$conditions = [
				'Content.type <>' => 'Page',
				'Content.url' => $condUrls,
				['or' => [
					['Site.status' => true],
					['Site.status' => null]
				]],
				['or' => [
					['Site.same_main_url' => $sameUrl],
					['Site.same_main_url' => null]
				]],
				['or' => [
					['Site.use_subdomain' => $useSubDomain],
					['Site.use_subdomain' => null]
				]]
			];
		} else {
			$conditions = [
				'Content.url' => $this->getUrlPattern($url),
				['or' => [
					['Site.status' => true],
					['Site.status' => null]
				]],
				['or' => [
					['Site.same_main_url' => $sameUrl],
					['Site.same_main_url' => null]
				]],
				['or' => [
					['Site.use_subdomain' => $useSubDomain],
					['Site.use_subdomain' => null]
				]]
			];
		}
		if ($publish) {
			$conditions = array_merge($conditions, $this->getConditionAllowPublish());
		}
		$content = $this->find('first', ['conditions' => $conditions, 'order' => 'Content.url DESC', 'cache' => false]);
		if (!$content) {
			return false;
		}
		if ($extend && $content['Content']['type'] == 'ContentFolder') {
			return false;
		}
		if ($content && empty($content['Site']['id'])) {
			$content['Site'] = $this->Site->getRootMain()['Site'];
		}
		return $content;
	}

}
