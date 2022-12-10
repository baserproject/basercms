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

namespace BcUploader\Controller\Admin;

use BaserCore\Controller\Admin\BcAdminAppController;
use BaserCore\Utility\BcSiteConfig;
use BcUploader\Service\Admin\UploaderFilesAdminServiceInterface;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * ファイルアップローダーコントローラー
 */
class UploaderFilesController extends BcAdminAppController
{

    public function beforeFilter(EventInterface $event)
    {
        // TODO ucmitz 未実装
//        $this->BcAuth->allow('view_limited_file');
//        $this->_checkEnv();
        parent::beforeFilter($event);
    }

    /**
     * プラグインの環境をチェックする
     */
    protected function _checkEnv()
    {
        $savePath = WWW_ROOT . 'files' . DS . $this->UploaderFile->actsAs['BcUpload']['saveDir'] . DS;
        if (!is_dir($savePath . 'limited')) {
            $Folder = new Folder();
            $Folder->create($savePath . 'limited', 0777);
            if (!is_dir($savePath . 'limited')) {
                $this->BcMessage->setError('現在、アップロードファイルの公開期間の指定ができません。指定できるようにするには、' . $savePath . ' に書き込み権限を与えてください。');
            }
            $File = new File($savePath . 'limited' . DS . '.htaccess');
            $htaccess = "Order allow,deny\nDeny from all";
            $File->write($htaccess);
            $File->close();
            if (!file_exists($savePath . 'limited' . DS . '.htaccess')) {
                $this->BcMessage->setError('現在、アップロードファイルの公開期間の指定ができません。指定できるようにするには、' . $savePath . 'limited/ に書き込み権限を与えてください。');
            }
        }
    }

    /**
     * [ADMIN] ファイル一覧
     *
     * @param int $id 呼び出し元 識別ID
     * @param string $filter
     * @return void
     * @checked
     * @noTodo
     */
    public function index(UploaderFilesAdminServiceInterface $service)
    {
        $this->setViewConditions('UploadFile', [
            'default' => [
                'query' => [
                    'num' => BcSiteConfig::get('admin_list_num')
                ]]]);
        $this->set($service->getViewVarsForIndex());
    }

    public function ajax_index($id)
    {
        // TODO ucmitz index メソッドから Ajaxリクエストの部分だけを切り離した。
        // 必要かどうか確認要
        $settings = $this->UploaderFile->getBehavior('BcUpload')->BcUpload['UploaderFile']->settings;
        $this->set('listId', $id);
        $this->set('imageSettings', $settings['UploaderFile']['fields']['name']['imagecopy']);
    }

    /**
     * [ADMIN] ファイル一覧を表示
     *
     * ファイルアップロード時にリダイレクトされた場合、
     * RequestHandlerコンポーネントが作動しないので明示的に
     * レイアウト、デバッグフラグの設定をする
     *
     * @param int $id 呼び出し元 識別ID
     * @param string $filter
     * @return    void
     */
    public function ajax_list(UploaderFilesAdminServiceInterface $service, $id = '')
    {
        $this->viewBuilder()->disableAutoLayout();
        Configure::write('debug', 0);
        $this->setViewConditions('UploadFile', [
            'default' => [
                'query' => [
                    'num' => $this->siteConfigs['admin_list_num']
                ]], 'type' => 'get']);

        // TODO ucmitz 未実装
//        if (empty($this->request->getData('Filter.uploader_type'))) {
//            $this->request = $this->request->withData('Filter.uploader_type', 'all');
//        }
//        if (!empty($this->request->getData('Filter.name'))) {
//            $this->request = $this->request->withData('Filter.name', rawurldecode($this->request->getData('Filter.name')));
//        }
        $this->set(
            $service->getViewVarsForAjaxList(
                $this->paginate($service->getIndex($this->getRequest()->getQueryParams())),
                $id
            )
        );
        // TODO ucmitz 暫定措置
        $this->viewBuilder()->setHelpers(['BcUploader.Uploader']);
    }

    /**
     * [ADMIN] サイズを指定して画像タグを取得する
     *
     * @param string $name
     * @param string $size
     * @return    void
     * @access    public
     */
    public function ajax_image($name, $size = 'small')
    {

        $file = $this->UploaderFile->findByName(rawurldecode($name));
        $this->set('file', $file);
        $this->set('size', $size);
    }

    /**
     * [ADMIN] 各サイズごとの画像の存在チェックを行う
     *
     * @param string $name
     * @return    void
     * @access    public
     */
    public function ajax_exists_images($name)
    {

        Configure::write('debug', 0);
        $this->RequestHandler->setContent('json');
        $this->RequestHandler->respondAs('application/json; charset=UTF-8');
        $files = $this->UploaderFile->filesExists($name);
        $this->set('result', $files);
        $this->render('json_result');
    }

    /**
     * [ADMIN] 編集処理
     *
     * @return    mixed
     */
    public function edit($id = null)
    {
        $this->autoRender = false;
        if (!$this->request->getData() && $this->request->is('ajax')) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        } elseif (!$this->request->is('ajax') && !$id) {
            $this->notFound();
        }

        $user = $this->BcAuth->user();
        $uploaderConfig = $this->UploaderConfig->findExpanded();
        if ($uploaderConfig['use_permission']) {
            if ($user['user_group_id'] != 1 && $this->request->getData('UploaderFile.user_id') != $user['id']) {
                $this->notFound();
            }
        }

        if (!$this->request->getData()) {
            $this->request = $this->request->withData('UploadFile', $this->UploaderFile->read(null, $id));
        } else {
            $this->UploaderFile->set($this->request->getData());
            $result = $this->UploaderFile->save();
            if ($this->request->is('ajax')) {
                if ($result) {
                    return true;
                } else {
                    return false;
                }
            } else {
                if ($result) {
                    $this->BcMessage->setInfo(__d('baser', 'ファイルの内容を保存しました。'));
                    $this->redirect(['action' => 'index']);
                } else {
                    $this->BcMessage->setInfo(__d('baser', '保存中にエラーが発生しました。'));
                }
            }
        }

        $this->render('../Elements/admin/uploader_files/form');
    }

    /**
     * [ADMIN] 削除処理
     *
     * @return    void
     * @access    public
     */
    public function delete($id)
    {
        $this->_checkSubmitToken();
        if (!$id) {
            $this->notFound();
        }

        $user = $this->BcAuth->user();
        $uploaderConfig = $this->UploaderConfig->findExpanded();
        $uploaderFile = $this->UploaderFile->read(null, $id);

        if (!$uploaderFile) {
            $this->notFound();
        }

        if ($uploaderConfig['use_permission']) {
            if ($user['user_group_id'] != 1 && $uploaderFile['UploaderFile']['user_id'] != $user['id']) {
                $this->notFound();
            }
        }

        $result = $this->UploaderFile->delete($id);
        if ($this->RequestHandler->isAjax()) {
            echo $result;
            exit();
        } else {
            if ($result) {
                $this->BcMessage->setSuccess(sprintf(__d('baser', '%s を削除しました。'), $uploaderFile['UploaderFile']['name']));
            } else {
                $this->BcMessage->setError(__d('baser', '削除中にエラーが発生しました。'));
            }
            $this->redirect(['action' => 'index']);
        }
    }

    /**
     * 検索ボックスを取得する
     *
     * @param string $listid
     */
    public function ajax_get_search_box($listId = "")
    {

        $this->set('listId', $listId);
        $this->render('../Elements/admin/searches/uploader_files_index');
    }

    /**
     * 公開期間のチェックを行う
     *
     */
    public function view_limited_file($filename)
    {

        $display = false;
        if (!empty($_SESSION['Auth'][Configure::read('BcPrefixAuth.Admin.sessionKey')])) {
            $display = true;
        } else {
            $conditions = [
                'UploaderFile.name' => $this->UploaderFile->getSourceFileName($filename),
                ['or' => [
                    ['UploaderFile.publish_begin <=' => date('Y-m-d H:i:s')],
                    ['UploaderFile.publish_begin' => NULL],
                    ['UploaderFile.publish_begin' => '0000-00-00 00:00:00']
                ]],
                ['or' => [
                    ['UploaderFile.publish_end >=' => date('Y-m-d H:i:s')],
                    ['UploaderFile.publish_end' => NULL],
                    ['UploaderFile.publish_end' => '0000-00-00 00:00:00']
                ]]
            ];
            $data = $this->UploaderFile->find('first', ['conditions' => $conditions]);
            if ($data) {
                $display = true;
            }
        }

        if ($display) {
            $info = pathinfo($filename);
            $ext = $info['extension'];
            $contentsMaping = [
                "gif" => "image/gif",
                "jpg" => "image/jpeg",
                "jpeg" => "image/jpeg",
                "png" => "image/png",
                "swf" => "application/x-shockwave-flash",
                "pdf" => "application/pdf",
                "sig" => "application/pgp-signature",
                "spl" => "application/futuresplash",
                "doc" => "application/msword",
                "ai" => "application/postscript",
                "torrent" => "application/x-bittorrent",
                "dvi" => "application/x-dvi",
                "gz" => "application/x-gzip",
                "pac" => "application/x-ns-proxy-autoconfig",
                "tar.gz" => "application/x-tgz",
                "tar" => "application/x-tar",
                "zip" => "application/zip",
                "mp3" => "audio/mpeg",
                "m3u" => "audio/x-mpegurl",
                "wma" => "audio/x-ms-wma",
                "wax" => "audio/x-ms-wax",
                "wav" => "audio/x-wav",
                "xbm" => "image/x-xbitmap",
                "xpm" => "image/x-xpixmap",
                "xwd" => "image/x-xwindowdump",
                "css" => "text/css",
                "html" => "text/html",
                "js" => "text/javascript",
                "txt" => "text/plain",
                "xml" => "text/xml",
                "mpeg" => "video/mpeg",
                "mov" => "video/quicktime",
                "avi" => "video/x-msvideo",
                "asf" => "video/x-ms-asf",
                "wmv" => "video/x-ms-wmv"
            ];
            header("Content-type: " . $contentsMaping[$ext]);
            readfile(WWW_ROOT . 'files' . DS . 'uploads' . DS . 'limited' . DS . $filename);
            exit();
        } else {
            $this->notFound();
        }
    }
}
