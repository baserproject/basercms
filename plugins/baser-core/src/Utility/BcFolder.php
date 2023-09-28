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

/**
 * Class BcFolder
 */
class BcFolder
{

    /**
     * パス
     * @var string
     */
    private $path;

    /**
     * Constructor
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * ディレクトリ内のファイル一覧を取得する
     * @return array
     */
    public function getFiles($options = [])
    {
        $options = array_merge([
            'exclude' => [],
            'full' => false
        ], $options);
        $files = [];
        $dir = new \DirectoryIterator($this->path);
        foreach ($dir as $fileInfo) {
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
     */
    public function getFolders($options = [])
    {
        $options = array_merge([
            'exclude' => [],
            'full' => false
        ], $options);
        $folders = [];
        $dir = new \DirectoryIterator($this->path);
        /** @var $fileInfo \SplFileInfo */
        foreach ($dir as $fileInfo) {
            $filename = $fileInfo->getFilename();
            if ($fileInfo->isDir() && !$fileInfo->isDot() && !in_array($filename, $options['exclude'])) {
                $folders[] = $options['full']? $fileInfo->getPathname() : $fileInfo->getFilename();
            }
        }
        sort($folders);
        return $folders;
    }

}
