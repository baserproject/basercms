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
use BaserCore\Service\Admin\ContentsAdminServiceInterface;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Event\EventManager;
use Cake\ORM\TableRegistry;
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
     * エンティティの変数名
     * @var string
     */
    protected $entityVarName;

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
            throw new BcException(__d('baser', '編集画面で利用するエンティティの変数名を entityVarName として定義してください。'));
        }
        $this->entityVarName = $config['entityVarName'];
        $this->ContentsService = $this->getService(ContentsAdminServiceInterface::class);
        $this->SiteConfigsService = $this->getService(SiteConfigsServiceInterface::class);
        $this->Sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
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
        if (in_array($request->getParam('action'), [$this->editAction, 'edit_alias'])) {
            $this->settingForm();
            EventManager::instance()->on(new BcContentsEventListener($this->entityVarName));
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
        $entityName = Inflector::classify($controller->getName());

        if ($entityName === "Content") {
            $content = $controller->viewBuilder()->getVar(Inflector::variable($entityName));
            $entityName = Inflector::pluralize($entityName) . ".";
        } else {
            $associated = $controller->viewBuilder()->getVar(Inflector::variable($entityName));
            $content = $associated->content;
            $entityName = Inflector::pluralize($entityName) . ".content.";
        }
        $site = $content->site;
        $theme = $site->theme;
        $templates = BcUtil::getTemplateList('Layouts', [$controller->getPlugin(), $theme]);
        if ($content->id != 1) {
            $parentTemplate = $this->ContentsService->getParentLayoutTemplate($content->id);
            if (in_array($parentTemplate, $templates)) {
                unset($templates[$parentTemplate]);
            }
            $templates = array_merge($templates, ['' => __d('baser', '親フォルダの設定に従う') . '（' . $parentTemplate . '）']);
        }
        $controller->set('layoutTemplates', $templates);

        if (Configure::read('BcApp.autoUpdateContentCreatedDate')) {
            $content->modified_date = date('Y-m-d H:i:s');
        }
        $siteList = $this->Sites->find('list', ['fields' => ['id', 'display_name']]);
        $controller->set('sites', $siteList);
        $controller->set('mainSiteDisplayName', $this->SiteConfigsService->getValue('main_site_display_name'));
        $controller->set('relatedContents', $this->Sites->getRelatedContents($content->id));

        if (!$entityName === "content") $associated->content = $content;
        $controller->set('entityName', $entityName);
        $controller->set($this->ContentsService->getViewVarsForEdit($content));
    }
}
