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

namespace BcWidgetArea\Service;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BcWidgetArea\Model\Entity\WidgetArea;
use Cake\Datasource\EntityInterface;
use Throwable;

/**
 * WidgetAreasServiceInterface
 */
interface WidgetAreasServiceInterface
{

    /**
     * 単一データ取得
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get(int $id);

    /**
     * 一覧データ取得
     *
     * @param array $queryParams
     * @return \Cake\ORM\Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(array $queryParams = []);

    /**
     * 初期データ取得
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew();

    /**
     * 作成
     *
     * @param array $postData
     * @return \Cake\Datasource\EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $postData);

    /**
     * 編集
     * @param EntityInterface $entity
     * @param array $postData
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(EntityInterface $entity, array $postData);

    /**
     * 削除
     * @param int $id
     * @return bool
     * @throws Throwable
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(int $id);

    /**
     * IDからタイトルリストを取得する
     *
     * @param array $ids
     * @param array $ids
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTitlesById(array $ids): array;

    /**
     * 一括処理
     * @param string $method
     * @param array $ids
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function batch(string $method, array $ids): bool;

    /**
     * ウィジェットをアップデートする
     *
     * @param int $widgetAreaId
     * @param array $postData
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function updateWidget(int $widgetAreaId, array $postData);

    /**
     * ウィジェットの並べ替えを更新する
     *
     * @param int $widgetAreaId
     * @param array $postData
     * @return WidgetArea|EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function updateSort(int $widgetAreaId, array $postData);

    /**
     * ウィジェットを削除する
     *
     * @param int $widgetAreaId
     * @param int $widgetId
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function deleteWidget(int $widgetAreaId, int $widgetId);

    /**
     * リストを取得する
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getList(): array;

    /**
     * コントロールソース取得
     * @param string $field
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getControlSource(string $field): array;

}
