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

namespace BcUploader\Model\Table;

use ArrayObject;
use BaserCore\Model\Table\AppTable;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use BcUploader\Service\UploaderConfigsService;
use BcUploader\Service\UploaderConfigsServiceInterface;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Validation\Validator;

/**
 * ファイルアップローダーモデル
 *
 */
class UploaderFilesTable extends AppTable
{

    /**
     * Trait
     */
    use BcContainerTrait;

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

        $this->setTable('uploader_files');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->belongsTo('UploaderCategories', [
            'className' => 'BcUploader.UploaderCategories',
            'foreignKey' => 'uploader_category_id',
        ]);
        $this->setupUploadBehavior();
    }

    /**
     * BcUploadBehavior を設定する
     * @return void
     * @checked
     */
    public function setupUploadBehavior()
    {
        /** @var UploaderConfigsService $uploaderConfigsService */
        $uploaderConfigsService = $this->getService(UploaderConfigsServiceInterface::class);
        $uploaderConfig = $uploaderConfigsService->get();
        $sizes = ['large', 'midium', 'small', 'mobile_large', 'mobile_small'];
        $imagecopy = [];
        foreach($sizes as $size) {
            if (!isset($uploaderConfig->{$size . '_width'}) || !isset($uploaderConfig->{$size . '_height'})) {
                continue;
            }
            $imagecopy[$size] = ['suffix' => '__' . $size];
            $imagecopy[$size]['width'] = $uploaderConfig->{$size . '_width'};
            $imagecopy[$size]['height'] = $uploaderConfig->{$size . '_height'};
            if (isset($uploaderConfig->{$size . '_thumb'})) {
                $imagecopy[$size]['thumb'] = $uploaderConfig->{$size . '_thumb'};
            }
        }
        $this->addBehavior('BaserCore.BcUpload', [
            'saveDir' => 'uploads',
            'existsCheckDirs' => ['uploads/limited'],
            'fields' => [
                'name' => [
                    'type' => 'all',
                    'imagecopy' => $imagecopy
                ]
            ]
        ]);
        // BcUploadBehavior より優先順位をあげる為登録、イベントを登録しなおす
        // TODO ucmitz 未実装
//        $this->getEventManager()->detach([$this, 'beforeDelete'], 'Model.beforeDelete');
//        $this->getEventManager()->attach([$this, 'beforeDelete'], 'Model.beforeDelete', ['priority' => 5]);
    }

    /**
     * 公開期間をチェックする
     *
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function checkPeriod($value, $context = null)
    {
        if (!empty($context['data']['publish_begin']) && !empty($context['data']['publish_end'])) {
            if (strtotime($context['data']['publish_begin']) > strtotime($context['data']['publish_end'])) {
                return false;
            }
        }
        return true;
    }

    /**
     * validationDefault
     *
     * @param Validator $validator
     * @return Validator
     * @checked
     * @noTodo
     * @unitTest
     */
    public function validationDefault(Validator $validator): Validator
    {
        // id
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        // name
        $validator
            ->allowEmptyString('name')
            ->add('name', [
                'fileCheck' => [
                    'rule' => ['fileCheck', BcUtil::convertSize(ini_get('upload_max_filesize'))],
                    'provider' => 'bc',
                    'message' => __d('baser_core', 'ファイルのアップロード制限を超えています。')
                ]
            ]);
        if (!BcUtil::isAdminUser() || !Configure::read('BcUploader.allowedAdmin')) {
            $validator->add('name', [
                'fileExt' => [
                    'rule' => ['fileExt', Configure::read('BcUploader.allowedExt')],
                    'provider' => 'bc',
                    'message' => __d('baser_core', '許可されていないファイルです。')
                ]
            ]);
        }

        // publish_begin
        $validator->add('publish_begin', [
            'checkPeriod' => [
                'rule' => 'checkPeriod',
                'provider' => 'table',
                'message' => __d('baser_core', '公開期間が不正です。')
            ]
        ]);

        // publish_end
        $validator->add('publish_end', [
            'checkPeriod' => [
                'rule' => 'checkPeriod',
                'provider' => 'table',
                'message' => __d('baser_core', '公開期間が不正です。')
            ]
        ]);
        return $validator;
    }

    /**
     * Before Save
     *
     * @param array $options
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        if ($entity->id) {
            $savePath = $this->getBehavior('BcUpload')->getFileUploader()->getSaveDir();
            $sizes = ['large', 'midium', 'small', 'mobile_large', 'mobile_small'];
            $pathinfo = pathinfo($entity->name);

            if (!empty($entity->publish_begin) || !empty($entity->publish_end)) {
                if (file_exists($savePath . $entity->name)) {
                    rename($savePath . $entity->name, $savePath . 'limited' . DS . $entity->name);
                }
                foreach($sizes as $size) {
                    $file = $pathinfo['filename'] . '__' . $size . '.' . $pathinfo['extension'];
                    if (file_exists($savePath . $file)) {
                        rename($savePath . $file, $savePath . 'limited' . DS . $file);
                    }
                }
            } else {
                if (file_exists($savePath . 'limited' . DS . $entity->name)) {
                    rename($savePath . 'limited' . DS . $entity->name, $savePath . $entity->name);
                }
                foreach($sizes as $size) {
                    $file = $pathinfo['filename'] . '__' . $size . '.' . $pathinfo['extension'];
                    if (file_exists($savePath . 'limited' . DS . $file)) {
                        rename($savePath . 'limited' . DS . $file, $savePath . $file);
                    }
                }
            }
        }

        return true;
    }

    /**
     * ソースファイルの名称を取得する
     * @param $fileName
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSourceFileName($fileName)
    {
        $sizes = ['large', 'midium', 'small', 'mobile_large', 'mobile_small'];
        return preg_replace('/__(' . implode('|', $sizes) . ')\./', '.', $fileName);
    }

    /**
     * Before Delete
     *
     * @param Event $event
     * @checked
     * @noTodo
     */
    public function beforeDelete(EventInterface $event, EntityInterface $entity, \ArrayObject $options)
    {
        $entity = $event->getData('entity');
        $fileUploader = $this->getFileUploader();
        if ($entity->isLimited()) {
            $fileUploader->savePath .= 'limited' . DS;
        } else {
            $fileUploader->savePath = preg_replace('/' . preg_quote('limited' . DS, '/') . '$/', '', $fileUploader->savePath);
        }
    }

}
