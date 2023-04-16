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

namespace BcThemeFile\Service\Admin;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BcThemeFile\Form\ThemeFolderForm;
use BcThemeFile\Model\Entity\ThemeFolder;
use Cake\Utility\Inflector;

/**
 * ThemeFoldersAdminServiceInterface
 */
interface ThemeFoldersAdminServiceInterface
{

    /**
     * フォルダ編集画面用の View 変数を取得する
     *
     * @param ThemeFolder $entity
     * @param ThemeFolderForm $form
     * @param array $args
     * @return array
     * @noTodo
     * @checked
     * @unitTest
     */
    public function getViewVarsForEdit(ThemeFolder $entity, ThemeFolderForm $form, array $args);

    /**
     * フォルダ新規登録画面用の View 変数を取得する
     *
     * @param ThemeFolder $entity
     * @param ThemeFolderForm $form
     * @param array $args
     * @return array
     * @noTodo
     * @checked
     * @unitTest
     */
    public function getViewVarsForAdd(ThemeFolder $entity, ThemeFolderForm $form, array $args);

    /**
     * フォルダ表示画面用の View 変数を取得する
     *
     * @param ThemeFolder $entity
     * @param ThemeFolderForm $form
     * @param array $args
     * @return array
     * @noTodo
     * @checked
     * @unitTest
     */
    public function getViewVarsForView(ThemeFolder $entity, ThemeFolderForm $form, array $args);

}
