<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.5
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Utility;

class BcFile
{

    /**
     * パス
     * @var string
     */
    private string $path;

    /**
     * Constructor
     * @param string $path
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * パスを取得する
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * ファイルを作成する
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create()
    {
        $path = $this->path;
        $parent = dirname($path);
        if (!is_dir($parent)) {
            $folder = new BcFolder($parent);
            if(!$folder->create()) {
                return false;
            }
        }
        if (!is_file($this->path)) {
            return touch($this->path);
        }
        return true;
    }
}
