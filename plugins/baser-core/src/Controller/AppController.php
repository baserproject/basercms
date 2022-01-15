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

namespace BaserCore\Controller;

use App\Controller\AppController as BaseController;
use BaserCore\Controller\Component\BcMessageComponent;
use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;
use BaserCore\Utility\BcSiteConfig;
use BaserCore\Utility\BcUtil;
use Cake\Controller\Component\PaginatorComponent;
use Cake\Controller\Component\SecurityComponent;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Event\EventManagerInterface;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

/**
 * Class AppController
 * @property BcMessageComponent $BcMessage
 * @property SecurityComponent $Security
 * @property PaginatorComponent $Paginator
 */
class AppController extends BaseController
{
    /**
     * Trait
     */
    use BcEventDispatcherTrait;

    /**
     * BcAppController constructor.
     * @param ServerRequest|null $request
     * @param Response|null $response
     * @param string|null $name
     * @param EventManagerInterface|null $eventManager
     * @param ComponentRegistry|null $components
     * @checked
     * @note(value="BcRequestFilterをミドルウェアに移行してから実装する")
     */
    public function __construct(
        ?ServerRequest $request = null,
        ?Response $response = null,
        ?string $name = null,
        ?EventManagerInterface $eventManager = null,
        ?ComponentRegistry $components = null
    )
    {
        parent::__construct($request, $response, $name, $eventManager, $components);

        // TODO ucmitz BcRequestFilter の実装が必要（ミドルウェアへの移行が必要）
        // >>>
        // $isInstall = $request->is('install');
        // ---
        $isInstall = false;
        // <<<

        // インストールされていない場合、トップページにリダイレクトする
        // コンソールベースのインストールの際のページテンプレート生成において、
        // BC_INSTALLED、$isInstall ともに true でない為、コンソールの場合は無視する
        if (!(BC_INSTALLED || BcUtil::isConsole()) && !$isInstall) {
            $this->redirect('/');
        }

    }

    /**
     * Initialize
     * @checked
     * @unitTest
     * @noTodo
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('BaserCore.BcMessage');
        $this->loadComponent('Security');
        $this->loadComponent('Paginator');

        // TODO ucmitz 未移行のためコメントアウト
        // >>>
//        $this->loadComponent('BaserCore.Flash');
//        $this->loadComponent('BaserCore.BcEmail');
        // <<<
    }

    /**
     * Before Render
     * @param EventInterface $event
     * @return \Cake\Http\Response|void|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeRender(EventInterface $event): void
    {
        if (!isset($this->RequestHandler) || !$this->RequestHandler->prefers('json')) {
            $this->viewBuilder()->setClassName('BaserCore.App');
            $site = $this->getRequest()->getParam('Site');
            if(!$site) {
                $sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
                $site = $sites->getRootMain();
            }
            $this->viewBuilder()->setTheme($site->theme);
        }
    }

    /**
     * Set Title
     * @param string $title
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setTitle($title): void
    {
        $this->set('title', $title);
    }

    /**
     * siteUrlや、sslUrlと現在のURLが違う場合には、そちらのURLにリダイレクトを行う
     * setting.php にて、cmsUrlとして、cmsUrlを定義した場合にはそちらを優先する
     * @return Response|void|null
     */
    public function redirectIfIsNotSameSite()
    {
        if($this->getRequest()->is('admin')) {
            return;
        }
        if (Configure::read('BcEnv.cmsUrl')) {
            $siteUrl = Configure::read('BcEnv.cmsUrl');
        } elseif ($this->getRequest()->is('ssl')) {
            $siteUrl = Configure::read('BcEnv.sslUrl');
        } else {
            $siteUrl = Configure::read('BcEnv.siteUrl');
        }
        if(!$siteUrl) {
            return;
        }
        if (BcUtil::siteUrl() !== $siteUrl) {
            $params = $this->getRequest()->getAttributes()['params'];
            unset($params['Content']);unset($params['Site']);
            $url = Router::reverse($params, false);
            $webroot = $this->request->getAttributes()['webroot'];
            $webrootReg = '/^\/' . preg_quote($webroot, '/') . '/';
            $url = preg_replace($webrootReg, '', $url);
            return $this->redirect($siteUrl . $url);
        }
    }

    /**
     * メンテナンス画面へのリダイレクトが必要な場合にリダイレクトする
     * @return Response|void|null
     */
    public function redirectIfIsRequireMaintenance()
    {
        if ($this->request->is('ajax')) {
            return;
        }
        if(empty(BcSiteConfig::get('maintenance'))){
            return;
        }
        if(Configure::read('debug')) {
            return;
        }
        if($this->getRequest()->is('maintenance')) {
            return;
        }
        if($this->getRequest()->is('admin')) {
            return;
        }
        if(BcUtil::isAdminUser()) {
            return;
        }

        // TODO ucmitz 削除検討要
        // CakePHP4 から requestAction がなくなっているので不要の可能性が高い
        // cell 機能で呼び出された場合のスルーの処理を書いたら削除する
        if (!empty($this->getRequest()->getParam('return')) && !empty($this->getRequest()->getParam('requested'))) {
            return $this->getResponse();
        }

        $redirectUrl = '/maintenance';
        if ($this->getRequest()->getParam('Site.alias')) {
            $redirectUrl = '/' . $this->getRequest()->getParam('Site.alias') . $redirectUrl;
        }
        return $this->redirect($redirectUrl);
    }

}
