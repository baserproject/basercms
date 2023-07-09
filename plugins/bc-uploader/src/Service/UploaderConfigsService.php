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

namespace BcUploader\Service;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BcUploader\Model\Entity\UploaderConfig;
use Cake\ORM\TableRegistry;

/**
 * UploaderConfigsService
 */
class UploaderConfigsService implements UploaderConfigsServiceInterface
{

    /**
     * キャッシュ用 Entity
     * @var UploaderConfig
     */
    protected $entity;

    /**
     * constructor.
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct()
    {
        $this->UploaderConfigs = TableRegistry::getTableLocator()->get('BcUploader.UploaderConfigs');
    }

    /**
     * アップローダー設定を取得
     * @return UploaderConfig|\Cake\Datasource\EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get()
    {
        if (!$this->entity) {
            $entity = $this->UploaderConfigs->newEntity(
                $this->UploaderConfigs->getKeyValue(),
                ['validate' => 'keyValue']
            );
            if ($entity->toArray()) {
                $this->entity = $entity;
            }
        }
        return $this->entity;
    }

    /**
     * キャッシュ用 Entity を削除
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function clearCache()
    {
        $this->entity = null;
    }

    /**
     * アップローダー設定を更新する
     *
     * @param array $postData
     * @return UploaderConfig|\Cake\Datasource\EntityInterface|false
     * @noTodo
     * @checked
     * @unitTest
     */
    public function update(array $postData)
    {
        $uploaderConfig = $this->UploaderConfigs->newEntity($postData, ['validate' => 'keyValue']);
        if ($uploaderConfig->hasErrors()) {
            return $uploaderConfig;
        }

        $siteConfigArray = $uploaderConfig->toArray();
        if ($this->UploaderConfigs->saveKeyValue($siteConfigArray)) {
            $this->entity = null;
            return $this->get();
        }
        return false;
    }

}
