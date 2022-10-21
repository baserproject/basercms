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
     * 画面の情報をセットする
     *
     * @param array $targetModel ターゲットとなるモデル
     * @param array $options オプション
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function setViewConditions($targetModel = [], $options = []): void
    {
        $this->saveViewConditions($targetModel, $options);
        $this->loadViewConditions($targetModel, $options);
    }

    /**
     * 画面の情報をセッションに保存する
     *
     * @param array $targetModel
     * @param array $options オプション
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function saveViewConditions($targetModel = [], $options = []): void
    {
        $options = array_merge([
            'action' => '',
            'group' => '',
            'post' => true,
            'get' => true,
            'named' => true,
            'query' => true,
        ], $options);

        if (!$options['action']) {
            $options['action'] = $this->request->getParam('action');
        }
        $contentsName = $this->name . Inflector::classify($options['action']);
        if ($options['group']) {
            $contentsName .= "." . $options['group'];
        }

        if (!is_array($targetModel)) {
            $targetModel = [$targetModel];
        }

        $session = $this->request->getSession();

        if ($options['post']) {
            if ($targetModel) {
                foreach($targetModel as $model) {
                    if ($this->request->getData($model)) {
                        $session->write("BcApp.viewConditions.{$contentsName}.data.{$model}", $this->request->getData($model));
                    }
                }
            } else {
                if ($this->request->getData()) {
                    $session->write("BcApp.viewConditions.{$contentsName}.data", $this->request->getData());
                }
            }
        }

        if ($options['get'] && $this->request->getQuery()) {
            $session->write("BcApp.viewConditions.{$contentsName}.query", $this->request->getQuery());
        }
        if (($options['named']) && $this->request->getParam('named')) {
            if ($session->check("BcApp.viewConditions.{$contentsName}.named")) {
                $named = array_merge($session->read("BcApp.viewConditions.{$contentsName}.named"), $this->request->getParam('named'));
            } else {
                $named = $this->request->getParam('named');
            }
            $session->write("BcApp.viewConditions.{$contentsName}.named", $named);
        }
        if ($options['query'] && $this->request->getQuery()) {
            if (isset($options['default']['query'])) {
                if ($session->check("BcApp.viewConditions.{$contentsName}.query")) {
                    $query = array_merge($options['default']['query'], $this->request->getQueryParams());
                } else {
                    $query = $options['default']['query'];
                }
            } else {
                $query = $this->request->getQueryParams();
            }

            $session->write("BcApp.viewConditions.{$contentsName}.query", $query);
        }
    }

    /**
     * 画面の情報をセッションから読み込む
     *
     * @param array $targetModel
     * @param array|string $options オプション
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function loadViewConditions($targetModel = [], $options = []): void
    {
        $options = array_merge([
            'default' => [],
            'action' => '',
            'group' => '',
            'post' => true,
            'get' => true,
            'named' => true
        ], $options);

        if (!$options['action']) {
            $options['action'] = $this->request->getParam('action');
        }
        $contentsName = $this->name . Inflector::classify($options['action']);
        if ($options['group']) {
            $contentsName .= "." . $options['group'];
        }

        if (!is_array($targetModel)) {
            $targetModel = [$targetModel];
        }

        $session = $this->request->getSession();

        if ($targetModel) {
            foreach($targetModel as $model) {
                if ($session->check("BcApp.viewConditions.{$contentsName}.data.{$model}")) {
                    $data = $session->read("BcApp.viewConditions.{$contentsName}.data.{$model}");
                } elseif (!empty($options['default'][$model])) {
                    $data = $options['default'][$model];
                } else {
                    $data = [];
                }
                if ($data) {
                    $this->request = $this->request->withData($model, $data);
                }
            }
        }

        $query = [];
        if ($session->check("BcApp.viewConditions.{$contentsName}.query")) {
            $query = $data = $session->read("BcApp.viewConditions.{$contentsName}.query");
            unset($query['url']);
            unset($query['ext']);
            unset($query['x']);
            unset($query['y']);
        }
        if (empty($query) && !empty($options['default']['query'])) {
            $query = $options['default']['query'];
        }
        if ($query) {
            $this->request = $this->request->withQueryParams($query);
        }

        $named = [];
        if (!empty($options['default']['named'])) {
            $named = $options['default']['named'];
        }
        if ($session->check("BcApp.viewConditions.{$contentsName}.named")) {
            $named = array_merge($named, $session->read("BcApp.viewConditions.{$contentsName}.named"));
        }

        $named['?'] = $query;

        $this->request = $this->request->withParam('pass', $named);
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
        if ($this->getName() === 'Preview') return;
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
