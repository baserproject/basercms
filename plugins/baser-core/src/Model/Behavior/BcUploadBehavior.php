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

namespace BaserCore\Model\Behavior;

use ArrayObject;
use BaserCore\Utility\BcFileUploader;
use Cake\ORM\Behavior;
use Cake\Event\EventInterface;
use Cake\Datasource\EntityInterface;
use Cake\Validation\Validator;
use BaserCore\Annotation\Note;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * ファイルアップロードビヘイビア
 */
/*
    《設定例》
    $this->addBehavior('BaserCore.BcUpload', [
        // 保存フォルダ名
        'saveDir' => "editor",
        // サブフォルダフォーマット
        // 保存フォルダをサブフォルダで分類する場合に設定
        'subdirDateFormat' => 'Y/m',
        // フィールドごとの設定
        'fields' => [
            // フィールド名
            'image' => [
                // ファイルタイプ
                // all | image | ファイルの拡張子
                'type' => 'image',
                // ファイル名を変換する際に参照するフィールド
                'namefield' => 'id',
                // ファイル名に追加する文字列
                // 文字列 | false
                'nameadd' => false,
                // リサイズ設定
                // アップロードした本体画像をリサイズ
                'imageresize' => [
                // プレフィックス
                'prefix' => 'template',
                // 横幅
                'width' => '100',
                // 高さ
                'height' => '100'
                ],
                // コピー設定
                'imagecopy' => [
                    'thumb' => [
                        'suffix' => 'template',
                        'width' => '150',
                        'height' => '150'
                    ],
                    'thumb_mobile' => [
                        'suffix' => 'template',
                        'width' => '100',
                        'height' => '100'
                    ]
                ]
            ],
            'pdf' => [
                'type' => 'pdf',
                'namefield' => 'id',
                'nameformat' => '%d',
                'nameadd' => false
            ]
        ]
    ]);
 */

/**
 * Class BcUploadBehavior
 *
 * @property BcFileUploader[] $BcFileUploader
 */
class BcUploadBehavior extends Behavior
{

    /**
     * BcFileUploader
     *
     * @var BcFileUploader[]
     */
    public $BcFileUploader = [];

    /**
     * Old Entity
     *
     * @var EntityInterface[]
     */
    public $oldEntity = [];

    /**
     * Initialize
     *
     * @param array $config
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        $this->BcFileUploader[$this->table()->getAlias()] = new BcFileUploader();
        $this->BcFileUploader[$this->table()->getAlias()]->initialize($config, $this->table());
    }

    /**
     * Before Marshal
     *
     * @param EventInterface $event
     * @param ArrayObject $data
     * @param ArrayObject $options
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeMarshal(EventInterface $event, ArrayObject $data, ArrayObject $options)
    {
        // アップロード用のリクエストデータを変換する
        $this->BcFileUploader[$this->table()->getAlias()]->setupRequestData($data);
        $this->BcFileUploader[$this->table()->getAlias()]->setupTmpData($data);
        $this->oldEntity[$this->table()->getAlias()][$data['_bc_upload_id']] = (!empty($data['id']))? $this->getOldEntity($data['id']) : null;
        // ファイルアップロード用のフィールドのエンティティ変換を許可する
        $options['accessibleFields']['_bc_upload_id'] = true;
    }

    /**
     * After Marshal
     *
     * バリデーションエラーが発生した際、ファイルアップロード対象のデータを元に戻す
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @checked
     * @noTodo
     */
    public function afterMarshal(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        if ($entity->getErrors()) {
            $this->BcFileUploader[$this->table()->getAlias()]->rollbackFile($entity);
        }
    }

    /**
     * Build Validator
     *
     * @param EventInterface $event
     * @param Validator $validator
     * @param string $name
     */
    public function buildValidator(EventInterface $event, Validator $validator, $name)
    {
        $settings = $this->getSettings();
        foreach ($settings['fields'] as $field => $fieldSettings) {
            $validator->add($field, 'checkFilePath', [
                'rule' => function ($value) {
                    return (!is_string($value) || !str_contains($value, '../'));
                },
                'message' => __d('baser_core', '許可されていないファイルです。')
            ]);
        }
    }

    /**
     * After Save
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @checked
     * @noTodo
     * @unitTest
     */
    public function afterSave(EventInterface $event, EntityInterface $entity)
    {
        if ($entity->id && !empty($this->oldEntity[$this->table()->getAlias()][$entity->_bc_upload_id])) {
            $oldEntity = $this->oldEntity[$this->table()->getAlias()][$entity->_bc_upload_id];
            $oldEntity->_bc_upload_id = $entity->_bc_upload_id;
            $this->BcFileUploader[$this->table()->getAlias()]->deleteExistingFiles($oldEntity);
        }
        $this->BcFileUploader[$this->table()->getAlias()]->saveFiles($entity);
        if ($entity->id && !empty($this->oldEntity[$this->table()->getAlias()][$entity->_bc_upload_id])) {
            $this->BcFileUploader[$this->table()->getAlias()]->deleteFiles($this->oldEntity[$this->table()->getAlias()][$entity->_bc_upload_id], $entity);
        }
        if ($this->BcFileUploader[$this->table()->getAlias()]->isUploaded()) {
            $this->BcFileUploader[$this->table()->getAlias()]->renameToBasenameFields($entity);
            $this->BcFileUploader[$this->table()->getAlias()]->resetUploaded();
        }
        // 保存時にbeforeSaveとafterSaveのループを防ぐ
        $eventManager = $this->table()->getEventManager();
        $beforeSaveListeners = $eventManager->listeners('Model.beforeSave');
        $afterSaveListeners = $eventManager->listeners('Model.afterSave');
        $eventManager->off('Model.beforeSave');
        $eventManager->off('Model.afterSave');
        $this->table()->save($entity, ['validate' => false]);
        foreach($beforeSaveListeners as $listener) {
            if (get_class($listener['callable'][0]) !== 'BaserCore\Event\BcModelEventDispatcher') {
                $eventManager->on('Model.beforeSave', [], $listener['callable']);
            }
        }
        foreach($afterSaveListeners as $listener) {
            if (get_class($listener['callable'][0]) !== 'BaserCore\Event\BcModelEventDispatcher') {
                $eventManager->on('Model.afterSave', [], $listener['callable']);
            }
        }
    }

    /**
     * テーブル削除時に対象の画像ファイルの削除を行う
     * 削除に失敗してもデータの削除は行う
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeDelete(EventInterface $event, EntityInterface $entity)
    {
        $oldEntity = $this->getOldEntity($entity->id);
        $this->BcFileUploader[$this->table()->getAlias()]->deleteFiles($oldEntity, $entity, true);
    }

    /**
     * 一時ファイルとして保存する
     *
     * @param array $data
     * @param string $tmpId
     * @return mixed false|array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function saveTmpFiles($data, $tmpId)
    {
        return $this->BcFileUploader[$this->table()->getAlias()]->saveTmpFiles($data, $tmpId);
    }

    /**
     * 設定情報を取得
     *
     * @param $alias
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSettings()
    {
        return $this->BcFileUploader[$this->table()->getAlias()]->settings;
    }

    /**
     * 設定情報を設定
     *
     * @param $alias
     * @param $settings
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setSettings($settings)
    {
        $this->BcFileUploader[$this->table()->getAlias()]->initialize($settings, $this->table());
    }

    /**
     * 保存先のパスを取得
     *
     * @param $alias
     * @param false $isTheme
     * @param false $limited
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSaveDir($isTheme = false, $limited = false)
    {
        return $this->BcFileUploader[$this->table()->getAlias()]->getSaveDir($isTheme, $limited);
    }

    /**
     * getFileUploader
     *
     * @param $modelName
     * @return BcFileUploader|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getFileUploader()
    {
        return (isset($this->BcFileUploader[$this->table()->getAlias()]))? $this->BcFileUploader[$this->table()->getAlias()] : false;
    }

    /**
     * getOldEntity
     *
     * @param int $id
     * @return EntityInterface|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getOldEntity($id)
    {
        $table = $this->table();
        $query = $table->find()->where([$table->getAlias() . '.id' => $id]);
        if ($table instanceof \BaserCore\Model\Table\ContentsTable) {
            $oldEntity = $query->applyOptions(['withDeleted'])->first();
        } else {
            $oldEntity = $query->first();
        }
        return ($oldEntity)?: null;
    }

    /**
     * 全フィールドのファイル名をフィールド値ベースのファイル名に変更する
     *
     * @param EntityInterface $entity
     * @param bool $copy
     * @checked
     * @noTodo
     * @unitTest
     */
    public function renameToBasenameFields($entity, $copy = false)
    {
        $this->BcFileUploader[$this->table()->getAlias()]->renameToBasenameFields($entity, $copy);
    }

}
