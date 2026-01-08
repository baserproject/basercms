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

namespace BaserCore\Controller\Admin;

use Authentication\Controller\Component\AuthenticationComponent;
use BaserCore\Controller\Component\BcMessageComponent;
use BaserCore\Error\BcException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Service\PasswordRequestsService;
use BaserCore\Service\PasswordRequestsServiceInterface;
use BaserCore\Service\UsersServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * Class PasswordRequestsController
 * @property AuthenticationComponent $Authentication
 * @property BcMessageComponent $BcMessage
 */
class PasswordRequestsController extends BcAdminAppController
{

    /**
     * initialize
     * ログインページ認証除外
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated(['entry', 'apply', 'done']);
    }

    /**
     * パスワード変更申請
     *
     * @param PasswordRequestsService $service
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function entry(PasswordRequestsServiceInterface $service): void
    {
        $passwordRequest = $service->getNew();
        $this->set('passwordRequest', clone $passwordRequest);
        if (!$this->request->is(['patch', 'post', 'put'])) return;
        $message = __d('baser_core', 'パスワードのリセットを受付ました。該当メールアドレスが存在した場合、変更URLを送信いたしました。');
        $isError = false;
        try {
            $service->update($passwordRequest, $this->request->getData());
        } catch (RecordNotFoundException) {
        } catch (PersistenceFailedException) {
            $message = __d('baser_core', '入力エラーです。内容を修正してください。');
            $isError = true;
        } catch (\Throwable $e) {
            $message = $e->getMessage();
            if($message === 'Could not send email: unknown') {
                $message = __d('baser_core', 'メールが送信できません。管理者に連絡してください。');
            }
            $isError = true;
        }
        if($isError) {
            $this->BcMessage->setError($message);
        } else {
            $this->BcMessage->setSuccess($message);
        }
        $this->redirect(['action' => 'entry']);
    }

    /**
     * パスワード変更
     * @checked
     * @noTodo
     * @unitTest
     */
    public function apply(PasswordRequestsServiceInterface $service, UsersServiceInterface $usersService, $key): void
    {
        $this->set('user', $usersService->getNew());
        $passwordRequest = $service->getEnableRequestData($key);

        if (empty($passwordRequest)) {
            $this->response->withStatus(404);
            $this->render('expired');
            return;
        }

        if (!$this->request->is(['patch', 'post', 'put'])) return;

        try {
            $service->updatePassword($passwordRequest, $this->getRequest()->getData());
        } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
            $this->set('user', $e->getEntity());
            return;
        } catch (BcException $e) {
            $this->BcMessage->setError(__d('baser_core', 'システムエラーが発生しました。'));
            return;
        }
        $this->redirect(['action' => 'done']);
    }

    /**
     * パスワード変更完了
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function done()
    {
    }

}
