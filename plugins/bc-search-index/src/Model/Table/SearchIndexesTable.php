<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcSearchIndex\Model\Table;

use BaserCore\Model\AppTable;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;

/**
 * SearchIndexesTable
 */
class SearchIndexesTable extends AppTable
{

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
        $this->addBehavior('Timestamp');
    }

    /**
     * 公開状態確認
     *
     * @param array $data
     * @return bool|int
     * @checked
     * @noTodo
     * @unitTest
     */
    public function allowPublish($data)
    {
        $allowPublish = (int)$data['status'];
        if ($data['publish_begin'] == '0000-00-00 00:00:00') {
            $data['publish_begin'] = null;
        }
        if ($data['publish_end'] == '0000-00-00 00:00:00') {
            $data['publish_end'] = null;
        }
        // 期限を設定している場合に条件に該当しない場合は強制的に非公開とする
        if (($data['publish_begin'] && $data['publish_begin'] >= date('Y-m-d H:i:s')) ||
            ($data['publish_end'] && $data['publish_end'] <= date('Y-m-d H:i:s'))) {
            $allowPublish = false;
        }
        return $allowPublish;
    }

}
