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

namespace BcUploader\Service;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Datasource\EntityInterface;

/**
 * UploaderFilesServiceInterface
 */
interface UploaderFilesServiceInterface
{

    /**
     * コントロールソースを取得する
     *
     * @param string $field フィールド名
     * @param array $options
     * @return    mixed    $controlSource    コントロールソース
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getControlSource($field = null, $options = []);

    /**
     * アップロードファイル名から既存のエンティティを取得する
     *
     * @param string $name
     * @return EntityInterface
     */
    public function getByName(string $name): EntityInterface;

    /**
     * ファイル名から実ファイルが存在するかどうかを取得する
     * @param string $name
     * @return array|false
     */
    public function filesExistsByName(string $name);

}
