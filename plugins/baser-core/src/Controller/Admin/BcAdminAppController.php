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
use BaserCore\Controller\BcAppController;
use BaserCore\Service\Admin\BcAdminAppServiceInterface;
use BaserCore\Service\PermissionsServiceInterface;
use BaserCore\Service\UsersService;
use BaserCore\Service\UsersServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;
/**
 * Class BcAdminAppController
 * @property AuthenticationComponent $Authentication
 */
class BcAdminAppController extends BcAppController
{

    /**
     * BcContainerTrait
     */
    use BcContainerTrait;

    /**
     * Initialize
     * @checked
     * @unitTest
     * @note(value="インストーラーを実装してから対応する")
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Authentication.Authentication', [
            'logoutRedirect' => Router::url(Configure::read('BcPrefixAuth.Admin.loginAction'), true),
        ]);

        if (Configure::read('BcApp.adminSsl') && !BcUtil::isConsole()) $this->Security->requireSecure();

        // TODO ucmitz 未移行のためコメントアウト
        // >>>
//        $this->loadComponent('BaserCore.BcManager');
        // <<<

        /** @var UsersService $usersService */
        $usersService = $this->getService(UsersServiceInterface::class);
        $this->response = $usersService->checkAutoLogin($this->request, $this->response);

        // ログインユーザ再読込
        if (!$usersService->reload($this->request)) {
            $this->redirect($this->Authentication->logout());
        }
    }

    /**
     * Before Filter
     * @param EventInterface $event
     * @return Response|void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $user = BcUtil::loginUser();
        /* @var PermissionsServiceInterface $permission */
        $permission = $this->getService(PermissionsServiceInterface::class);
        if ($user && !$permission->check($this->getRequest()->getPath(), Hash::extract($user->toArray()['user_groups'], '{n}.id'))) {
            $this->BcMessage->setError(__d('baser', '指定されたページへのアクセスは許可されていません。'));
            $this->redirect(Router::url(Configure::read('BcPrefixAuth.Admin.loginRedirect')));
        }
    }

    /**
     * Before Render
     * @param EventInterface $event
     * @return \Cake\Http\Response|void|null
     * @checked
     * @unitTest
     * @noTodo
     */
    public function beforeRender(EventInterface $event): void
    {
        parent::beforeRender($event);
        if (isset($this->RequestHandler) && $this->RequestHandler->prefers('json')) return;
        if ($this->getRequest()->getQuery('preview')) return;
        $this->viewBuilder()->setClassName('BaserCore.BcAdminApp');
        $this->setAdminTheme();
        $this->set($this->getService(BcAdminAppServiceInterface::class)->getViewVarsForAll());
    }

    /**
     * 管理画面用テーマをセットする
     *
     * 優先順位
     * BcSiteConfig::get('admin_theme') > Configure::read('BcApp.adminTheme')
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function setAdminTheme()
    {
        $this->viewBuilder()->setTheme(BcUtil::getCurrentAdminTheme());
    }

    /**
     * Set Search
     * @param string $template
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function setSearch($template): void
    {
        $this->set('search', $template);
    }

    /**
     * Set Help
     * @param string $template
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function setHelp($template): void
    {
        $this->set('help', $template);
    }

    /**
     * リファラチェックを行う
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _checkReferer(): bool
    {
        $siteDomain = BcUtil::getCurrentDomain();
        if (empty($_SERVER['HTTP_REFERER'])) {
            return false;
        }
        $refererDomain = BcUtil::getDomain($_SERVER['HTTP_REFERER']);
        if (!$siteDomain || !preg_match('/^' . preg_quote($siteDomain, '/') . '/', $refererDomain)) {
            throw new NotFoundException();
        }
        return true;
    }

}
