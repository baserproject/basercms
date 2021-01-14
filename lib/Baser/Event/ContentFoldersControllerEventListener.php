<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Event
 * @since           baserCMS v 4.0.9
 * @license         https://basercms.net/license/index.html
 */

App::uses('BcEventListener', 'Event');

/**
 * Class ContentFoldersControllerEventListener
 *
 * @package Baser.Event
 * @property Page $Page
 * @property ContentFolder $ContentFolder
 */
class ContentFoldersControllerEventListener extends BcControllerEventListener
{

	/**
	 * イベント
	 *
	 * @var array
	 */
	public $events = [
		'Contents.beforeMove',
		'Contents.afterMove',
		'Contents.beforeDelete',
		'Contents.afterChangeStatus'
	];

	/**
	 * 古いテンプレートのパス
	 *
	 * コンテンツのフォルダ間移動の際に利用
	 * @var null
	 */
	public $oldPath = null;

	/**
	 * ページモデル
	 *
	 * @var bool|null|object
	 */
	public $Page = null;

	/**
	 * コンテンツフォルダーモデル
	 *
	 * @var bool|null|object
	 */
	public $ContentFolder = null;

	/**
	 *
	 * ContentFoldersControllerEventListener constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		// DB接続ができない場合、処理がコントローラーまで行き着かない為、try で実行
		try {
			$this->Page = ClassRegistry::init('Page');
			$this->ContentFolder = ClassRegistry::init('ContentFolder');
		} catch (Exception $e) {
		}
	}

	/**
	 * Contents Before Move
	 *
	 * oldPath を取得する事が目的
	 *
	 * @param CakeEvent $event
	 * @return bool
	 */
	public function contentsBeforeMove(CakeEvent $event)
	{
		if ($event->data['data']['currentType'] != 'ContentFolder') {
			return true;
		}
		$this->oldPath = $this->Page->getContentFolderPath($event->data['data']['currentId']);
		return true;
	}

	/**
	 * Contents After Move
	 *
	 * テンプレートの移動が目的
	 *
	 * @param CakeEvent $event
	 */
	public function contentsAfterMove(CakeEvent $event)
	{
		if ($event->data['data']['Content']['type'] != 'ContentFolder') {
			return;
		}
		$Controller = $event->subject();
		$this->Page->Behaviors->unload('BcCache');
		$contents = $Controller->Content->children($event->data['data']['Content']['id'], false, ['type', 'entity_id'], 'Content.lft', null, 1, 1);
		foreach($contents as $content) {
			if ($content['Content']['type'] !== 'Page') {
				continue;
			}
			$page = $this->Page->find('first', ['conditions' => ['Page.id' => $content['Content']['entity_id']], 'recursive' => 0]);
			$this->Page->createPageTemplate($page);
			$this->Page->saveSearchIndex($this->Page->createSearchIndex($page));
		}
		$this->Page->Behaviors->load('BcCache');
		// 別の階層に移動の時は元の固定ページファイルを削除（同一階層の移動の時は削除しない）
		$nowPath = $this->Page->getContentFolderPath($event->data['data']['Content']['id']);
		if ($this->oldPath != $nowPath) {
			$Folder = new Folder($this->oldPath);
			$Folder->delete();
		}
	}

	/**
	 * Contents Before Delete
	 *
	 * ゴミ箱に入れた固定ページのテンプレートの削除が目的
	 *
	 * @param CakeEvent $event
	 */
	public function contentsBeforeDelete(CakeEvent $event)
	{
		$id = $event->data['data'];
		$data = $this->ContentFolder->find('first', ['conditions' => ['Content.id' => $id]]);
		if ($data) {
			$path = $this->Page->getContentFolderPath($id);
			$Folder = new Folder($path);
			$Folder->delete();
			$Controller = $event->subject();
			$contents = $Controller->Content->children($id, false, ['type', 'entity_id'], 'Content.lft', null, 1, 1);
			foreach($contents as $content) {
				if ($content['Content']['type'] !== 'Page') {
					continue;
				}
				$page = $this->Page->find('first', ['conditions' => ['Page.id' => $content['Content']['entity_id']], 'recursive' => 0]);
				$this->Page->deleteSearchIndex($page['Page']['id']);
			}
		}
	}

	/**
	 * Contents After Change Status
	 *
	 * 一覧から公開設定を変更した場合に検索インデックスを更新する事が目的
	 *
	 * @param CakeEvent $event
	 */
	public function contentsAfterChangeStatus(CakeEvent $event)
	{
		if (empty($event->data['result'])) {
			return;
		}
		$id = $event->data['id'];
		/* @var SearchIndex $searchIndexModel */
		$searchIndexModel = ClassRegistry::init('SearchIndex');
		$searchIndexModel->reconstruct($id);
	}

}
