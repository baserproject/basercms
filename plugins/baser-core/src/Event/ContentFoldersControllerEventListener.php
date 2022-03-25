<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Event;

use ArrayObject;
use BaserCore\Model\Entity\Page;
use BaserCore\Model\Table\ContentFoldersTable;
use BaserCore\Model\Table\ContentsTable;
use BaserCore\Model\Table\PagesTable;
use BaserCore\Model\Table\SearchIndexesTable;
use Cake\ORM\TableRegistry;
use Cake\Event\EventInterface;
use Cake\Datasource\EntityInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class ContentFoldersControllerEventListener
 */
class ContentFoldersControllerEventListener extends BcControllerEventListener
{

    /**
     * イベント
     *
     * @var array
     */
    public $events = [
        'BaserCore.Contents.afterMove',
        'BaserCore.Contents.beforeDelete',
        'BaserCore.Contents.afterChangeStatus'
    ];

    /**
     * ページモデル
     *
     * @var PagesTable|null
     */
    public $Pages = null;

    /**
     * コンテンツフォルダーモデル
     *
     * @var ContentFoldersTable|null
     */
    public $ContentFolders = null;

    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
        // DB接続ができない場合、処理がコントローラーまで行き着かない為、try で実行
        try {
            $this->Pages = TableRegistry::getTableLocator()->get('BaserCore.Pages');
            $this->ContentFolders = TableRegistry::getTableLocator()->get('BaserCore.ContentFolders');
        } catch (\Exception $e) {}
    }

    /**
     * Contents After Move
     *
     * 検索インデックスの際生成を行う
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @uses baserCoreContentsAfterMove()
     * @checked
     * @noTodo
     * @unitTest
     */
    public function baserCoreContentsAfterMove(EventInterface $event)
    {
        $content = $event->getData('data');
        if ($content->type !== 'ContentFolder') {
            return;
        }
        /* @var ContentsTable $contentsTable */
        $contentsTable = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        $contents = $contentsTable->find('children', ['for' => $content->id])->select(['type', 'entity_id'])->order('lft')->all();
        foreach($contents as $content) {
            if ($content->type !== 'Page') continue;
            /* @var Page $page */
            $page = $this->Pages->find()->where(['Pages.id' => $content->entity_id])->contain(['Contents'])->first();
            $this->Pages->saveSearchIndex($this->Pages->createSearchIndex($page));
        }
    }

    /**
     * Contents Before Delete
     *
     * ゴミ箱に入れた固定ページのテンプレートの削除が目的
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @uses baserCoreContentsBeforeDelete()
     */
    public function baserCoreContentsBeforeDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
//        $id = $event->getData('data');
//        $data = $this->ContentFolders->find()->where(['Content.id' => $id])->first();
//        if ($data) {
//            // TODO: 固定ページのファイル生成は廃止なので、一旦コメントアウト
//            // $path = $this->Pages->getContentFolderPath($id);
//            // $Folder = new Folder($path);
//            // $Folder->delete();
//            $Controller = $event->getSubject();
//            $contents = $Controller->Contents->children($id, false, ['type', 'entity_id'], 'Content.lft', null, 1, 1);
//            foreach($contents as $content) {
//                if ($content->type !== 'Page') {
//                    continue;
//                }
//                $page = $this->Pages->find()->where(['Page.id' => $content['Content']['entity_id']])->first();
//                $this->Pages->deleteSearchIndex($page->id);
//            }
//        }
    }

    /**
     * Contents After Change Status
     *
     * 一覧から公開設定を変更した場合に検索インデックスを更新する事が目的
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @uses baserCoreContentsAfterChangeStatus()
     */
    public function baserCoreContentsAfterChangeStatus(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
//        if (empty($event->getData('result'))) {
//            return;
//        }
//        $id = $event->getData('id');
//        /* @var SearchIndexesTable $searchIndexesTable */
//        $searchIndexesTable = TableRegistry::getTableLocator()->get('BaserCore.SearchIndex');
//        $searchIndexesTable->reconstruct($id);
    }

}
