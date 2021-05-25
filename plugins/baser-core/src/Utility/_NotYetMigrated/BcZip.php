<?php
// TODO : コード確認要
return;
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Vendor
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

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
     */
    protected function _extractByPhpLib($source, $target)
    {
        if ($this->Zip->open($source) === true && $this->Zip->extractTo($target)) {
            $archivePath = $this->Zip->getNameIndex(0);
            $archivePathAry = explode(DS, $archivePath);
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
     * Destruct
     */
    public function __destruct()
    {
        if (class_exists('ZipArchive')) {
            $this->Zip->close();
        }
    }

    /**
     * CUI 向けにパスをエスケープする
     *
     * @param $path
     * @return string
     */
    protected function _escapePath($path)
    {
        $pathAry = explode(DS, $path);
        foreach($pathAry as $key => $value) {
            $pathAry[$key] = escapeshellarg($value);
        }
        return implode(DS, $pathAry);
    }

}
