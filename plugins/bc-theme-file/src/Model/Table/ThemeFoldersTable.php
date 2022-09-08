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

namespace BcThemeFile\Model\Table;

use BaserCore\Model\Table\AppTable;

/**
 * Class ThemeFolder
 *
 * テーマフォルダモデル
 *
 * @package Baser.Model
 */
class ThemeFoldersTable extends AppTable
{

    /**
     * クラス名
     *
     * @var string
     */
    public $name = 'ThemeFolder';

    /**
     * use table
     *
     * @var boolean
     */
    public $useTable = false;

    /**
     * ThemeFolder constructor.
     *
     * @param bool $id
     * @param null $table
     * @param null $ds
     */
    public function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->validate = [
            'name' => [
                ['rule' => ['notBlank'], 'message' => __d('baser', 'テーマフォルダ名を入力してください。'), 'required' => true],
                ['rule' => ['halfText'], 'message' => __d('baser', 'テーマフォルダ名は半角のみで入力してください。')],
                ['rule' => ['duplicateThemeFolder'], 'on' => 'create', 'message' => __d('baser', '入力されたテーマフォルダ名は、同一階層に既に存在します。')]]
        ];
    }

    /**
     * フォルダの重複チェック
     *
     * @param array $check
     * @return boolean
     */
    public function duplicateThemeFolder($check)
    {
        if (!$check[key($check)]) {
            return true;
        }
        if ($check[key($check)] == $this->data['ThemeFolder']['pastname']) {
            return true;
        }
        $targetPath = $this->data['ThemeFolder']['parent'] . $check[key($check)];
        if (is_dir($targetPath)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * データの存在確認
     * validates の、on オプションを動作する為に定義
     * @param int $id
     * @return bool
     */
    public function exists($conditions): bool
    {
        $data = $this->data['ThemeFolder'];
        if (empty($data['parent']) || empty($data['name'])) {
            return false;
        }
        return (is_dir($data['parent'] . $data['name']) && $this->id !== false);
    }

}
