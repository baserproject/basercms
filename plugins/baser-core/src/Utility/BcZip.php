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

namespace BaserCore\Utility;

use Cake\Filesystem\Folder;
use ZipArchive;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class BcZip
 */
class BcZip
{
    /**
     * ZipArchive
     *
     * @var bool|ZipArchive
     */
    public $Zip = false;

    /**
     * error
     *
     * @var null
     */
    public $error = null;

    /**
     * Top Archive Name
     *
     * @var null
     */
    public $topArchiveName = null;

    /**
     * BcZip constructor.
     * @checked
     * @noTodo
     */
    public function __construct()
    {
        if (class_exists('ZipArchive')) {
            $this->Zip = new ZipArchive();
        }
    }

    /**
     * ZIP を展開する
     *
     * @param $source
     * @param $target
     * @return bool
     * @checked
     * @noTodo
     */
    public function extract($source, $target)
    {
        $this->error = null;
        $this->topArchiveName = null;
        if ($this->Zip) {
            $result = $this->_extractByPhpLib($source, $target);
        } else {
            $result = $this->_extractByCommand($source, $target);
        }
        if ($result) {
            $extractedPath = $target . $this->topArchiveName;
            $Folder = new Folder();
            $Folder->chmod($extractedPath, 0777);
            if ($this->Zip) $this->Zip->close();
            return true;
        } else {
            return false;
        }
    }

    /**
     * ZipArchive クラスによる展開
     *
     * @param $source
     * @param $target
     * @return bool
     * @checked
     * @noTodo
     */
    protected function _extractByPhpLib($source, $target)
    {
        if ($this->Zip->open($source) === true && $this->Zip->extractTo($target)) {
            $archivePath = $this->Zip->getNameIndex(0);
            $archivePathAry = explode('/', $archivePath);
            $this->topArchiveName = $archivePathAry[0];
            return true;
        } else {
            return false;
        }
    }

    /**
     * コマンドによる展開
     *
     * @param $source
     * @param $target
     * @return bool
     * @checked
     * @noTodo
     */
    protected function _extractByCommand($source, $target)
    {
        exec('which unzip', $return1);
        if (empty($return1[0])) {
            return false;
        }
        $unzipCommand = $return1[0];
        $target = preg_replace('/\/$/', '', $target);
        $command = $unzipCommand . ' -o ' . $this->_escapePath($source) . ' -d ' . $this->_escapePath($target);
        exec($command, $return2);
        if (!empty($return2[2])) {
            $path = str_replace('  inflating: ' . $target, '', $return2[2]);
            $path = preg_replace('/^\//', '', $path);
            $pathAry = explode(DS, $path);
            $this->topArchiveName = $pathAry[0];
            return true;
        } else {
            exec($unzipCommand . ' 2>&1', $errs);
            $this->error = $errs;
            return false;
        }
    }

    /**
     * CUI 向けにパスをエスケープする
     *
     * @param $path
     * @return string
     * @checked
     * @noTodo
     */
    protected function _escapePath($path)
    {
        $pathAry = explode(DS, $path);
        foreach($pathAry as $key => $value) {
            $pathAry[$key] = escapeshellarg($value);
        }
        return implode(DS, $pathAry);
    }

    /**
     * zip生成
     *
     * @param string $sorce 元データ
     * @param string $dist 出力先
     * @return void
     * @checked
     * @noTodo
     */
    public function create($sorce, $dist)
    {
        $za = new \ZipArchive();
        $za->open($dist, \ZIPARCHIVE::CREATE);
        $this->zipSub($za, $sorce);
        $za->close();
    }

    /**
     * 再帰的にzip生成対象ファイルを追加する
     *
     * @param ZipArchive $za
     * @param string $path
     * @param string $parentPath
     * @return void
     * @checked
     * @noTodo
     */
    private function zipSub($za, $path, $parentPath = '')
    {
        $dh = opendir($path);
        while(($entry = readdir($dh)) !== false) {
            if ($entry == '.' || $entry == '..') {
            } else {
                $localPath = $parentPath . $entry;
                $fullpath = $path . DS . $entry;
                if (is_file($fullpath)) {
                    $za->addFile($fullpath, $localPath);
                } else if (is_dir($fullpath)) {
                    $za->addEmptyDir($localPath);
                    $this->zipSub($za, $fullpath, $localPath . DS);
                }
            }
        }
        closedir($dh);
    }


}
