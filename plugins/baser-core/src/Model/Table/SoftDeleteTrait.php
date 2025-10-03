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

namespace BaserCore\Model\Table;

use ArrayObject;
use Cake\ORM\RulesChecker;
use Cake\Datasource\EntityInterface;
use BaserCore\Error\MissingColumnException;

/**
 * SoftDeleteTrait
 */
trait SoftDeleteTrait
{

    /**
     * 利用状態
     * @var bool
     */
    private $enabled = true;

    /**
     * 論理削除を利用可能にする
     * @return void
     */
    public function enableSoftDelete()
    {
        $this->enabled = true;
    }

    /**
     * 論理削除を利用不可にする
     * @return void
     */
    public function disableSoftDelete()
    {
        $this->enabled = false;
    }

    /**
     * Get the configured deletion field
     *
     * @return string
     * @throws \BaserCore\Error\MissingColumnException
     */
    public function getSoftDeleteField()
    {
        if (isset($this->softDeleteField)) {
            $field = $this->softDeleteField;
        } else {
            $field = 'deleted';
        }

        if ($this->getSchema()->getColumn($field) === null) {
            throw new MissingColumnException(
                __(
                    'Configured field `{0}` is missing from the table `{1}`.',
                    $field,
                    $this->getAlias()
                )
            );
        }

        return $field;
    }

    /**
     * Select Query
     *
     * 論理削除用の SelectQuery を返却する
     * TreeBehavior を利用して、アイテムを移動する場合、
     * 論理削除されたアイテムも対象としなければツリー構造が壊れてしまうため、
     * SoftDeleteTrait を、利用不可にすることで、オリジナルの SelectQueryを返却できるようにしている
     *
     * @return \BaserCore\ORM\SelectQuery|\Cake\ORM\Query\SelectQuery
     */
    public function SelectQuery(): \BaserCore\ORM\SelectQuery|\Cake\ORM\Query\SelectQuery
    {
        if($this->enabled) {
            return new \BaserCore\ORM\SelectQuery($this);
        } else {
            return new \Cake\ORM\Query\SelectQuery($this);
        }
    }

    /**
     * Perform the delete operation.
     *
     * Will soft delete the entity provided. Will remove rows from any
     * dependent associations, and clear out join tables for BelongsToMany associations.
     *
     * @param \Cake\DataSource\EntityInterface $entity The entity to soft delete.
     * @param \ArrayObject $options The options for the delete.
     * @throws \InvalidArgumentException if there are no primary key values of the
     * passed entity
     * @return bool success
     */
    protected function _processDelete($entity, $options): bool
    {
        if(!$this->enabled) {
            return parent::_processDelete($entity, $options);
        }

        if ($entity->isNew()) {
            return false;
        }

        $primaryKey = (array)$this->getPrimaryKey();
        if (!$entity->has($primaryKey)) {
            $msg = 'Deleting requires all primary key values.';
            throw new \InvalidArgumentException($msg);
        }

        if ($options['checkRules'] && !$this->checkRules($entity, RulesChecker::DELETE, $options)) {
            return false;
        }

        $query = $this->updateQuery();
        $conditions = (array)$entity->extract($primaryKey);
        $statement = $query->update($this->getTable())
            ->set([$this->getSoftDeleteField() => date('Y-m-d H:i:s')])
            ->where($conditions)
            ->execute();

        $success = $statement->rowCount() > 0;
        if (!$success) {
            return $success;
        }

        return $success;
    }

    /**
     * Soft deletes all records matching `$conditions`.
     * @param array $conditions entities search conditions
     * @return int number of affected rows.
     */
    public function deleteAll($conditions): int
    {
        if(!$this->enabled) {
            return parent::deleteAll($conditions);
        }
        $query = $this->updateQuery()
            ->update($this->getTable())
            ->set([$this->getSoftDeleteField() => date('Y-m-d H:i:s')])
            ->where($conditions);
        $statement = $query->execute();
        $statement->closeCursor();
        return $statement->rowCount();
    }

    /**
     * Hard deletes the given $entity.
     * @param EntityInterface $entity entity
     * @return bool true in case of success, false otherwise.
     */
    public function hardDelete(EntityInterface $entity, array $options = [])
    {
        if(!$this->enabled) {
            throw new \BadMethodCallException('SoftDeleteTrait is not enabled');
        }

        $options = new ArrayObject($options);

        $event = $this->dispatchEvent('Model.beforeDelete', [
            'entity' => $entity,
            'options' => $options,
        ]);
        if ($event->isStopped()) {
            return $event->getResult();
        }

        $this->_associations->cascadeDelete(
            $entity,
            ['_primary' => false] + $options->getArrayCopy()
        );

        $primaryKey = (array)$this->getPrimaryKey();
        $query = $this->deleteQuery();
        $conditions = (array)$entity->extract($primaryKey);
        $statement = $query->delete($this->getTable())
            ->where($conditions)
            ->execute();

        $success = $statement->rowCount() > 0;
        if (!$success) {
            return $success;
        }

        $this->dispatchEvent('Model.afterDelete', [
            'entity' => $entity,
            'options' => $options,
        ]);

        return $success;
    }

    /**
     * Hard deletes all records that were soft deleted before a given date.
     * @param \DateTime $until Date until which soft deleted records must be hard deleted.
     * @return int number of affected rows.
     */
    public function hardDeleteAll(\Datetime $until)
    {
        if(!$this->enabled) {
            throw new \BadMethodCallException('SoftDeleteTrait is not enabled');
        }
        $query = $this->deleteQuery()
            ->delete()
            ->where([
                $this->getSoftDeleteField() . ' IS NOT NULL',
                $this->getSoftDeleteField() . ' <=' => $until->format('Y-m-d H:i:s')
            ]);
        $statement = $query->execute();
        $statement->closeCursor();
        return $statement->rowCount();
    }

    /**
     * Restore a soft deleted entity into an active state.
     * @param EntityInterface $entity Entity to be restored.
     * @return bool true in case of success, false otherwise.
     */
    public function restore(EntityInterface $entity)
    {
        if(!$this->enabled) {
            throw new \BadMethodCallException('SoftDeleteTrait is not enabled');
        }
        $softDeleteField = $this->getSoftDeleteField();
        $entity->$softDeleteField = null;
        return $this->save($entity);
    }
}
