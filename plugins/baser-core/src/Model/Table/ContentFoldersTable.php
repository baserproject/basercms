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
     *
     * @param \Cake\Event\Event $event
     */
    public function beforeMove(\Cake\Event\Event $event)
    {
        if ($event->getData('data.currentType') == 'ContentFolder') {
            $this->setBeforeRecord($event->getData('data.entityId'));
        }
    }

    /**
     * After Move
     *
     * @param \\Cake\Event\Event $event
     */
    public function afterMove(\Cake\Event\Event $event)
    {
        if (!empty($event->getData('data.Content')) && $event->getData('data.Content.type') == 'ContentFolder') {
            $this->movePageTemplates($event->getData('data.Content.url'));
        }
    }

    /**
     * Before Save
     * @param Event $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @return bool
     */
    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        // 変更前のURLを取得
        if (!empty($event->getData('entity')->get('id')) && ($this->isMovableTemplate || !empty($options['reconstructSearchIndices']))) {
            $this->isMovableTemplate = false;
            $this->setBeforeRecord($event->getData('entity')->get('id'));
        }
        return parent::beforeSave($event, $entity, $options);
    }

    /**
     * After Save
     *
     * @param bool $created
     * @param array $options
     * @param bool
     */
    public function afterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        if (!empty($entity->content->url) && $this->beforeUrl) {
            $this->movePageTemplates($entity->content->url);
            $this->isMovableTemplate = true;
        }
        // TODO: 一時措置
        // if (!empty($options['reconstructSearchIndices']) && $this->beforeStatus !== $this->data['Content']['status']) {
        //     $searchIndexModel = ClassRegistry::init('SearchIndex');
        //     $searchIndexModel->reconstruct($this->data['Content']['id']);
        // }
        return true;
    }

    /**
     * 保存前のURLをセットする
     *
     * @param int $id
     */
    private function setBeforeRecord($id)
    {
        $record = $this->find('first', ['fields' => ['Content.url', 'Content.status'], 'conditions' => ['ContentFolder.id' => $id], 'recursive' => 0]);
        if ($record['Content']['url']) {
            $this->beforeUrl = $record['Content']['url'];
            $this->beforeStatus = $record['Content']['status'];
        }
    }

    /**
     * 固定ページテンプレートを移動する
     *
     * @param string $afterUrl
     * @return bool
     */
    public function movePageTemplates($afterUrl)
    {
        if ($this->beforeUrl && $this->beforeUrl != $afterUrl) {
            $basePath = APP . 'View' . DS . 'Pages' . DS;
            if (is_dir($basePath . $this->beforeUrl)) {
                (new Folder())->move([
                    'to' => $basePath . $afterUrl,
                    'from' => $basePath . $this->beforeUrl,
                    'chmod' => 0777
                ]);
            }
        }
        $this->beforeUrl = null;
        return true;
    }

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

    /**
     * フォルダのテンプレートリストを取得する
     *
     * @param $contentId
     * @param $theme
     * @return array
     */
    public function getFolderTemplateList($contentId, $theme)
    {
        if (!is_array($theme)) {
            $theme = [$theme];
        }
        $folderTemplates = [];
        foreach($theme as $value) {
            $folderTemplates = array_merge($folderTemplates, BcUtil::getTemplateList('ContentFolders', '', $value));
        }
        if ($contentId != 1) {
            $parentTemplate = $this->getParentTemplate($contentId, 'folder');
            $searchKey = array_search($parentTemplate, $folderTemplates);
            if ($searchKey !== false) {
                unset($folderTemplates[$searchKey]);
            }
            $folderTemplates = ['' => sprintf(__d('baser', '親フォルダの設定に従う（%s）'), $parentTemplate)] + $folderTemplates;
        }
        return $folderTemplates;
    }

    /**
     * 親のテンプレートを取得する
     *
     * @param int $id
     * @param string $type folder|page
     */
    public function getParentTemplate($id, $type)
    {
        // TODO ucmitz 暫定措置
        // >>>
        return 'default';
        // <<<

        $this->Content->bindModel(
            ['belongsTo' => [
                'ContentFolder' => [
                    'className' => 'ContentFolder',
                    'foreignKey' => 'entity_id'
                ]
            ]
            ],
            false
        );
        $contents = $this->Content->getPath($id, null, 0);
        $this->Content->unbindModel(
            ['belongsTo' => [
                'ContentFolder'
            ]
            ]
        );
        $contents = array_reverse($contents);
        unset($contents[0]);
        $parentTemplates = Hash::extract($contents, '{n}.ContentFolder.' . $type . '_template');
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

}
