<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Controller\Admin;

use BaserCore\Utility\BcUtil;
use BaserCore\Controller\Component\BcMessageComponent;
use BaserCore\Mailer\PasswordRequestMailer;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\EventInterface;
use Cake\Routing\Router;
use Cake\Http\Response;

/**
 * Class UsersController
 * @package BaserCore\Controller\Admin
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
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel('BaserCore.Users');
        $this->Authentication->allowUnauthenticated(['entry', 'apply', 'done']);
    }


	/**
	 * パスワード変更申請
	 *
	 * - input
     *	- PasswordRequest.email
     *  - submit
     *
     * - viewVars
     *  - title
     *  - PasswordRequest.[]
	 *
	 */
    public function entry(): void
    {
        $passwordRequest = $this->PasswordRequests->newEmptyEntity();
        $this->set('passwordRequest', $passwordRequest);
        $this->setTitle(__d('baser', 'パスワードのリセット'));

        if ($this->request->is(['patch', 'post', 'put']) === false) {
            return;
        }

        $passwordRequest = $this->PasswordRequests->patchEntity($passwordRequest, $this->request->getData());
        if ($passwordRequest->hasErrors()) {
            $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            return;
        }

        $user = $this->Users->find('all')
            ->where(['Users.email' => $this->request->getData('email')])
            ->first();

        if (!empty($user)) {
            $passwordRequest->user_id = $user->id;
            $passwordRequest->used = 0;
            $passwordRequest->setRequestKey();

            $this->PasswordRequests->save($passwordRequest);
            (new PasswordRequestMailer())->deliver($user, $passwordRequest);
        }

        $this->BcMessage->setSuccess(__d('baser', 'パスワードのリセットを受付ました。該当メールアドレスが存在した場合、変更URLを送信いたしました。'));
    }

	/**
	 * パスワード変更
	 *
	 * - input
     *	- User.password_1
     *	- User.password_2
     *  - submit
	 *
     * - viewVars
     *  - title
     *  - user
	 */
    public function apply($key): void
    {
        $title = __d('baser', 'パスワードのリセット');
        $this->set('title', $title);
        $this->set('user', $this->Users->newEmptyEntity($this->request->getData()));
        $passwordRequest = $this->PasswordRequests->getEnableRequestData($key);

        if (empty($passwordRequest)) {
            $this->response->withStatus(404);
            $this->render('expired');
            return;
        }

        if ($this->request->is(['patch', 'post', 'put']) === false) {
            return;
        }

        // ユーザパスワード更新処理
        // $conn = ConnectionManager::get('default');

        $user = $this->Users->find('all')
            ->where(['Users.id' => $passwordRequest->user_id])
            ->first();
        $this->request = $this->request->withData('password', $this->request->getData('password_1'));

        $user = $this->Users->patchEntity(
            $user,
            $this->request->getData(),
            ['validate' => 'passwordUpdate']
        );

        if ($user->hasErrors()) {
            $this->set('user', $user);
            return;
        }

        if ($this->PasswordRequests->updatePassword($passwordRequest, $this->request->getData('password_1')) === false) {
            $this->render('expired');
            return;
        }
        $this->redirect(['action' => 'done']);
    }

    /**
     * パスワード変更完了
     *
     * - viewVars
     *  - title
     *
     * @return void
     */
    public function done()
    {
        $title = __d('baser', 'パスワードのリセット');
        $this->set('title', $title);
    }

}
