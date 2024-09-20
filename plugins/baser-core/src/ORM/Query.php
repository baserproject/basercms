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

namespace BaserCore\ORM;

use Cake\ORM\Query as CakeQuery;

/**
 * Soft delete-aware query
 */
class Query extends CakeQuery
{
    /**
     * Overwriting triggerBeforeFind() to let queries not return soft deleted records
     *
     * Cake\ORM\Query::triggerBeforeFind() overwritten to add the condition `deleted IS NULL` to every find request
     * in order to not return soft deleted records.
     * If the query contains the option `withDeleted`, the condition `deleted IS NULL` is not applied.
     *
     * @return void
     */
    public function triggerBeforeFind(): void
    {
        if (!$this->_beforeFindFired && $this->_type === 'select') {
            parent::triggerBeforeFind();

            $repository = $this->getRepository();
            $options = $this->getOptions();

            if (!is_array($options) || !in_array('withDeleted', $options)) {
                $aliasedField = $repository->aliasField($repository->getSoftDeleteField());
                $this->andWhere($aliasedField . ' IS NULL');
            }
        }
    }
}
