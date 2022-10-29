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

        $passwordRequest = $service->update($passwordRequest, $this->request->getData());
        if (!$passwordRequest) {
            $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            return;
        }
        $this->BcMessage->setSuccess(__d('baser', 'パスワードのリセットを受付ました。該当メールアドレスが存在した場合、変更URLを送信いたしました。'));
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
            $this->BcMessage->setError(__('baser', 'システムエラーが発生しました。'));
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
