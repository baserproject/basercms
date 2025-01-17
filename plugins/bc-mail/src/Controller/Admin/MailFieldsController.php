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
use BaserCore\Utility\BcFile;
use BaserCore\Utility\BcFolder;
use BcMail\Service\Admin\MailFieldsAdminService;
use BcMail\Service\Admin\MailFieldsAdminServiceInterface;
use BcMail\Service\MailFieldsService;
use BcMail\Service\MailFieldsServiceInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Exception\PersistenceFailedException;
use Exception;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

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
     * @unitTest
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
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeFilter(EventInterface $event)
    {
        $response = parent::beforeFilter($event);
        if($response) return $response;
        $this->_checkEnv();

        $mailContentId = $this->request->getParam('pass.0');
        if (!$mailContentId) throw new BcException(__d('baser_core', '不正なURLです。'));
        /* @var ContentsService $contentsService */
        $contentsService = $this->getService(ContentsServiceInterface::class);
        $request = $contentsService->setCurrentToRequest(
            'BcMail.MailContent',
            $mailContentId,
            $this->getRequest()
        );
        if (!$request) throw new BcException(__d('baser_core', 'コンテンツデータが見つかりません。'));
        $this->setRequest($request);
    }

    /**
     * プラグインの環境をチェックする
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _checkEnv()
    {
        $savePath = WWW_ROOT . 'files' . DS . "mail" . DS . 'limited';
        if (!is_dir($savePath)) {
            $Folder = new BcFolder($savePath);
            $Folder->create();
            if (!is_dir($savePath)) {
                $this->BcMessage->setError(
                    __d('baser_core', 'ファイルフィールドを利用している場合、フォームより送信したファイルフィールドのデータは公開された状態となっています。URLを直接閲覧すると参照できてしまいます。参照されないようにするためには、{0} に書き込み権限を与えてください。', WWW_ROOT . 'files/mail/')
                );
            }

        }
        if (!file_exists($savePath . DS . '.htaccess')) {
            $File = new BcFile($savePath . DS . '.htaccess');
            $htaccess = "Order allow,deny\nDeny from all";
            $File->write($htaccess);
            if (!file_exists($savePath . DS . '.htaccess')) {
                $this->BcMessage->setError(
                    __d('baser_core', 'ファイルフィールドを利用している場合、フォームより送信したファイルフィールドのデータは公開された状態となっています。URLを直接閲覧すると参照できてしまいます。参照されないようにするためには、{0} に書き込み権限を与えてください。', WWW_ROOT . 'files/mail/limited/')
                );
            }
        }
    }

    /**
     * [ADMIN] メールフィールド一覧
     *
     * @param int $mailContentId
     * @return void
     * @checked
     * @noTodo
     * @unitTest
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
     * @unitTest
     */
    public function add(MailFieldsAdminServiceInterface $service, int $mailContentId)
    {
        if($this->getRequest()->is(['post', 'put'])) {
            // EVENT MailFields.beforeAdd
            $event = $this->dispatchLayerEvent('beforeAdd', [
                'data' => $this->getRequest()->getData()
            ]);
            if ($event !== false) {
                $data = ($event->getResult() === null || $event->getResult() === true) ? $event->getData('data') : $event->getResult();
                $this->setRequest($this->getRequest()->withParsedBody($data));
            }
            try {
                $entity = $service->create($this->getRequest()->getData());
                // EVENT MailFields.afterAdd
                $this->dispatchLayerEvent('afterAdd', [
                    'data' => $entity
                ]);
                $this->BcMessage->setSuccess(__d('baser_core', '新規メールフィールド「{0}」を追加しました。', $entity->name));
                $this->redirect([
                    'controller' => 'mail_fields',
                    'action' => 'index',
                    $mailContentId
                ]);
            } catch (PersistenceFailedException $e) {
                $entity = $e->getEntity();
                $this->BcMessage->setError(__d('baser_core', '入力エラーです。内容を修正してください。'));
            } catch (Exception $e) {
                $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage()));
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
     * @unitTest
     */
    public function edit(MailFieldsAdminServiceInterface $service, int $mailContentId, int $id)
    {
        $entity = $service->get($id);
        if($this->getRequest()->is(['post', 'put'])) {
            // EVENT MailFields.beforeEdit
            $event = $this->dispatchLayerEvent('beforeEdit', [
                'data' => $this->getRequest()->getData()
            ]);
            if ($event !== false) {
                $data = ($event->getResult() === null || $event->getResult() === true) ? $event->getData('data') : $event->getResult();
                $this->setRequest($this->getRequest()->withParsedBody($data));
            }
            try {
                $entity = $service->update($entity, $this->getRequest()->getData());
                // EVENT MailFields.afterEdit
                $this->dispatchLayerEvent('afterEdit', [
                    'data' => $entity
                ]);
                $this->BcMessage->setSuccess(__d('baser_core', 'メールフィールド「{0}」を更新しました。', $entity->name));
                $this->redirect([
                    'controller' => 'mail_fields',
                    'action' => 'index',
                    $mailContentId
                ]);
            } catch (PersistenceFailedException $e) {
                $entity = $e->getEntity();
                $this->BcMessage->setError(__d('baser_core', '入力エラーです。内容を修正してください。'));
            } catch (Exception $e) {
                $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage()));
            }
        }
        $this->set($service->getViewVarsForEdit(
            $mailContentId,
            $entity
        ));
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
     * @unitTest
     */
    public function delete(MailFieldsServiceInterface $service, int $mailContentId, int $id)
    {
        $this->request->allowMethod(['post', 'delete']);
        try {
            $entity = $service->get($id);
            if($service->delete($id)) {
                $this->BcMessage->setSuccess(__d('baser_core', 'メールフィールド「{0}」を削除しました。', $entity->name));
            } else {
                $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。'));
            }
        } catch (\Throwable $e) {
            $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
        }
        return $this->redirect(['action' => 'index', $mailContentId]);
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
     * @UnitTest
     */
    public function copy(MailFieldsServiceInterface $service, int $mailContentId, int $id)
    {
        try {
            if($service->copy($mailContentId, $id)) {
                $entity = $service->get($id);
                $this->BcMessage->setSuccess(__d('baser_core', 'メールフィールド「{0}」をコピーしました。', $entity->name));
            } else {
                $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。'));
            }
        } catch (\Throwable $e) {
            $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
        }
        return $this->redirect(['action' => 'index', $mailContentId]);
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
     * @UnitTest
     */
    public function unpublish(MailFieldsServiceInterface $service, int $mailContentId, int $id)
    {
        if ($this->request->is(['patch', 'post', 'put'])) {
            $result = $service->unpublish($id);
            if ($result) {
                $this->BcMessage->setSuccess(sprintf(__d('baser_core', 'メールフィールド「%s」を無効状態にしました。'), $result->name));
            } else {
                $this->BcMessage->setSuccess(__d('baser_core', 'データベース処理中にエラーが発生しました。'));
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
     * @unitTest
     */
    public function publish(MailFieldsServiceInterface $service, int $mailContentId, int $id)
    {
        if ($this->request->is(['patch', 'post', 'put'])) {
            $result = $service->publish($id);
            if ($result) {
                $this->BcMessage->setSuccess(sprintf(__d('baser_core', 'メールフィールド「%s」を有効状態にしました。'), $result->name));
            } else {
                $this->BcMessage->setSuccess(__d('baser_core', 'データベース処理中にエラーが発生しました。'));
            }
        }
        return $this->redirect(['action' => 'index', $mailContentId]);
    }

}
