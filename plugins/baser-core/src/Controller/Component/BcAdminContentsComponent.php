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

namespace BaserCore\Controller\Component;

use BaserCore\Error\BcException;
use BaserCore\Event\BcContentsEventListener;
use BaserCore\Service\Admin\BcAdminContentsServiceInterface;
use BaserCore\Service\Admin\ContentsAdminServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Event\EventManager;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class BcContentsComponent
 *
 * 階層コンテンツと連携したフォーム画面を作成する為のコンポーネント
 *
 * @property ContentsAdminServiceInterface $ContentsService
 */
class BcAdminContentsComponent extends Component
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * コンテンツ編集用のアクション名
     * 判定に利用
     * settings で指定する
     *
     * @var string
     */
    public $editAction = 'edit';

	/**
	 * コンテンツ新規登録用のアクション名
	 * 判定に利用
	 * settings で指定する
	 *
	 * @var string
	 */
	public $addAction = 'add';

    /**
     * Initialize
     *
     * @param array $config
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        if (!isset($config['entityVarName'])) {
            throw new BcException(__d('baser_core', '編集画面で利用するエンティティの変数名を entityVarName として定義してください。'));
        }
        $this->setupAdmin();
    }

    /**
     * 管理システム設定
     *
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function setupAdmin(): void
    {
        $this->setConfig('items', BcUtil::getContentsItem());
    }

    /**
     * Before render
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeRender(): void
    {
        $controller = $this->getController();
        $request = $controller->getRequest();
        $controller->set('contentsItems', $this->getConfig('items'));
        if ($this->getConfig('useForm') && in_array($request->getParam('action'), [$this->addAction, $this->editAction, 'edit_alias'])) {
            $this->settingForm();
        }
    }

    /**
     * コンテンツ保存フォームを設定する
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function settingForm()
    {
        $controller = $this->getController();
        $request = $controller->getRequest();
        $entityName = $this->getConfig('entityVarName')?? Inflector::classify($controller->getName());
        EventManager::instance()->on(new BcContentsEventListener($entityName));

        if ($entityName === "content") {
            $content = $controller->viewBuilder()->getVar(Inflector::variable($entityName));
        } else {
            $associated = $controller->viewBuilder()->getVar(Inflector::variable($entityName));
            if(!isset($associated->content)) {
                throw new BcException(__d('baser_core', '編集画面で利用するエンティティに content プロパティが定義されていません。
                    エンティティを取得する際に、contain を利用して、[\'Contents\' => [\'Sites\']] を指定してください。'));
            }
            $content = $associated->content;
        }
        $controller->getRequest()->getSession()->write('BcApp.Admin.currentSite', $content->site);
        $controller->setRequest($controller->getRequest()->withAttribute('currentSite', $content->site));
        Router::setRequest($controller->getRequest());

        if (Configure::read('BcApp.autoUpdateContentCreatedDate')) {
            $content->modified_date = date('Y-m-d H:i:s');
        }
        /* @var \BaserCore\Service\Admin\BcAdminContentsService $bcAdminContentsService */
        $bcAdminContentsService = $this->getService(BcAdminContentsServiceInterface::class);
        if($request->getParam('action') === $this->addAction) {
            $controller->set($bcAdminContentsService->getViewVarsForAdd($content));
        } else {
            $controller->set($bcAdminContentsService->getViewVarsForEdit($content));
        }
    }
}
