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
use BaserCore\Model\Table\PasswordRequestsTable;
use BaserCore\Model\Table\UsersTable;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\EventInterface;
use Cake\Routing\Router;
use Cake\Http\Response;

/**
 * Class UsersController
 * @package BaserCore\Controller\Admin
 * @property UsersTable $Users
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
        $this->loadModel('Users');
        $this->Authentication->allowUnauthenticated(['entry', 'apply']);
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

        if ($this->request->is('post') === false) {
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

            // TODO メール送信
            $this->PasswordRequests->save($passwordRequest);
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
	 */
    public function apply($key): void
    {
        $this->setTitle(__d('baser', 'パスワードのリセット'));
        // $passwordRequest = $this->PasswordRequests->newEmptyEntity();


        $passwordRequest = $this->PasswordRequests->getEnableRequestData($key);
        if (empty($passwordRequest)) {
            $this->render('expired');
            return;
        }

        var_dump($passwordRequest);
        exit;

        // TODO ユーザパスワード更新処理
        // TODO 変更申請使用済み更新処理

        // TODO エラー制御

        $this->set('passwordRequest', $passwordRequest);

    }

}
