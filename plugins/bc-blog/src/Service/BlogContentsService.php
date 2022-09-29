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

namespace BcBlog\Service;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;

/**
 * BlogContentsService
 */
class BlogContentsService implements BlogContentsServiceInterface
{

    /**
     * Construct
     * @checked
     * @noTodo
     */
    public function __construct()
    {
        $this->BlogContents = TableRegistry::getTableLocator()->get("BcBlog.BlogContents");
    }

    /**
     * 単一データ取得
     * @param int $id
     * @return \Cake\Datasource\EntityInterface|array|null
     * @checked
     * @noTodo
     */
    public function get(int $id)
    {
        return $this->BlogContents->find()
            ->contain(['Contents' => ['Sites']])
            ->where(['BlogContents.id' => $id])
            ->first();
    }

}
