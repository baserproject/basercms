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

namespace BcUploader\Service\Admin;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BcUploader\Model\Table\UploaderFilesTable;
use BcUploader\Service\UploaderFilesService;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\ORM\ResultSet;

/**
 * UploaderFilesAdminService
 * @property UploaderFilesTable $UploaderFiles
 */
class UploaderFilesAdminService extends UploaderFilesService implements UploaderFilesAdminServiceInterface
{

    /**
     * 一覧用の View 変数を取得する
     *
     * @param int|null $id
     * @return array
     * @checked
     * @noTodo
     */
    public function getViewVarsForIndex(int $id = null)
    {
        return [
            'listId' => $id,
            'isAjax' => $id? true : false,
            'uploaderConfigs' => $this->uploaderConfigsService->get(),
            'installMessage' => $this->checkInstall()
        ];
    }

    /**
     * Ajaxで取得する一覧用の View 変数を取得する
     * @param ResultSet $entities
     * @param int $listId
     * @return array
     * @checked
     * @noTodo
     */
    public function getViewVarsForAjaxList(ResultSet $entities, int $listId = null)
    {
        $uploaderConfig = $this->uploaderConfigsService->get();
        return [
            'listId' => $listId,
            'layoutType' => $uploaderConfig->layout_type,
            'uploaderFiles' => $entities,
            'installMessage' => $this->checkInstall()
        ];
    }

    /**
     * インストール状態の確認
     *
     * @return string インストールメッセージ
     * @checked
     * @noTodo
     */
    protected function checkInstall()
    {
        // インストール確認
        $installMessage = '';
        $viewFilesPath = str_replace(ROOT, '', WWW_ROOT) . 'files';
        $saveDir = $this->UploaderFiles->getSettings()['saveDir'];
        $viewSavePath = $viewFilesPath . DS . $saveDir;
        $filesPath = WWW_ROOT . 'files';
        $savePath = $filesPath . DS . $saveDir;
        $limitedPath = $savePath . DS . 'limited';
        $viewLimitedPath = $viewSavePath . DS . 'limited';

        if (!is_dir($limitedPath)) {
            $folder = new Folder();
            $folder->create($limitedPath, 0777);
            if (!is_dir($limitedPath)) {
                if (is_writable($filesPath)) {
                    $installMessage = sprintf(__d('baser', '%sを作成し、書き込み権限を与えてください'), $viewSavePath);
                } elseif (!is_dir($filesPath)) {
                    $installMessage = sprintf(__d('baser', '作成し、%sに書き込み権限を与えてください'), $viewFilesPath);
                } else {
                    $installMessage = sprintf(__d('baser', '%sに書き込み権限を与えてください'), $viewFilesPath);
                }
            } else {
                $File = new File($limitedPath . DS . '.htaccess');
                $htaccess = "Order allow,deny\nDeny from all";
                $File->write($htaccess);
                $File->close();
                if (!file_exists($limitedPath . DS . '.htaccess')) {
                    $installMessage = __d('baser', '現在、アップロードファイルの公開期間の指定ができません。' .
                        '指定できるようにするには、{0} に書き込み権限を与えてください。', $viewLimitedPath);
                }
            }
        } else {
            if (!is_writable($savePath)) {
                $installMessage = sprintf(__d('baser', '%sに書き込み権限を与えてください'), $viewSavePath);
            }
        }
        return $installMessage;
    }

    /**
     * アップロードファイル編集用のポップアップ画面で呼び出す画像用の View 変数を取得する
     * @param string $name
     * @param string $size
     * @return array
     * @checked
     * @noTodo
     */
    public function getViewVarsForAjaxImage(string $name, string $size)
    {
        return [
            'uploaderFile' => $this->UploaderFiles->findByName(rawurldecode($name))->first(),
            'size' => $size
        ];
    }

}
