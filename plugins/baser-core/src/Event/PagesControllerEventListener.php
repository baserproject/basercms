<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Event;

use BaserCore\Model\Entity\Content;
use BaserCore\Model\Table\PagesTable;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Exception;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class PagesControllerEventListener
 *
 * @property PagesTable $Pages
 */
class PagesControllerEventListener extends BcControllerEventListener
{

    /**
     * イベント
     *
     * @var array
     */
    public $events = [
        'BaserCore.Contents.afterMove',
        'BaserCore.Contents.beforeDelete',
        'BaserCore.Contents.afterTrashReturn',
        'BaserCore.Contents.afterChangeStatus'
    ];

    /**
     * ページモデル
     *
     * @var bool|null|object
     */
    public $Pages = null;

    /**
     * PagesControllerEventListener constructor.
     */
    public function __construct()
    {
        parent::__construct();
        // DB接続ができない場合、処理がコントローラーまで行き着かない為、try で実行
        try {
            $this->Pages = TableRegistry::getTableLocator()->get('BaserCore.Pages');
        } catch (Exception $e) {
        }
    }

    /**
     * Contents After Move
     *
     * 検索インデックスの生成が目的
     *
     * @param Event $event
     * @uses baserCoreContentsAfterMove()
     * @checked
     * @noTodo
     * @unitTest
     */
    public function baserCoreContentsAfterMove(Event $event)
    {
        $content = $event->getData('data');
        if ($content->type !== 'Page') {
            return;
        }
        if (empty($content->entity_id)) {
            $contentsTable = TableRegistry::getTableLocator()->get('BaserCore.Contents');
            /* @var Content $contentTmp */
            $contentTmp = $contentsTable->find()->where(['id' => $content->id])->first();
            $entityId = $contentTmp->entity_id;
        } else {
            $entityId = $content->entity_id;
        }
        $page = $this->Pages->find()
            ->where(['Pages.id' => $entityId])
            ->contain('Contents')
            ->first();
        $this->Pages->saveSearchIndex($this->Pages->createSearchIndex($page));
    }

    /**
     * Contents Before Delete
     *
     * ゴミ箱に入れた固定ページの検索インデックスの削除が目的
     * afterDelete の方が確実だが、そのタイミングだと page が取得できないため
     *
     * @param Event $event
     * @uses baserCoreContentsBeforeDelete()
     * @checked
     * @noTodo
     * @unitTest
     * @todo beforeDelete で page::id を取得し afterDelete で削除することを検討
     */
    public function baserCoreContentsBeforeDelete(Event $event)
    {
        $id = $event->getData('data');
        $page = $this->Pages->find()->contain(['Contents'])->where(['Contents.id' => $id])->first();
        if ($page) $this->Pages->deleteSearchIndex($page->id);
    }

    /**
     * Contents After Trash Return
     *
     * ゴミ箱から戻した固定ページの検索インデックス生成が目的
     *
     * @param Event $event
     * @uses baserCoreContentsAfterTrashReturn()
     * @checked
     * @noTodo
     * @unitTest
     */
    public function baserCoreContentsAfterTrashReturn(Event $event)
    {
        $id = $event->getData('data');
        $page = $this->Pages->find()->contain(['Contents'])->where(['Contents.id' => $id])->first();
        if ($page) $this->Pages->saveSearchIndex($this->Pages->createSearchIndex($page));
    }

    /**
     * Contents After Change Status
     *
     * 一覧から公開設定を変更した場合に固定ページの検索インデックスを更新する事が目的
     *
     * @param Event $event
     * @uses baserCoreContentsAfterChangeStatus()
     * @checked
     * @noTodo
     * @unitTest
     */
    public function baserCoreContentsAfterChangeStatus(Event $event)
    {
        if (empty($event->getData('result'))) {
            return;
        }
        $id = $event->getData('id');
        $page = $this->Pages->find()->contain(['Contents'])->where(['Contents.id' => $id])->first();
        if($page) $this->Pages->saveSearchIndex($this->Pages->createSearchIndex($page));
    }

}
