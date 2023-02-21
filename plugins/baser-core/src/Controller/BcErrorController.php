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
namespace BaserCore\Controller;

use BaserCore\ServiceProvider\BcServiceProvider;
use BaserCore\Utility\BcContainer;
use Cake\Controller\ComponentRegistry;
use Cake\Event\EventInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Event\EventManagerInterface;
use Cake\Http\Response;
use Cake\Http\ServerRequest;

/**
 * BcErrorController
 *
 * 継承先を BcFrontAppController に切り替えることにより、
 * エラー画面にフロントテーマを適用する（BcFrontAppController::beforeRender() でテーマを適用しているため）
 */
class BcErrorController extends BcFrontAppController
{

    /**
     * Constructor
     *
     * @param ServerRequest|null $request
     * @param Response|null $response
     * @param string|null $name
     * @param EventManagerInterface|null $eventManager
     * @param ComponentRegistry|null $components
     */
    public function __construct(?ServerRequest $request = null, ?Response $response = null, ?string $name = null, ?EventManagerInterface $eventManager = null, ?ComponentRegistry $components = null)
    {
        parent::__construct($request, $response, $name, $eventManager, $components);
        $this->setName('Error');
        $this->setPlugin('');
    }

    /**
     * Initialization hook method.
     *
     * @return void
     * @noTodo
     * @checked
     * @unitTest
     */
    public function initialize(): void
    {
        $this->loadComponent('RequestHandler');
        // エラー時にはサービスプロバイダーが登録されず、エラー画面表示時にヘルパーでサービスが見つからずにエラーとなってしまう。
        // 画面表示時にさらにエラーになるとテーマが適用されなくなってしまうためここでサービスプロバイダーを登録する
        BcContainer::get()->addServiceProvider(new BcServiceProvider());
    }

    /**
     * beforeRender callback.
     *
     * @param \Cake\Event\EventInterface $event Event.
     * @return \Cake\Http\Response|null
     * @noTodo
     * @checked
     * @unitTest
     */
    public function beforeRender(EventInterface $event): void
    {
        parent::beforeRender($event);
        $this->viewBuilder()->setTemplatePath('Error');
    }

}
