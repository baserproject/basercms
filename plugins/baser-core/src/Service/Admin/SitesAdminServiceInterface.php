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
namespace BaserCore\Service\Admin;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Datasource\EntityInterface;

/**
 * SitesAdminServiceInterface
 */
interface SitesAdminServiceInterface
{

    /**
     * 一覧画面用のデータを取得する
     * @param \Cake\ORM\ResultSet|\Cake\Datasource\ResultSetInterface $sites
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForIndex($sites): array;

    /**
     * 編集画面用のデータを取得する
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForEdit(EntityInterface $site): array;

    /**
     * 編集画面用のデータを取得する
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForAdd(EntityInterface $site): array;

}
