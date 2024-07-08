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
use Cake\Log\LogTrait;
use Exception;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Class BcFolder
 */
class BcFolder
{

    /**
     * Trait
     */
    use LogTrait;

    /**
     * パス
     * @var string
     */
    private string $path;

    /**
     * 権限
     * @var int
     */
    public int $mode = 0755;

    /**
     * Holds messages from last method.
     *
     * @var array
     */
    protected array $_messages = [];

    /**
     * Holds errors from last method.
     *
     * @var array
     */
    protected array $_errors = [];

    /**
     * Constructor
     * @param string $path
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct(string $path = '')
    {
        if(!$path) {
            // @deprecated 6.0.0 since 5.1.0 後方互換用
            $this->log(__d('baser_core', 'BcFile では、コンストラクタでパスの指定をしないのは非推奨です。パスの指定行うようにしてください。この要件はバージョン 6.0.0 で必須となります。'));
        }
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
     * ディレクトリを作成する
     * @param int $path
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(int|string $mask = 0777)
    {
        if(is_string($mask)) {
            // @deprecated 6.0.0 since 5.1.0 後方互換用
            $this->log(__d('baser_core', 'BcFile::create() では、第一引数にパスを指定するのは非推奨です。パスの指定はコンストラクタで行ってください。この要件はバージョン 6.0.0 で必須となります。'));
            $this->path = $mask;
            $mask = 0777;
        }
        $path = $this->path;
        $parent = dirname($path);
        if (!is_dir($parent)) {
            $this->path = $parent;
            if ($this->create($mask)) {
                $this->path = $path;
            } else {
                $this->path = $path;
                return false;
            }
        }
        if (!is_dir($this->path)) {
            return mkdir($this->path, $mask, true);
        }
        return true;
    }

    /**
     * ディレクトリ内のファイル一覧を取得する
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getFiles(array $options = [])
    {
        if (!is_dir($this->path)) return [];
        $options = array_merge([
            'exclude' => [],
            'full' => false
        ], $options);
        $files = [];
        $dir = new \DirectoryIterator($this->path);
        foreach($dir as $fileInfo) {
            $filename = $fileInfo->getFilename();
            if ($fileInfo->isFile() && !in_array($filename, $options['exclude'])) {
                $files[] = $options['full']? $fileInfo->getPathname() : $fileInfo->getFilename();;
            }
        }
        sort($files);
        return $files;
    }

    /**
     * ディレクトリ内のフォルダ一覧を取得する
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getFolders(array $options = [])
    {
        if (!is_dir($this->path)) return [];
        $options = array_merge([
            'exclude' => [],
            'full' => false
        ], $options);
        $folders = [];
        $dir = new \DirectoryIterator($this->path);
        /** @var $fileInfo \SplFileInfo */
        foreach($dir as $fileInfo) {
            $filename = $fileInfo->getFilename();
            if ($fileInfo->isDir() && !$fileInfo->isDot() && !in_array($filename, $options['exclude'])) {
                $folders[] = $options['full']? $fileInfo->getPathname() : $fileInfo->getFilename();
            }
        }
        sort($folders);
        return $folders;
    }

    /**
     * ディレクトリを再帰的に削除する
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete($path = '')
    {
        if($path) {
            // @deprecated 6.0.0 since 5.1.0 後方互換用
            $this->log(__d('baser_core', 'BcFile::delete() では、第一引数にパスを指定するのは非推奨です。パスの指定はコンストラクタで行ってください。この要件はバージョン 6.0.0 で必須となります。'));
            $this->path = $path;
        }
        if (!is_dir($this->path)) return false;
        $files = $this->getFiles(['full' => true]);
        foreach($files as $file) {
            unlink($file);
        }
        $folders = $this->getFolders(['full' => true]);
        $path = $this->path;
        foreach($folders as $folder) {
            $this->path = $folder;
            $this->delete();
        }
        $this->path = $path;
        rmdir($this->path);
        return true;
    }

    /**
     * ディレクトリをコピーする
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy($dest, $mode = 0777): bool
    {
        if(is_array($mode) && !empty($mode['from'])) {
            // @deprecated 6.0.0 since 5.1.0 後方互換用
            $this->log(__d('baser_core', 'BcFile::delete() では、第二引数の from キーにパスを指定するのは非推奨です。パスの指定はコンストラクタで行ってください。この要件はバージョン 6.0.0 で必須となります。'));
            $this->path = $mode['from'];
            $mode = 0777;
        }
        $source = $this->path;
        if (!is_dir($source)) return false;
        if (is_dir($source)) {
            $dir_handle = opendir($source);
            if (!file_exists($dest)) {
                (new BcFolder($dest))->create();
                chmod($dest, $mode);
            }
            while($file = readdir($dir_handle)) {
                if ($file != "." && $file != "..") {
                    if (is_dir($source . "/" . $file)) {
                        $this->path = $source . DS . $file;
                        self::copy($dest . DS . $file);
                    } else {
                        copy($source . "/" . $file, $dest . "/" . $file);
                        chmod($dest . "/" . $file, $mode);
                    }
                }
            }
            closedir($dir_handle);
        } else {
            copy($source, $dest);
            chmod($dest, $mode);
        }
        return true;
    }

    /**
     * ディレクトリを移動する
     * @param string $dest
     * @checked
     * @noTodo
     * @unitTest
     */
    public function move($dest, $options = []): bool
    {
        if(!empty($options['from'])) {
            // @deprecated 6.0.0 since 5.1.0 後方互換用
            $this->log(__d('baser_core', 'BcFile::delete() では、第二引数の from キーにパスを指定するのは非推奨です。パスの指定はコンストラクタで行ってください。この要件はバージョン 6.0.0 で必須となります。'));
            $this->path = $options['from'];
        }
        $source = $this->path;
        if (!is_dir($source)) return false;
        return $this->copy($dest) && $this->delete();
    }

    /**
     * ディレクトリ構造のモードを再帰的に変更します。これにはファイルのモードも変更することが含まれます。
     * @checked
     * @noTodo
     * @unitTest
     */
    public function chmod(?int $mode = null, bool $recursive = true, array $exceptions = []): bool
    {
        $path = $this->path;
        if (!$mode) {
            $mode = $this->mode;
        }

        if ($recursive === false && is_dir($path)) {
            // phpcs:disable
            if (@chmod($path, intval($mode, 8))) {
                // phpcs:enable
                $this->_messages[] = sprintf('%s changed to %s', $path, $mode);

                return true;
            }

            $this->_errors[] = sprintf('%s NOT changed to %s', $path, $mode);

            return false;
        }

        if (is_dir($path)) {
            $paths = $this->tree($path);

            foreach($paths as $type) {
                foreach($type as $fullpath) {
                    $check = explode(DIRECTORY_SEPARATOR, $fullpath);
                    $count = count($check);

                    if (in_array($check[$count - 1], $exceptions, true)) {
                        continue;
                    }

                    // phpcs:disable
                    if (@chmod($fullpath, intval($mode, 8))) {
                        // phpcs:enable
                        $this->_messages[] = sprintf('%s changed to %s', $fullpath, $mode);
                    } else {
                        $this->_errors[] = sprintf('%s NOT changed to %s', $fullpath, $mode);
                    }
                }
            }

            if (empty($this->_errors)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 各ディレクトリ内のネストされたディレクトリとファイルの配列を返す
     * @checked
     * @noTodo
     * @unitTest
     */
    public function tree(?string $path = null, $exceptions = false, ?string $type = null): array
    {
        if (!$path) {
            $path = $this->path;
        }
        $files = [];
        $directories = [$path];

        if (is_array($exceptions)) {
            $exceptions = array_flip($exceptions);
        }
        $skipHidden = false;
        if ($exceptions === true) {
            $skipHidden = true;
        } elseif (isset($exceptions['.'])) {
            $skipHidden = true;
            unset($exceptions['.']);
        }

        try {
            $directory = new RecursiveDirectoryIterator(
                $path,
                FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_SELF
            );
            $iterator = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::SELF_FIRST);
        } catch (Exception $e) {
            unset($directory, $iterator);

            if ($type === null) {
                return [[], []];
            }

            return [];
        }

        /**
         * @var string $itemPath
         * @var RecursiveDirectoryIterator $fsIterator
         */
        foreach($iterator as $itemPath => $fsIterator) {
            if ($skipHidden) {
                $subPathName = $fsIterator->getSubPathname();
                if ($subPathName[0] === '.' || str_contains($subPathName, DIRECTORY_SEPARATOR . '.')) {
                    unset($fsIterator);
                    continue;
                }
            }
            /** @var \FilesystemIterator $item */
            $item = $fsIterator->current();
            if (!empty($exceptions) && isset($exceptions[$item->getFilename()])) {
                unset($fsIterator, $item);
                continue;
            }

            if ($item->isFile()) {
                $files[] = $itemPath;
            } elseif ($item->isDir() && !$item->isDot()) {
                $directories[] = $itemPath;
            }

            // inner iterators need to be unset too in order for locks on parents to be released
            unset($fsIterator, $item);
        }

        // unsetting iterators helps to release possible locks in certain environments,
        // which could otherwise make `rmdir()` fail
        unset($directory, $iterator);

        if ($type === null) {
            return [$directories, $files];
        }
        if ($type === 'dir') {
            return $directories;
        }

        return $files;
    }

    public function find(string $regexpPattern = '.*'): array
    {
        $files = $this->getFiles();

        return array_values(preg_grep('/^' . $regexpPattern . '$/i', $files));
    }

    /**
     * フォルダ内のファイルとフォルダを読み込む
     *
     * @param mixed $sort 利用不可
     * @param bool $exceptions 利用不可
     * @param bool $fullPath
     * @return array
     * @deprecated 6.0.0 since 5.1.0 後方互換用
     */
    public function read($sort = true, $exceptions = false, $fullPath = false)
    {
        $files[0] = $this->getFolders(['full' => $fullPath]);
        $files[1] = $this->getFiles(['full' => $fullPath]);
        return $files;
    }

}
