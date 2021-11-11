<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Event
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('BcEventListener', 'Event');

/**
 * Class MailControllerEventListener
 *
 * @package Baser.Event
 * @property Page $Page
 */
class MailControllerEventListener extends BcControllerEventListener
{

	/**
	 * イベント
	 *
	 * @var array
	 */
	public $events = [
		'Contents.beforeDelete',
		'Contents.afterTrashReturn',
		'Contents.afterChangeStatus'
	];

	/**
	 * ページモデル
	 *
	 * @var bool|null|object
	 */
	public $MailContent = null;

	/**
	 * PagesControllerEventListener constructor.
	 */
	public function __construct() {

		parent::__construct();

		// DB接続ができない場合、処理がコントローラーまで行き着かない為、try で実行
		try {
			$this->MailContent = ClassRegistry::init('Mail.MailContent');
		} catch (Exception $e) {
		}
	}

	/**
	 * Contents Before Delete
	 *
	 * ゴミ箱に入れた場合に検索インデックスを削除する事が目的
	 *
	 * @param CakeEvent $event
	 */
	public function contentsBeforeDelete(CakeEvent $event) {

		$id = $event->data['data'];
		$data = $this->MailContent->find('first', ['conditions' => ['Content.id' => $id]]);
		if ($data) {
			$this->MailContent->deleteSearchIndex($data['MailContent']['id']);
		}
	}

	/**
	 * Contents After Trash Return
	 *
	 * ゴミ箱から戻した場合に検索インデックスを更新する事が目的
	 *
	 * @param CakeEvent $event
	 */
	public function contentsAfterTrashReturn(CakeEvent $event) {

		$id = $event->data;
		$data = $this->MailContent->find('first', ['conditions' => ['Content.id' => $id]]);
		if ($data) {
			if (empty($data['Content']['exclude_search'])) {
				$this->MailContent->saveSearchIndex($this->MailContent->createSearchIndex($data));
			} else {
				$this->MailContent->deleteSearchIndex($data['MailContent']['id']);
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
	public function contentsAfterChangeStatus(CakeEvent $event) {

		if (empty($event->data['result'])) {
			return;
		}
		$id = $event->data['id'];
		$data = $this->MailContent->find('first', ['conditions' => ['Content.id' => $id]]);
		if (empty($data['Content']['exclude_search'])) {
			$this->MailContent->saveSearchIndex($this->MailContent->createSearchIndex($data));
		} else {
			$this->MailContent->deleteSearchIndex($data['MailContent']['id']);
		}
	}

}
