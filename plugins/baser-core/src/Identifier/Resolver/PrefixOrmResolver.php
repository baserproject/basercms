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

namespace BaserCore\Identifier\Resolver;

use Authentication\Identifier\Resolver\OrmResolver;
use Authentication\Identifier\Resolver\ResolverInterface;
use Cake\Datasource\EntityInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * PrefixOrmResolver
 */
class PrefixOrmResolver extends OrmResolver implements ResolverInterface
{

    /**
     * Find
     *
     * プレフィックス付きの Jwt の subject を分解して finder に渡す
     *
     * @param array $conditions
     * @param string $type
     * @return array|EntityInterface|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function find(array $conditions, $type = self::TYPE_AND): \ArrayAccess|array|null
    {
        $prefix = '';
        foreach($conditions as $field => $value) {
            if ($field === 'id') {
                [$prefix, $value] = explode('_', $value);
                $conditions[$field] = $value;
            }
        }

        if ($prefix !== $this->_config['prefix']) {
            return null;
        }

        $table = $this->getTableLocator()->get($this->_config['userModel']);

        $query = $table->selectQuery();
        $finders = (array)$this->_config['finder'];
        foreach($finders as $finder => $options) {
            if (is_string($options)) {
                $query->find($options, ['prefix' => $prefix]);
            } else {
                $query->find($finder, array_merge(['prefix' => $prefix], $options));
            }
        }

        $where = [];
        foreach($conditions as $field => $value) {
            $field = $table->aliasField($field);
            if (is_array($value)) {
                $field = $field . ' IN';
            }
            $where[$field] = $value;
        }

        return $query->where([$type => $where])->first();
    }

}
