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
use BaserCore\Error\BcException;
use BaserCore\Utility\BcSiteConfig;
use BcUploader\Service\Admin\UploaderFilesAdminService;
use BcUploader\Service\Admin\UploaderFilesAdminServiceInterface;
use BcUploader\Service\UploaderFilesService;
use BcUploader\Service\UploaderFilesServiceInterface;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * ファイルアップローダーコントローラー
 */
class UploaderFilesController extends BcAdminAppController
{

    /**
     * Before filter
     *
     * @param EventInterface $event
     * @return \Cake\Http\Response|void
     * @checked
     * @noTodo
     */
    public function beforeFilter(EventInterface $event)
    {
        $this->viewBuilder()->setHelpers(['BcUploader.Uploader']);
        return parent::beforeFilter($event);
    }

    /**
     * [ADMIN] ファイル一覧
     *
     * @param UploaderFilesAdminService $service
     * @return void
     * @checked
     * @noTodo
     */
    public function index(UploaderFilesAdminServiceInterface $service)
    {
        $this->setViewConditions('UploadFile', [
            'default' => [
                'query' => [
                    'num' => BcSiteConfig::get('admin_list_num'),
                    'uploader_type' => 'all'
                ]]]);
        $this->setRequest($this->getRequest()->withParsedBody($this->getRequest()->getQueryParams()));
        $this->set($service->getViewVarsForIndex());
    }

    /**
     * エディタから呼び出される前提の一覧
     *
     * @param UploaderFilesAdminService $service
     * @param int $id
     * @checked
     * @noTodo
     */
    public function ajax_index(UploaderFilesAdminServiceInterface $service, int $id)
    {
        $this->viewBuilder()->disableAutoLayout();
        $this->set($service->getViewVarsForIndex($id));
    }

    /**
     * [ADMIN] ファイル一覧を表示
     *
     * ファイルアップロード時にリダイレクトされた場合、
     * RequestHandlerコンポーネントが作動しないので明示的に
     * レイアウト、デバッグフラグの設定をする
     *
     * @param UploaderFilesAdminServiceInterface $service
     * @param int|null $id 呼び出し元 識別ID
     * @return void
     * @checked
     * @noTodo
     */
    public function ajax_list(UploaderFilesAdminServiceInterface $service, int $id = null)
    {
        $this->viewBuilder()->disableAutoLayout();
        $this->setViewConditions('UploadFile', [
            'default' => [
                'query' => [
                    'num' => BcSiteConfig::get('admin_list_num')
                ]],
            'type' => 'get'
        ]);
        $this->set(
            $service->getViewVarsForAjaxList(
                $this->paginate($service->getIndex($this->getRequest()->getQueryParams())),
                $id
            )
        );
    }

    /**
     * [ADMIN] サイズを指定して画像タグを取得する
     *
     * @param UploaderFilesAdminService $service
     * @param string $name
     * @param string $size
     * @checked
     * @noTodo
     */
    public function ajax_image(
        UploaderFilesAdminServiceInterface $service,
        string $name,
        string $size = 'small')
    {
        $this->viewBuilder()->disableAutoLayout();
        $this->set($service->getViewVarsForAjaxImage($name, $size));
    }

    /**
     * [ADMIN] 各サイズごとの画像の存在チェックを行う
     *
     * @param string $name
     * @return void
     */
    public function ajax_exists_images(string $name)
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
     * @param UploaderFilesService $service
     * @param int $id
     * @return \Cake\Http\Response|void|null
     * @checked
     * @noTodo
     */
    public function edit(UploaderFilesServiceInterface $service, int $id)
    {
        $entity = $service->get($id);
        if(!$service->isEditable($entity->toArray())) {
            $this->BcMessage->setWarning(__d('baser', '編集権限がありません。'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->getRequest()->is(['post', 'put'])) {
            try {
                $entity = $service->update($entity, $this->getRequest()->getData());
                $this->BcMessage->setInfo(__d('baser', 'アップロードファイル「{0}」を更新しました。', $entity->name));
                $this->redirect(['action' => 'index']);
            } catch (PersistenceFailedException $e) {
                $entity = $e->getEntity();
                $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            } catch (\Throwable $e) {
                $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。'));
            }
        }
        $this->set([
            'uploaderFile' => $entity
        ]);
    }

    /**
     * [ADMIN] 削除処理
     *
     * @param UploaderFilesService $service
     * @param int $id
     * @return    void
     * @checked
     * @noTodo
     */
    public function delete(UploaderFilesServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['post', 'delete']);

        try {
            $entity = $service->get($id);
            if ($service->delete($id)) {
                $this->BcMessage->setSuccess(__d('baser', 'アップロードファイル「{0}」を削除しました。', $entity->name));
            }
        } catch (BcException $e) {
            $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * 検索ボックスを取得する
     *
     * @param int|null $listId
     * @checked
     * @noTodo
     */
    public function ajax_get_search_box(int $listId = null)
    {
        $this->viewBuilder()->disableAutoLayout();
        $this->set('listId', $listId);
        $this->render('../element/search/uploader_files_index');
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
