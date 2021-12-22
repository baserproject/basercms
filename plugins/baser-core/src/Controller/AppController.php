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
use BaserCore\Utility\BcUtil;
use Cake\Controller\Component\PaginatorComponent;
use Cake\Controller\Component\SecurityComponent;
use Cake\Controller\ComponentRegistry;
use Cake\Event\EventInterface;
use Cake\Event\EventManagerInterface;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;

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

}
