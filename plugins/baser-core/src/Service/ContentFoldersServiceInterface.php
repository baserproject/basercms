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

namespace BaserCore\Service;

use BaserCore\Model\Entity\Site;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Datasource\EntityInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Interface ContentFoldersServiceInterface
 */
interface ContentFoldersServiceInterface extends CrudBaseServiceInterface
{

    /**
     * コンテンツフォルダーをゴミ箱から取得する
     * 
     * @param int $id
     * @return EntityInterface|array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTrash($id);

    /**
     * フォルダのテンプレートリストを取得する
     *
     * @param $contentId
     * @param $theme
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getFolderTemplateList($contentId, $theme);

    /**
     * 親のテンプレートを取得する
     *
     * @param int $id
     * @param string $type folder|page
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getParentTemplate($id, $type);

    /**
     * サイトルートフォルダを保存
     *
     * @param Site $site
     * @param bool $isUpdateChildrenUrl 子のコンテンツのURLを一括更新するかどうか
     * @return false|EntityInterface
     * @throws RecordNotFoundException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function saveSiteRoot($site, $isUpdateChildrenUrl = false);

    /**
     * 物理削除
     * 
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(int $id): bool;

}


