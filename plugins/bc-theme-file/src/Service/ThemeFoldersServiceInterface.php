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

namespace BcThemeFile\Service;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BcThemeFile\Form\ThemeFolderForm;
use BcThemeFile\Model\Entity\ThemeFolder;

/**
 * ThemeFoldersServiceInterface
 */
interface ThemeFoldersServiceInterface
{

    /**
     * テーマフォルダの初期データを取得する
     *
     * @param string $file
     * @return ThemeFolder
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew(string $file);

    /**
     * 単一データ取得
     *
     * @param string $file
     * @return ThemeFolder
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get(string $file);

    /**
     * 一覧データ取得
     *
     * @param array $params
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(array $params);

    /**
     * 作成
     *
     * @param array $postData
     * @return ThemeFolderForm
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $postData);

    /**
     * 編集
     *
     * @param array $postData
     * @return ThemeFolderForm
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(array $postData);

    /**
     * 削除
     *
     * @param string $fullpath
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(string $fullpath);

    /**
     * コピー
     *
     * @param string $fullpath
     * @return ThemeFolder|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy(string $fullpath);

    /**
     * 一括処理
     *
     * @param string $method
     * @param array $paths
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function batch(string $method, array $paths): bool;

    /**
     * 複数のフルパスからフォルダ名、ファイル名を取得する
     *
     * @param array $paths
     * @return array|bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNamesByFullpath(array $paths);

    /**
     * 現在のテーマにフォルダをコピー
     *
     * @param array $params
     * @return array|false|string|string[]
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copyToTheme(array $params);

    /**
     * フォームフォルダを取得する
     *
     * @param array $data
     * @return ThemeFolderForm
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getForm(array $data);

}
