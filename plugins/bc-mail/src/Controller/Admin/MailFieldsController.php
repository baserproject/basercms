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

namespace BcMail\Controller\Admin;

use BaserCore\Error\BcException;
use BaserCore\Service\ContentsService;
use BaserCore\Service\ContentsServiceInterface;
use BcMail\Service\Admin\MailFieldsAdminService;
use BcMail\Service\Admin\MailFieldsAdminServiceInterface;
use BcMail\Service\MailFieldsService;
use BcMail\Service\MailFieldsServiceInterface;
use Cake\Event\EventInterface;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\ORM\Exception\PersistenceFailedException;
use Exception;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * メールフィールドコントローラー
 */
class MailFieldsController extends MailAdminAppController
{

    /**
     * initialize
     * @return void
     * @checked
     * @noTodo
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('BaserCore.BcAdminContents', [
            'entityVarName' => 'mailContent'
        ]);
    }

    /**
     * beforeFilter
     *
     * @return void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->_checkEnv();

        $mailContentId = $this->request->getParam('pass.0');
        if (!$mailContentId) throw new BcException(__d('baser', '不正なURLです。'));
        /* @var ContentsService $contentsService */
        $contentsService = $this->getService(ContentsServiceInterface::class);
        $request = $contentsService->setCurrentToRequest(
            'BcMail.MailContent',
            $mailContentId,
            $this->getRequest()
        );
        if (!$request) throw new BcException(__d('baser', 'コンテンツデータが見つかりません。'));
        $this->setRequest($request);

        // TODO ucmitz 以下、未精査
        // >>>
//        $mailContentId = $this->request->getParam('pass.0');
//        $this->mailContent = $this->MailContent->read(null, $mailContentId);
//        $this->request->getParam('Content', $this->BcContents->getContent($mailContentId)['Content']);
//        if ($this->request->getParam('Content.status')) {
//            $sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
//            $site = $sites->findById($this->request->getParam('Content.site_id'))->first();
//            $this->set(
//                'publishLink',
//                $this->Content->getUrl(
//                    $this->request->getParam('Content.url'),
//                    true,
//                    $site->useSubDomain
//                )
//            );
//        }
        // <<<
    }

    /**
     * プラグインの環境をチェックする
     */
    protected function _checkEnv()
    {
        $savePath = WWW_ROOT . 'files' . DS . "mail" . DS . 'limited';
        if (!is_dir($savePath)) {
            $Folder = new Folder();
            $Folder->create($savePath, 0777);
            if (!is_dir($savePath)) {
                $this->BcMessage->setError(
                    'ファイルフィールドを利用している場合、フォームより送信したファイルフィールドのデータは公開された状態となっています。URLを直接閲覧すると参照できてしまいます。参照されないようにするためには、' . WWW_ROOT . 'files/mail/ に書き込み権限を与えてください。'
                );
            }
            $File = new File($savePath . DS . '.htaccess');
            $htaccess = "Order allow,deny\nDeny from all";
            $File->write($htaccess);
            $File->close();
            if (!file_exists($savePath . DS . '.htaccess')) {
                $this->BcMessage->setError(
                    'ファイルフィールドを利用している場合、フォームより送信したファイルフィールドのデータは公開された状態となっています。URLを直接閲覧すると参照できてしまいます。参照されないようにするためには、' . WWW_ROOT . 'files/mail/limited/ に書き込み権限を与えてください。'
                );
            }
        }
    }

    /**
     * beforeRender
     *
     * @return void
     */
    public function beforeRender(EventInterface $event): void
    {
        parent::beforeRender($event);
        // TODO ucmitz 未実装
//        $this->set('mailContent', $this->mailContent);
    }

    /**
     * [ADMIN] メールフィールド一覧
     *
     * @param int $mailContentId
     * @return void
     * @checked
     * @noTodo
     */
    public function index(MailFieldsAdminServiceInterface $service, int $mailContentId)
    {
        $this->setViewConditions('MailField', ['default' => ['query' => [
            'sort' => 'sort',
            'direction' => 'asc',
        ]]]);
        $this->set($service->getViewVarsForIndex($this->getRequest(), $mailContentId));
    }

    /**
     * [ADMIN] メールフィールド追加
     *
     * @param MailFieldsAdminService $service
     * @param int $mailContentId
     * @return void
     * @checked
     * @noTodo
     */
    public function add(MailFieldsAdminServiceInterface $service, int $mailContentId)
    {
        if($this->getRequest()->is(['post', 'put'])) {
            try {
                $entity = $service->create($this->getRequest()->getData());
                $this->BcMessage->setSuccess(__d('baser', '新規メールフィールド「{0}」を追加しました。', $entity->name));
                $this->redirect([
                    'controller' => 'mail_fields',
                    'action' => 'index',
                    $mailContentId
                ]);
            } catch (PersistenceFailedException $e) {
                $entity = $e->getEntity();
                $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            } catch (Exception $e) {
                $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage()));
            }
        }
        $this->set($service->getViewVarsForAdd(
            $mailContentId,
            $entity ?? $service->getNew($mailContentId)
        ));
    }

    /**
     * [ADMIN] 編集処理
     *
     * @param MailFieldsAdminService $service
     * @param int $mailContentId
     * @param int $id
     * @return void
     * @checked
     * @noTodo
     */
    public function edit(MailFieldsAdminServiceInterface $service, int $mailContentId, int $id)
    {
        $entity = $service->get($id);
        if($this->getRequest()->is(['post', 'put'])) {
            try {
                $entity = $service->update($entity, $this->getRequest()->getData());
                $this->BcMessage->setSuccess(__d('baser', 'メールフィールド「{0}」を更新しました。', $entity->name));
                $this->redirect([
                    'controller' => 'mail_fields',
                    'action' => 'index',
                    $mailContentId
                ]);
            } catch (PersistenceFailedException $e) {
                $entity = $e->getEntity();
                $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            } catch (Exception $e) {
                $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage()));
            }
        }
        $this->set($service->getViewVarsForEdit(
            $mailContentId,
            $entity
        ));
    }

    /**
     * [ADMIN] 削除処理（Ajax）
     *
     * @param int $mailContentId
     * @param int $id
     * @return void
     */
    public function ajax_delete($mailContentId, $id = null)
    {
        $this->_checkSubmitToken();
        /* 除外処理 */
        if (!$id) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }
        // メッセージ用にデータを取得
        $data = $this->MailField->read(null, $id);
        $field = Hash::get($data, 'MailField');

        /* 削除処理 */
        if (!$field || !$this->MailMessage->delMessageField($mailContentId, $field['field_name'])) {
            $this->ajaxError(
                500,
                __d('baser', 'データベースに問題があります。メール受信データ保存用テーブルの更新処理に失敗しました。')
            );
            exit;
        }

        if (!$this->MailField->delete($id)) {
            exit;
        }

        $this->MailField->saveDbLog(
            sprintf(
                __d('baser', 'メールフィールド「%s」 を削除しました。'),
                $field['name']
            )
        );
        exit(true);
    }

    /**
     * [ADMIN] 削除処理
     *
     * @param MailFieldsService $service
     * @param int $mailContentId
     * @param int $id
     * @throws \Throwable
     * @checked
     * @noTodo
     */
    public function delete(MailFieldsServiceInterface $service, int $mailContentId, int $id)
    {
        $this->request->allowMethod(['post', 'delete']);
        $entity = $service->get($id);
        try {
            if($service->delete($id)) {
                $this->BcMessage->setSuccess(__d('baser', 'メールフィールド「{0}」を削除しました。', $entity->name));
            } else {
                $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。'));
            }
        } catch (\Throwable $e) {
            $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
        }
        return $this->redirect(['action' => 'index', $mailContentId]);
    }

    /**
     * 一括削除
     *
     * @param array $ids
     * @return boolean
     */
    protected function _batch_del($ids)
    {
        if ($ids) {
            foreach ($ids as $id) {
                // メッセージ用にデータを取得
                $data = $this->MailField->read(null, $id);
                $field = $data['MailField'];
                /* 削除処理 */
                if (!$this->MailMessage->delMessageField($field['mail_content_id'], $field['field_name'])) {
                    continue;
                }
                if (!$this->MailField->delete($id)) {
                    continue;
                }
                $this->MailField->saveDbLog(
                    sprintf(
                        __d('baser', 'メールフィールド「%s」 を削除しました。'),
                        $field['name']
                    )
                );
            }
        }

        return true;
    }

    /**
     * フォームの初期値を取得する
     *
     * @return array
     */
    protected function _getDefaultValue()
    {
        $data['MailField']['type'] = 'text';
        $data['MailField']['use_field'] = 1;
        $data['MailField']['no_send'] = 0;
        return $data;
    }

    /**
     * フィールドデータをコピーする
     *
     * @param MailFieldsService $service
     * @param int $mailContentId
     * @param int $id
     * @return void
     * @checked
     * @noTodo
     */
    public function copy(MailFieldsServiceInterface $service, int $mailContentId, int $id)
    {
        try {
            if($service->copy($mailContentId, $id)) {
                $entity = $service->get($id);
                $this->BcMessage->setSuccess(__d('baser', 'メールフィールド「{0}」をコピーしました。', $entity->name));
            } else {
                $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。'));
            }
        } catch (\Throwable $e) {
            $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
        }
        return $this->redirect(['action' => 'index', $mailContentId]);
    }

    /**
     * メッセージCSVファイルをダウンロードする
     *
     * @param int $mailContentId
     * @return void
     */
    public function download_csv($mailContentId)
    {
        $mailContentId = (int)$mailContentId;
        if (!$mailContentId || !$this->mailContent || !is_int($mailContentId)) {
            $this->BcMessage->setError(__d('baser', '無効な処理です。'));
            $this->redirect(['controller' => 'mail_contents', 'action' => 'index']);
        }
        $this->MailMessage->alias = 'MailMessage' . $mailContentId;
        $this->MailMessage->schema(true);
        $this->MailMessage->cacheSources = false;
        $this->MailMessage->setUseTable($mailContentId);
        $messages = $this->MailMessage->convertMessageToCsv(
            $mailContentId,
            $this->MailMessage->find(
                'all',
                ['order' => $this->MailMessage->alias . '.created DESC']
            )
        );
        $this->set('encoding', $this->request->getQuery('encoding'));
        $this->set('messages', $messages);
        $this->set('contentName', $this->request->getParam('Content.name'));
    }

    /**
     * [ADMIN] 無効状態にする（AJAX）
     *
     * @param MailFieldsService $service
     * @param int $mailContentId
     * @param int $id
     * @return void
     * @checked
     * @noTodo
     */
    public function unpublish(MailFieldsServiceInterface $service, int $mailContentId, int $id)
    {
        if ($this->request->is(['patch', 'post', 'put'])) {
            $result = $service->unpublish($id);
            if ($result) {
                $this->BcMessage->setSuccess(sprintf(__d('baser', 'メールフィールド「%s」を無効状態にしました。'), $result->name));
            } else {
                $this->BcMessage->setSuccess(__d('baser', 'データベース処理中にエラーが発生しました。'));
            }
        }
        return $this->redirect(['action' => 'index', $mailContentId]);
    }

    /**
     * [ADMIN] 有効状態にする
     *
     * @param MailFieldsService $service
     * @param int $mailContentId
     * @param int $id
     * @checked
     * @noTodo
     */
    public function publish(MailFieldsServiceInterface $service, int $mailContentId, int $id)
    {
        if ($this->request->is(['patch', 'post', 'put'])) {
            $result = $service->publish($id);
            if ($result) {
                $this->BcMessage->setSuccess(sprintf(__d('baser', 'メールフィールド「%s」を有効状態にしました。'), $result->name));
            } else {
                $this->BcMessage->setSuccess(__d('baser', 'データベース処理中にエラーが発生しました。'));
            }
        }
        return $this->redirect(['action' => 'index', $mailContentId]);
    }

    /**
     * 一括公開
     *
     * @param array $ids
     * @return boolean
     * @access protected
     */
    protected function _batch_publish($ids)
    {
        if (!$ids) {
            return true;
        }
        foreach ($ids as $id) {
            $this->_changeStatus($id, true);
        }
        return true;
    }

    /**
     * 一括非公開
     *
     * @param array $ids
     * @return boolean
     * @access protected
     */
    protected function _batch_unpublish($ids)
    {
        if (!$ids) {
            return true;
        }

        foreach ($ids as $id) {
            $this->_changeStatus($id, false);
        }
        return true;
    }

    /**
     * ステータスを変更する
     *
     * @param int $id
     * @param boolean $status
     * @return boolean
     */
    protected function _changeStatus($id, $status)
    {
        $statusTexts = [0 => __d('baser', '無効'), 1 => __d('baser', '有効')];
        $data = $this->MailField->find(
            'first',
            ['conditions' => ['MailField.id' => $id], 'recursive' => -1]
        );
        $data['MailField']['use_field'] = $status;
        $this->MailField->set($data);

        if (!$this->MailField->save()) {
            return false;
        }

        $statusText = $statusTexts[$status];
        $this->MailField->saveDbLog(
            sprintf(
                __d('baser', 'メールフィールド「%s」 の設定を %s に変更しました。'),
                $data['MailField']['name'],
                $statusText
            )
        );
        return true;
    }
}
