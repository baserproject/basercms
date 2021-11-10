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

namespace BaserCore\Model\Table;

use ArrayObject;
use Cake\Event\Event;
use Cake\Filesystem\Folder;
use Cake\ORM\TableRegistry;
use BaserCore\Model\AppTable;
use BaserCore\Utility\BcUtil;
use Cake\Event\EventInterface;
use Cake\Validation\Validator;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Datasource\EntityInterface;
use BaserCore\Model\Entity\ContentFolder;

/**
 * Class ContentFoldersTable
 * @package BaserCore\Model\Table
 */
class ContentFoldersTable extends AppTable
{
    /**
     * 変更前URL
     *
     * @var array
     */
    public $beforeUrl = null;

    /**
     * 変更前ステータス
     *
     * @var bool|null
     */
    private $beforeStatus = null;

    /**
     * テンプレートを移動可能かどうか
     *
     * @var bool
     */
    public $isMovableTemplate = true;

        /**
     * Initialize
     *
     * @param array $config テーブル設定
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('BaserCore.BcContents');
    }

    /**
     * Implemented Events
     *
     * @return array
     */
    public function implementedEvents(): array
    {
        return array_merge(parent::implementedEvents(), [
            'Controller.Contents.beforeMove' => ['callable' => 'beforeMove'],
            'Controller.Contents.afterMove' => ['callable' => 'afterMove']
        ]);
    }


    /**
     * validationDefault
     *
     * @param  Validator $validator
     * @return Validator
     * @checked
     * @noTodo
     * @unitTest
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
        ->integer('id')
        ->allowEmptyString('id', null, 'create')
        ->add('id', 'valid', ['rule' => 'numeric', 'message' => __d('baser', 'IDに不正な値が利用されています。')]);

        return $validator;
    }

    /**
     * Before Move
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @checked
     * @unitTest
     */
    public function beforeMove(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        if ($event->getData('data.currentType') == 'ContentFolder') {
            $this->setBeforeRecord($event->getData('data.entityId'));
        }
    }

    /**
     * After Move
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @checked
     */
    public function afterMove(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        // TODO: movePageTemplatesがなくなったので、一時措置
        return true;
        // if (!empty($event->getData('data.Content')) && $event->getData('data.Content.type') == 'ContentFolder') {
        //     $this->movePageTemplates($event->getData('data.Content.url'));
        // }
    }

    /**
     * Before Save
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        // 変更前のURLを取得
        if (!empty($entity->id) && ($this->isMovableTemplate || !empty($options['reconstructSearchIndices']))) {
            $this->isMovableTemplate = false;
            $this->setBeforeRecord($entity->id);
        }
        return parent::beforeSave($event, $entity, $options);
    }

    /**
     * After Save
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @param bool
     * @checked
     * @unitTest
     */
    public function afterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        if (!empty($entity->content->url) && $this->beforeUrl) {
            $this->isMovableTemplate = true;
        }
        if (!empty($options['reconstructSearchIndices']) && $this->beforeStatus !== $entity->content->status) {
            // TODO: テスト未実装
            $searchIndexModel = TableRegistry::getTableLocator()->get('SearchIndex');
            $searchIndexModel->reconstruct($entity->content->id);
        }
        return true;
    }

    /**
     * 保存前のURLをセットする
     *
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    private function setBeforeRecord($id)
    {
        $record = $this->get($id, ['contain' => ['Contents']]);
        if ($record->content->url) {
            $this->beforeUrl = $record->content->url;
            $this->beforeStatus = $record->content->status;
        }
    }

    // NOTE: 以前までapp/View/Pages/about.phpなど固定ページの内容をphpファイルで維持していたが、廃止になったのでメソッド削除
    // /**
    //  * 固定ページテンプレートを移動する
    //  * @param string $afterUrl
    //  * @return bool
    //  */
    // public function movePageTemplates($afterUrl)
    // {
    //     if ($this->beforeUrl && $this->beforeUrl != $afterUrl) {
    //         $basePath = APP . 'View' . DS . 'Pages' . DS;
    //         if (is_dir($basePath . $this->beforeUrl)) {
    //             (new Folder())->move([
    //                 'to' => $basePath . $afterUrl,
    //                 'from' => $basePath . $this->beforeUrl,
    //                 'chmod' => 0777
    //             ]);
    //         }
    //     }
    //     $this->beforeUrl = null;
    //     return true;
    // }

    /**
     * サイトルートフォルダを保存
     *
     * @param null $siteId
     * @param array $data
     * @param bool $isUpdateChildrenUrl 子のコンテンツのURLを一括更新するかどうか
     * @return bool
     */
    public function saveSiteRoot($siteId = null, $data = [], $isUpdateChildrenUrl = false)
    {
        if (!isset($data['Content'])) {
            $_data = $data;
            unset($data);
            $data['Content'] = $_data;
        }
        if (!is_null($siteId)) {

            // エイリアスが変更となっているかどうかの判定が必要
            $_data = $this->find('first', ['conditions' => [
                'Content.site_id' => $siteId,
                'Content.site_root' => true
            ]]);
            $_data['Content'] = array_merge($_data['Content'], $data['Content']);
            $data = $_data;
            $this->set($data);
        } else {
            $this->create($data);
        }
        $this->Content->updatingRelated = false;
        if ($this->save()) {
            // エイリアスを変更した場合だけ更新
            if ($isUpdateChildrenUrl) {
                $this->Content->updateChildrenUrl($data['Content']['id']);
            }
            return true;
        } else {
            return false;
        }
    }
}
