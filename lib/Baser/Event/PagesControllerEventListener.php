<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Event
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('BcEventListener', 'Event');

/**
 * Class PagesControllerEventListener
 * 
 * @package Baser.Event
 * @property Page $Page
 */
class PagesControllerEventListener extends BcControllerEventListener {

/**
 * イベント
 * 
 * @var array
 */
	public $events = [
		'Contents.beforeMove',
		'Contents.afterMove',
		'Contents.beforeDelete',
		'Contents.afterTrashReturn'
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
 * PagesControllerEventListener constructor.
 */
	public function __construct() {
		parent::__construct();
		$this->Page = ClassRegistry::init('Page'); 
	}

/**
 * Contents Before Move
 * 
 * oldPath を取得する事が目的
 * 
 * @param CakeEvent $event
 * @return bool|void
 */
	public function contentsBeforeMove(CakeEvent $event) {
		if($event->data['data']['currentType'] != 'Page') {
			return true;
		}
		$Controller = $event->subject();
		$entityId = $Controller->Content->field('entity_id', [
			'Content.id' => $event->data['data']['currentId']
		]);
		$this->oldPath = $this->Page->getPageFilePath(
			$this->Page->find('first', array(
					'conditions' => array('Page.id' => $entityId),
					'recursive' => 0)
			)
		);
		return true;
	}

/**
 * Contents After Move
 * 
 * テンプレートの移動が目的
 * 
 * @param CakeEvent $event
 */
	public function contentsAfterMove(CakeEvent $event) {
		if($event->data['data']['Content']['type'] != 'Page') {
			return;
		}
		if(empty($event->data['data']['Content']['entity_id'])) {
			$Controller = $event->subject();
			$entityId = $Controller->Content->field('entity_id', [
				'Content.id' =>$event->data['data']['Content']['id']
			]);
		} else {
			$entityId = $event->data['data']['Content']['entity_id'];
		}
		$data = $this->Page->find('first', [
			'conditions' => ['Page.id' => $entityId],
			'recursive' => 0
		]);
		$this->Page->oldPath = $this->oldPath;	
		$this->Page->createPageTemplate($data);
	}

/**
 * Contents Before Delete
 * 
 * ゴミ箱に入れた固定ページのテンプレートの削除が目的
 * 
 * @param CakeEvent $event
 */
	public function contentsBeforeDelete(CakeEvent $event) {
		$id = $event->data;
		$data = $this->Page->find('first', ['conditions' => ['Content.id' => $id]]);
		if($data) {
			$this->Page->delFile($data);
		}
	}

/**
 * Contents After Trash Return
 * 
 * ゴミ箱から戻した固定ページのテンプレート生成が目的
 * 
 * @param CakeEvent $event
 */
	public function contentsAfterTrashReturn(CakeEvent $event) {
		$id = $event->data;
		$data = $this->Page->find('first', ['conditions' => ['Content.id' => $id]]);
		if($data) {
			$this->Page->createPageTemplate($data);
		}
	}
	
}