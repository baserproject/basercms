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

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

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
        if (!$this->checkParentFolder()) return false;
        if (!is_file($this->path)) return touch($this->path);
        return true;
    }

    /**
     * 親フォルダが存在するかチェックし、存在しない場合は作成する
     * @return bool
     */
    private function checkParentFolder()
    {
        $parent = dirname($this->path);
        if (!is_dir($parent)) {
            $folder = new BcFolder($parent);
            if(!$folder->create()) {
                return false;
            }
        }
        return true;
    }

    /**
     * ファイルを読み込む
     * @return false|string
     */
    public function read()
    {
        if(!is_file($this->path)) {
            return false;
        }
        return file_get_contents($this->path);
    }

    /**
     * ファイルを書き込む
     * @param $data
     * @return bool
     */
    public function write($data)
    {
        if (!$this->checkParentFolder()) return false;
        return (bool) file_put_contents($this->path, $data);
    }

    /**
     * ファイルを削除
     * @return bool
     */
    public function delete()
    {
        if(!is_file($this->path)) {
            return false;
        }
        return unlink($this->path);
    }

    /**
     * ファイルのサイズを取得
     * @return bool
     */
    public function size()
    {
        if (is_file($this->path)) {
            return filesize($this->path);
        }
        return false;
    }

    /**
     * close
     * @return void
     * @deprecated 6.0.0 since 5.1.0 後方互換用に配置
     */
    public function close()
    {}

}
