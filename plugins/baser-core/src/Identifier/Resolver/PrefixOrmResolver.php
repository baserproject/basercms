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
     */
    public function find(array $conditions, $type = self::TYPE_AND)
    {
        $prefix = '';
        foreach ($conditions as $field => $value) {
            if($field === 'id') {
                [$prefix, $value] = explode('_', $value);
                $conditions[$field] = $value;
            }
        }

        $table = $this->getTableLocator()->get($this->_config['userModel']);

        $query = $table->query();
        $finders = (array)$this->_config['finder'];
        foreach ($finders as $finder => $options) {
            if (is_string($options)) {
                $query->find($options, ['prefix' => $prefix]);
            } else {
                $query->find($finder, array_merge(['prefix' => $prefix], $options));
            }
        }

        $where = [];
        foreach ($conditions as $field => $value) {
            $field = $table->aliasField($field);
            if (is_array($value)) {
                $field = $field . ' IN';
            }
            $where[$field] = $value;
        }

        return $query->where([$type => $where])->first();
    }
}
