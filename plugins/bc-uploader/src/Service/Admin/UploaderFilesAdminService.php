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

/**
 * UploaderFilesAdminService
 * @property UploaderFilesTable $UploaderFiles
 */
class UploaderFilesAdminService extends UploaderFilesService implements UploaderFilesAdminServiceInterface
{

    /**
     * 一覧用の View 変数を取得する
     *
     * @return array
     * @checked
     * @noTodo
     */
    public function getViewVarsForIndex()
    {
        return [
            'uploaderConfigs' => $this->uploaderConfigsService->get(),
            'installMessage' => $this->checkInstall()
        ];
    }

    /**
     * Ajaxで取得する一覧用の View 変数を取得する
     * @param $entities
     * @param $listId
     * @return array
     * @checked
     * @noTodo
     */
    public function getViewVarsForAjaxList($entities, $listId)
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
     * @return    string    インストールメッセージ
     */
    protected function checkInstall()
    {
        // TODO ucmitz 未実装
        return '';
        // インストール確認
        $installMessage = '';
        $viewFilesPath = str_replace(ROOT, '', WWW_ROOT) . 'files';
        $viewSavePath = $viewFilesPath . DS . $this->UploaderFiles->actsAs['BcUpload']['saveDir'];
        $filesPath = WWW_ROOT . 'files';
        $savePath = $filesPath . DS . $this->UploaderFiles->actsAs['BcUpload']['saveDir'];
        if (!is_dir($savePath)) {
            $ret = mkdir($savePath, 0777);
            if (!$ret) {
                if (is_writable($filesPath)) {
                    $installMessage = sprintf(__d('baser', '%sを作成し、書き込み権限を与えてください'), $viewSavePath);
                } else {
                    if (!is_dir($filesPath)) {
                        $installMessage = sprintf(__d('baser', '作成し、%sに書き込み権限を与えてください'), $viewFilesPath);
                    } else {
                        $installMessage = sprintf(__d('baser', '%sに書き込み権限を与えてください'), $viewFilesPath);
                    }
                }
            }
        } else {
            if (!is_writable($savePath)) {
                $installMessage = sprintf(__d('baser', '%sに書き込み権限を与えてください'), $viewSavePath);
            }
        }
        return $installMessage;
    }

}
