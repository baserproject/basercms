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

namespace BcCustomContent\Model\Table;

use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\Model\Table\AppTable;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * CustomContentsTable
 *
 * @property CustomTablesTable $CustomTables
 */
class CustomContentsTable extends AppTable
{

    /**
     * Trait
     */
    use BcEventDispatcherTrait;

    /**
     * Initialize
     *
     * @param array $config テーブル設定
     * @checked
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('BaserCore.BcContents');
        $this->addBehavior('Timestamp');
        $this->belongsTo('CustomTables', ['className' => 'BcCustomContent.CustomTables'])
            ->setForeignKey('custom_table_id');
    }

}
