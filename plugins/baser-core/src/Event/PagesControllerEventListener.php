<?php
// TODO : コード確認要
use Cake\Event\Event;

return;
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
 * Class PagesControllerEventListener
 *
 * @package Baser.Event
 * @property Page $Page
 */
class PagesControllerEventListener extends BcControllerEventListener
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
        'Contents.afterTrashReturn',
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
     * PagesControllerEventListener constructor.
     */
    public function __construct()
    {
        parent::__construct();
        // DB接続ができない場合、処理がコントローラーまで行き着かない為、try で実行
        try {
            $this->Page = ClassRegistry::init('Page');
        } catch (Exception $e) {
        }
    }

    /**
     * Contents Before Move
     *
     * oldPath を取得する事が目的
     *
     * @param Event $event
     * @return bool|void
     */
    public function contentsBeforeMove(Event $event)
    {
        if ($event->getData('data.currentType') != 'Page') {
            return true;
        }
        $Controller = $event->getSubject();
        $entityId = $Controller->Content->field('entity_id', [
            'Content.id' => $event->getData('data.currentId')
        ]);
        $this->oldPath = $this->Page->getPageFilePath(
            $this->Page->find('first', [
                    'conditions' => ['Page.id' => $entityId],
                    'recursive' => 0]
            )
        );
        return true;
    }

    /**
     * Contents After Move
     *
     * テンプレートの移動が目的
     *
     * @param Event $event
     */
    public function contentsAfterMove(Event $event)
    {
        if ($event->getData('data.Content.type') != 'Page') {
            return;
        }
        if (empty($event->getData('data.Content.entity_id'))) {
            $Controller = $event->getSubject();
            $entityId = $Controller->Content->field('entity_id', [
                'Content.id' => $event->getData('data.Content.id')
            ]);
        } else {
            $entityId = $event->getData('data.Content.entity_id');
        }
        $data = $this->Page->find('first', [
            'conditions' => ['Page.id' => $entityId],
            'recursive' => 0
        ]);
        $this->Page->oldPath = $this->oldPath;
        $this->Page->createPageTemplate($data);
        $this->Page->saveSearchIndex($this->Page->createSearchIndex($data));
    }

    /**
     * Contents Before Delete
     *
     * ゴミ箱に入れた固定ページのテンプレートの削除が目的
     *
     * @param Event $event
     */
    public function contentsBeforeDelete(Event $event)
    {
        $id = $event->getData('data');
        $data = $this->Page->find('first', ['conditions' => ['Content.id' => $id]]);
        if ($data) {
            $this->Page->delFile($data);
            $this->Page->deleteSearchIndex($data['Page']['id']);
        }
    }

    /**
     * Contents After Trash Return
     *
     * ゴミ箱から戻した固定ページのテンプレート生成が目的
     *
     * @param Event $event
     */
    public function contentsAfterTrashReturn(Event $event)
    {
        $id = $event->getData();
        $data = $this->Page->find('first', ['conditions' => ['Content.id' => $id]]);
        if ($data) {
            $this->Page->createPageTemplate($data);
            $this->Page->saveSearchIndex($this->Page->createSearchIndex($data));
        }
    }

    /**
     * Contents After Change Status
     *
     * 一覧から公開設定を変更した場合に固定ページの検索インデックスを更新する事が目的
     *
     * @param Event $event
     */
    public function contentsAfterChangeStatus(Event $event)
    {
        if (empty($event->getData('result'))) {
            return;
        }
        $id = $event->getData('id');
        $data = $this->Page->find('first', ['conditions' => ['Content.id' => $id]]);
        $this->Page->saveSearchIndex($this->Page->createSearchIndex($data));
    }

}
