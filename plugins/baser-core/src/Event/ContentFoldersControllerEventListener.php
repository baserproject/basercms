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

use ArrayObject;
use BaserCore\Model\Entity\Page;
use BaserCore\Model\Table\ContentFoldersTable;
use BaserCore\Model\Table\ContentsTable;
use BaserCore\Model\Table\PagesTable;
use BcSearchIndex\Service\SearchIndexesService;
use BcSearchIndex\Service\SearchIndexesServiceInterface;
use BaserCore\Utility\BcContainerTrait;
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
     * Trait
     */
    use BcContainerTrait;

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
     * @checked
     * @noTodo
     * @unitTest
     */
    public function baserCoreContentsBeforeDelete(EventInterface $event)
    {
        $id = $event->getData('data');
        if ($this->ContentFolders->find()->where(['Contents.id' => $id])->contain(['Contents'])->count()) {
            $contentsTable = TableRegistry::getTableLocator()->get('BaserCore.Contents');
            $contents = $contentsTable->find('children', ['for' => $id])->select(['type', 'entity_id'])->order('lft')->all();
            foreach($contents as $content) {
                if ($content->type !== 'Page') {
                    continue;
                }
                $this->Pages->deleteSearchIndex($content->entity_id);
            }
        }
    }

    /**
     * Contents After Change Status
     *
     * 一覧から公開設定を変更した場合に検索インデックスを更新する事が目的
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @uses baserCoreContentsAfterChangeStatus()
     * @checked
     * @noTodo
     * @unitTest
     */
    public function baserCoreContentsAfterChangeStatus(EventInterface $event)
    {
        if (empty($event->getData('result'))) {
            return;
        }
        $id = $event->getData('id');
        /* @var SearchIndexesService $searchIndexService */
        $searchIndexService = $this->getService(SearchIndexesServiceInterface::class);
        $searchIndexService->reconstruct($id);
    }

}
