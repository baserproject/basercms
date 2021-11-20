<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Event;
use ArrayObject;
use Cake\ORM\TableRegistry;
use Cake\Event\EventInterface;
use Cake\Datasource\EntityInterface;
use BaserCore\Event\BcControllerEventListener;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

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
            $this->Pages = TableRegistry::getTableLocator()->get('BaserCore.Pages');
            $this->ContentFolders = TableRegistry::getTableLocator()->get('BaserCore.ContentFolders');
        } catch (\Exception $e) {

        }
    }

    /**
     * Contents Before Move
     *
     * oldPath を取得する事が目的
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @return bool
     */
    public function contentsBeforeMove(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        if ($event->getData('data.currentType') != 'ContentFolder') {
            return true;
        }
        $this->oldPath = $this->Pages->getContentFolderPath($event->getData('data.currentId'));
        return true;
    }

    /**
     * Contents After Move
     *
     * テンプレートの移動が目的
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     */
    public function contentsAfterMove(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        if ($event->getData('data.Content.type') != 'ContentFolder') {
            return;
        }
        $Controller = $event->getSubject();
        $contents = $Controller->Contents->children($event->getData('data.Content.id'), false, ['type', 'entity_id'], 'Content.lft', null, 1, 1);
        foreach($contents as $content) {
            if ($content->type !== 'Page') {
                continue;
            }
            $page = $this->Pages->find()->where(['Page.id' => $content->entity_id])->first();
            $this->Pages->createPageTemplate($page);
            $this->Pages->saveSearchIndex($this->Pages->createSearchIndex($page));
        }
        // 別の階層に移動の時は元の固定ページファイルを削除（同一階層の移動の時は削除しない）
        $nowPath = $this->Pages->getContentFolderPath($event->getData('data.Content.id'));
        // if ($this->oldPath != $nowPath) {
        //     $Folder = new Folder($this->oldPath);
        //     $Folder->delete();
        // }
    }

    /**
     * Contents Before Delete
     *
     * ゴミ箱に入れた固定ページのテンプレートの削除が目的
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     */
    public function contentsBeforeDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        $id = $event->getData('data');
        $data = $this->ContentFolders->find()->where(['Content.id' => $id])->first();
        if ($data) {
            // TODO: 固定ページのファイル生成は廃止なので、一旦コメントアウト
            // $path = $this->Pages->getContentFolderPath($id);
            // $Folder = new Folder($path);
            // $Folder->delete();
            $Controller = $event->getSubject();
            $contents = $Controller->Contents->children($id, false, ['type', 'entity_id'], 'Content.lft', null, 1, 1);
            foreach($contents as $content) {
                if ($content->type !== 'Page') {
                    continue;
                }
                $page = $this->Pages->find()->where(['Page.id' => $content['Content']['entity_id']])->first();
                $this->Pages->deleteSearchIndex($page->id);
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
     */
    public function contentsAfterChangeStatus(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        if (empty($event->getData('result'))) {
            return;
        }
        $id = $event->getData('id');
        /* @var SearchIndex $searchIndexModel */
        $searchIndexModel = TableRegistry::getTableLocator()->get('BaserCore.SearchIndex');
        $searchIndexModel->reconstruct($id);
    }

}
