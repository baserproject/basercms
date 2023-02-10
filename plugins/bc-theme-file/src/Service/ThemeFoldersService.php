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

namespace BcThemeFile\Service;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Error\BcException;
use BaserCore\Error\BcFormFailedException;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use BcThemeFile\Form\ThemeFolderForm;
use BcThemeFile\Model\Entity\ThemeFolder;
use Cake\Core\Plugin;
use Cake\Filesystem\Folder;

/**
 * ThemeFoldersService
 */
class ThemeFoldersService extends BcThemeFileService implements ThemeFoldersServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * テーマフォルダの初期データを取得する
     *
     * @param string $file
     * @return ThemeFolder
     * @checked
     * @noTodo
     */
    public function getNew(string $file)
    {
        return new ThemeFolder([
            'fullpath' => $file,
            'type' => 'folder',
        ], ['new' => true]);
    }

    /**
     * 単一データ取得
     * @param string $file
     * @return ThemeFolder
     * @checked
     * @noTodo
     */
    public function get(string $file)
    {
        return new ThemeFolder([
            'fullpath' => $file,
            'type' => 'folder'
        ]);
    }

    /**
     * 一覧データ取得
     *
     * @param array $params
     * @return array
     * @checked
     * @noTodo
     */
    public function getIndex(array $params)
    {
        $excludeFolderList = ['_notes'];
        $excludeFileList = [];
        if ($params['type'] === 'etc') {
            // レイアウト／エレメント以外のその他テンプレート
            $excludeFolderList = [];
            $excludeFileList = [
                'screenshot.png',
                'VERSION.txt',
                'config.php',
                'AppView.php',
                'BcAppView.php'
            ];
            if (!$params['path']) {
                $excludeFolderList = [
                    'layout',
                    'element',
                    'email',
                    'Helper',
                    'plugin',
                    'img',
                    'css',
                    'js',
                    '_notes'
                ];
            }
        }
        $folder = new Folder($params['fullpath']);
        $files = $folder->read(true, true);
        $themeFiles = [];
        $folders = [];
        foreach($files[0] as $file) {
            if (in_array($file, $excludeFolderList)) continue;
            $folders[] = $this->get($params['fullpath'] . $file);
        }
        $themeFilesService = $this->getService(ThemeFilesServiceInterface::class);
        foreach($files[1] as $file) {
            if (in_array($file, $excludeFileList)) continue;
            $themeFiles[] = $themeFilesService->get($params['fullpath'] . $file);
        }
        $themeFiles = array_merge($folders, $themeFiles);
        return $themeFiles;
    }

    /**
     * 作成
     *
     * @param array $postData
     * @return ThemeFolderForm
     * @checked
     * @noTodo
     */
    public function create(array $postData)
    {
        $postData['mode'] = 'create';
        $form = new ThemeFolderForm();
        if ($form->validate($postData)) {
            if ($form->execute($postData)) {
                $form->set('fullpath', $postData['fullpath'] . $postData['name']);
                return $form;
            } else {
                throw new BcException(__d('baser', 'フォルダの作成に失敗しました。書き込み権限に問題がある可能性があります。'));
            }
        } else {
            throw new BcFormFailedException($form, __d('baser', 'フォルダの作成に失敗しました。'));
        }
    }

    /**
     * 編集
     *
     * @param array $postData
     * @return ThemeFolderForm
     * @checked
     * @noTodo
     */
    public function update(array $postData)
    {
        $postData['mode'] = 'update';
        $form = new ThemeFolderForm();
        if ($form->validate($postData)) {
            if ($form->execute($postData)) {
                $form->set('fullpath', dirname($postData['fullpath']) . DS . $postData['name']);
                return $form;
            } else {
                throw new BcException(__d('baser', 'フォルダのリネームに失敗しました。書き込み権限に問題がある可能性があります。'));
            }
        } else {
            throw new BcFormFailedException($form, __d('baser', 'フォルダのリネームに失敗しました。'));
        }
    }

    /**
     * 削除
     *
     * @param string $fullpath
     * @return bool
     * @checked
     * @noTodo
     */
    public function delete(string $fullpath)
    {
        if (is_dir($fullpath)) {
            $folder = new Folder();
            return $folder->delete($fullpath);
        } else {
            return false;
        }
    }

    /**
     * コピー
     *
     * @param string $fullpath
     * @return ThemeFolder|false
     * @checked
     * @noTodo
     */
    public function copy(string $fullpath)
    {
        $fullpath = preg_replace('/\/$/is', '', $fullpath);
        $newPath = $fullpath . '_copy';
        while(true) {
            if (!is_dir($newPath)) {
                $newEntity = $this->get($newPath);
                break;
            }
            $newPath .= '_copy';
        }
        $folder = new Folder();
        $result = $folder->copy($newPath, [
            'from' => $fullpath,
            'chmod' => 0777,
            'skip' => ['_notes'
        ]]);
        $folder = null;
        if ($result) {
            return $newEntity;
        } else {
            return false;
        }
    }

    /**
     * 一括処理
     *
     * @param string $method
     * @param array $paths
     * @return bool
     * @checked
     * @noTodo
     */
    public function batch(string $method, array $paths): bool
    {
        if (!$paths) return true;
        $themeFilesService = $this->getService(ThemeFilesServiceInterface::class);
        foreach($paths as $path) {
            if(is_dir($path)) {
                $service = $this;
            } else {
                $service = $themeFilesService;
            }
            if (!$service->$method($path)) {
                throw new BcException(__d('baser', 'エラーが発生しました。書き込み権限に問題がある可能性があります。'));
            }
        }
        return true;
    }

    /**
     * 複数のフルパスからフォルダ名、ファイル名を取得する
     *
     * @param array $paths
     * @return array|bool
     * @checked
     * @noTodo
     */
    public function getNamesByFullpath(array $paths)
    {
        if (!$paths) return true;
        $themeFilesService = $this->getService(ThemeFilesServiceInterface::class);
        $names = [];
        foreach($paths as $path) {
            if(is_dir($path)) {
                $service = $this;
            } else {
                $service = $themeFilesService;
            }
            $entity = $service->get($path);
            $names[] = $entity->name;
        }
        return $names;
    }

    /**
     * 現在のテーマにフォルダをコピー
     *
     * @param array $params
     * @return array|false|string|string[]
     * @checked
     * @noTodo
     */
    public function copyToTheme(array $params)
    {
       $theme = BcUtil::getCurrentTheme();
        if ($params['type'] !== 'etc') {
            if ($params['plugin'] && $params['assets']) {
                // TODO ucmitz 未検証
                $themePath = Plugin::path($theme) . $params['plugin'] . DS . $params['type'] . DS;
            } else {
                $themePath = Plugin::templatePath($theme) . $params['type'] . DS;
            }
            if ($params['path']) {
                $themePath .= $params['path'] . DS;
            }
        } else {
            $themePath = Plugin::templatePath($theme) . $params['path'] . DS;
        }
        $folder = new Folder();
        $folder->create(dirname($themePath), 0777);
        if ($folder->copy($themePath, ['from' => $params['fullpath'], 'chmod' => 0777, 'skip' => ['_notes']])) {
            return str_replace(ROOT, '', $themePath);
        } else {
            return false;
        }
    }

    /**
     * フォームフォルダを取得する
     *
     * @param array $data
     * @return ThemeFolderForm
     * @checked
     * @noTodo
     */
    public function getForm(array $data)
    {
        return (new ThemeFolderForm())->setData($data);
    }
}
