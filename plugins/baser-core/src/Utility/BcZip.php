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
     * @unitTest
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
     * @unitTest
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
            $extractedPath = rtrim($target, "/\\") . DS . $this->topArchiveName;
            $Folder = new BcFolder($extractedPath);
            $Folder->chmod( 0777);
            if ($this->Zip) $this->Zip->close();
            return $this->topArchiveName;
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
     * @unitTest
     */
    protected function _extractByPhpLib($source, $target)
    {
        if ($this->Zip->open($source) !== true) {
            return false;
        }
        $targetPath = $this->_normalizeTargetPath($target);
        if ($targetPath === false) {
            $this->Zip->close();
            return false;
        }
        if (!$this->_validateZipEntries($targetPath)) {
            $this->Zip->close();
            return false;
        }
        if ($this->Zip->extractTo($target)) {
            $archivePath = $this->Zip->getNameIndex(0);
            $archivePathAry = explode('/', $archivePath);
            $this->topArchiveName = $archivePathAry[0];
            return true;
        }
        return false;
    }

    /**
     * コマンドによる展開
     *
     * @param $source
     * @param $target
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _extractByCommand($source, $target)
    {
        exec('which unzip', $return1);
        if (empty($return1[0])) {
            return false;
        }
        $unzipCommand = $return1[0];
        $targetPath = $this->_normalizeTargetPath($target);
        if ($targetPath === false) {
            return false;
        }
        $listCommand = $unzipCommand . ' -Z -1 ' . $this->_escapePath($source);
        exec($listCommand . ' 2>&1', $entries, $listStatus);
        if ($listStatus !== 0) {
            $this->error = $entries;
            return false;
        }
        foreach ($entries as $entry) {
            $entry = trim($entry);
            if ($entry === '') {
                continue;
            }
            if (!$this->_isZipEntrySafe($entry, $targetPath)) {
                $this->error = 'Invalid zip entry path: ' . $entry;
                return false;
            }
        }
        $target = rtrim($target, "/\\");
        $command = $unzipCommand . ' -o ' . $this->_escapePath($source) . ' -d ' . $this->_escapePath($target);
        exec($command . ' 2>&1', $return2);
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
     * @unitTest
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
     * ZIPエントリがターゲット配下か確認する
     *
     * @param string $entry
     * @param string $targetPath
     * @return bool
     */
    protected function _isZipEntrySafe($entry, $targetPath)
    {
        $entry = str_replace('\\', '/', $entry);
        if ($entry === '' || strpos($entry, "\0") !== false) {
            return false;
        }
        if (preg_match('/^[A-Za-z]:\//', $entry) || strpos($entry, '/') === 0) {
            return false;
        }
        $normalizedEntry = $this->_normalizeRelativePath($entry);
        if ($normalizedEntry === null || $normalizedEntry === '') {
            return false;
        }
        $destPath = $this->_normalizeAbsolutePath($targetPath . '/' . $normalizedEntry);
        if ($destPath === '') {
            return false;
        }
        $comparisonDest = $destPath . '/';
        $comparisonTarget = $targetPath . '/';
        if (DIRECTORY_SEPARATOR === '\\') {
            // Windows 互換性のため、大文字小文字を無視して比較する。
            $comparisonDest = strtolower($comparisonDest);
            $comparisonTarget = strtolower($comparisonTarget);
        }
        // 展開先がターゲット配下に収まることを保証する。
        return (strpos($comparisonDest, $comparisonTarget) === 0);
    }

    /**
     * ZIPエントリ一覧を検証する
     *
     * @param string $targetPath
     * @return bool
     */
    protected function _validateZipEntries($targetPath)
    {
        for ($i = 0; $i < $this->Zip->numFiles; $i++) {
            $entry = $this->Zip->getNameIndex($i);
            if ($entry === false || !$this->_isZipEntrySafe($entry, $targetPath)) {
                $this->error = 'Invalid zip entry path: ' . (string)$entry;
                return false;
            }
        }
        return true;
    }

    /**
     * 展開先ディレクトリの正規化
     *
     * 展開前検証用に、存在しない場合は親ディレクトリで解決して基準パスを作る。
     *
     * @param string $target
     * @return string|false
     */
    protected function _normalizeTargetPath($target)
    {
        $trimmedTarget = rtrim($target, "/\\");
        if ($trimmedTarget === '' && ($target === '/' || $target === '\\')) {
            $trimmedTarget = $target;
        }
        if ($trimmedTarget === '') {
            $this->error = 'Target directory not found.';
            return false;
        }
        $targetPath = realpath($trimmedTarget);
        if ($targetPath === false) {
            // ディレクトリ作成は行わず、比較用の基準パスのみ構成する。
            $parentPath = realpath(dirname($trimmedTarget));
            if ($parentPath === false || !is_dir($parentPath)) {
                $this->error = 'Target directory not found.';
                return false;
            }
            $targetPath = $parentPath . '/' . basename($trimmedTarget);
        }
        return rtrim(str_replace('\\', '/', $targetPath), '/');
    }

    /**
     * 相対パスを正規化する
     *
     * @param string $path
     * @return string|null
     */
    protected function _normalizeRelativePath($path)
    {
        $path = str_replace('\\', '/', $path);
        $parts = [];
        foreach (explode('/', $path) as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }
            if ($part === '..') {
                if (empty($parts)) {
                    return null;
                }
                array_pop($parts);
                continue;
            }
            $parts[] = $part;
        }
        return implode('/', $parts);
    }

    /**
     * 絶対パスを正規化する
     *
     * @param string $path
     * @return string
     */
    protected function _normalizeAbsolutePath($path)
    {
        // ZIP 内のエントリはまだ実在しないため、realpath() は使えない。
        $path = str_replace('\\', '/', $path);
        $drive = '';
        if (preg_match('/^[A-Za-z]:/', $path)) {
            $drive = strtoupper($path[0]) . ':';
            $path = substr($path, 2);
        }
        $parts = [];
        foreach (explode('/', $path) as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }
            if ($part === '..') {
                if (!empty($parts)) {
                    array_pop($parts);
                }
                continue;
            }
            $parts[] = $part;
        }
        $normalized = implode('/', $parts);
        if ($drive !== '') {
            return $drive . '/' . $normalized;
        }
        return '/' . $normalized;
    }

    /**
     * zip生成
     *
     * @param string $sorce 元データ
     * @param string $dist 出力先
     * @return void
     * @checked
     * @noTodo
     * @unitTest
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
     * @unitTest
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
