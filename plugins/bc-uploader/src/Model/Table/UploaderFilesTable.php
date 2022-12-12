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
use BcUploader\Service\UploaderConfigsService;
use BcUploader\Service\UploaderConfigsServiceInterface;
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
 * @package        Uploader.Model
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

    public function setupUploadBehavior()
    {
        /** @var UploaderConfigsService $uploaderConfigsService */
        $uploaderConfigsService = $this->getService(UploaderConfigsServiceInterface::class);
        $uploaderConfig = $uploaderConfigsService->get();
        $sizes = ['large', 'midium', 'small', 'mobile_large', 'mobile_small'];
        $imagecopy = [];
        foreach ($sizes as $size) {
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
     */
    public function checkPeriod()
    {
        if (!empty($this->data['UploaderFile']['publish_begin']) && !empty($this->data['UploaderFile']['publish_end'])) {
            if (strtotime($this->data['UploaderFile']['publish_begin']) > strtotime($this->data['UploaderFile']['publish_end'])) {
                return false;
            }
        }
        return true;
    }

    /**
     * MailField constructor.
     *
     * @param bool $id
     * @param null $table
     * @param null $ds
     * @checked
     * @noTodo
     */
    public function validationDefault(Validator $validator): Validator
    {
        // TODO ucmitz 未実装
//        $this->validate = [
//            'publish_begin' => [
//                'checkPeriod' => [
//                    'rule' => 'checkPeriod',
//                    'message' => __d('baser', '公開期間が不正です。')
//                ]
//            ],
//            'publish_end' => [
//                'checkPeriod' => [
//                    'rule' => 'checkPeriod',
//                    'message' => __d('baser', '公開期間が不正です。')
//                ]
//            ]
//        ];
//        if (!BcUtil::isAdminUser() || !Configure::read('BcUploader.allowedAdmin')) {
//            $this->validate['name'] = [
//                'fileExt' => [
//                    'rule' => ['fileExt', Configure::read('BcUploader.allowedExt')],
//                    'message' => __d('baser', '許可されていないファイル形式です。')
//                ]
//            ];
//        }
        return $validator;
    }

    /**
     * Before Save
     *
     * @param array $options
     * @return bool
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        // TODO ucmitz 未実装
        return true;
        if (!empty($this->data['UploaderFile']['id'])) {

            $savePath = WWW_ROOT . 'files' . DS . $this->actsAs['BcUpload']['saveDir'] . DS;
            $sizes = ['large', 'midium', 'small', 'mobile_large', 'mobile_small'];
            $pathinfo = pathinfo($this->data['UploaderFile']['name']);

            if (!empty($this->data['UploaderFile']['publish_begin']) || !empty($this->data['UploaderFile']['publish_end'])) {
                if (file_exists($savePath . $this->data['UploaderFile']['name'])) {
                    rename($savePath . $this->data['UploaderFile']['name'], $savePath . 'limited' . DS . $this->data['UploaderFile']['name']);
                }
                foreach ($sizes as $size) {
                    $file = $pathinfo['filename'] . '__' . $size . '.' . $pathinfo['extension'];
                    if (file_exists($savePath . $file)) {
                        rename($savePath . $file, $savePath . 'limited' . DS . $file);
                    }
                }
            } else {
                if (file_exists($savePath . 'limited' . DS . $this->data['UploaderFile']['name'])) {
                    rename($savePath . 'limited' . DS . $this->data['UploaderFile']['name'], $savePath . $this->data['UploaderFile']['name']);
                }
                foreach ($sizes as $size) {
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
    public function beforeDelete(Event $event)
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
