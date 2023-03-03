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

namespace BcMail\Service;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BcMail\Model\Entity\MailConfig;
use BcMail\Model\Table\MailConfigsTable;
use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;

/**
 * MailConfigsService
 */
class MailConfigsService implements MailConfigsServiceInterface
{

    /**
     * MailConfigs Table
     * @var MailConfigsTable
     */
    public $MailConfigs;

    /**
     * キャッシュ用 Entity
     * @var MailConfig
     */
    protected $entity;

    /**
     * MailConfigsService constructor.
     *
     * @checked
     * @unitTest
     * @noTodo
     */
    public function __construct()
    {
        $this->MailConfigs = TableRegistry::getTableLocator()->get('BcMail.MailConfigs');
    }

    /**
     * フィールドの値を取得する
     *
     * @param string $fieldName
     * @return string|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getValue($fieldName): ?string
    {
        $entity = $this->get();
        return $entity->{$fieldName};
    }

    /**
     * データを取得する
     *
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get(): MailConfig
    {
        if (!$this->entity) {
            $this->entity = $this->MailConfigs->newEntity(
                $this->MailConfigs->getKeyValue(),
                ['validate' => 'keyValue']
            );
        }
        return $this->entity;
    }

    /**
     * データを更新する
     *
     * @param array $postData
     * @return MailConfig|EntityInterface|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(array $postData)
    {
        $entity = $this->MailConfigs->newEntity($postData, ['validate' => 'keyValue']);
        if ($entity->hasErrors()) {
            return $entity;
        }
        $entityArray = $entity->toArray();
        if ($this->MailConfigs->saveKeyValue($entityArray)) {
            $this->clearCache();
            return $this->get();
        }
        return false;
    }

    /**
     * 設定値を更新する
     *
     * @param string $name
     * @param string $value
     * @return MailConfig
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setValue($name, $value)
    {
        return $this->update([$name => $value]);
    }

    /**
     * 設定値をリセットする
     *
     * @param string $name
     * @return MailConfig
     * @checked
     * @noTodo
     * @unitTest
     */
    public function resetValue($name)
    {
        return $this->setValue($name, '');
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

}
