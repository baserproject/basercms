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

namespace BaserCore\View\Helper;

use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Service\DblogsServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use Cake\Datasource\ResultSetInterface;
use Cake\View\Helper;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;

/**
 * Class BcAdminDashboardHelper
 */
class BcAdminDashboardHelper extends Helper
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * 最近の動きを取得
     *
     * @param int limit
     * @return ResultSetInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getDblogs($limit): ResultSetInterface
    {
        return $this->getService(DblogsServiceInterface::class)->getDblogs($limit);
    }

    /**
     * コンテンツ情報を取得する
     *
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getContentsInfo()
    {
        return $this->getService(ContentsServiceInterface::class)->getContentsInfo();
    }

}
