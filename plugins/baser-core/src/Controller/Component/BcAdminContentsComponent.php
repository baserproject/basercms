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

namespace BaserCore\Controller\Component;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Cake\Event\EventManager;
use BaserCore\Utility\BcUtil;
use Cake\Controller\Component;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Event\BcContentsEventListener;
use BaserCore\Service\ContentServiceInterface;
use BaserCore\Service\SiteConfigServiceInterface;

/**
 * Class BcContentsComponent
 *
 * 階層コンテンツと連携したフォーム画面を作成する為のコンポーネント
 *
 * @package BaserCore\Controller\Component
 * @property ContentServiceInterface $ContentService
 */
class BcAdminContentsComponent extends Component
{
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
     * Initialize
     *
     * @param array $config
     * @return void
     * @checked
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->ContentService = $this->getService(ContentServiceInterface::class);
        $this->SiteConfigService = $this->getService(SiteConfigServiceInterface::class);
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
     */
    public function beforeRender(): void
    {
        $controller = $this->getController();
        $request = $controller->getRequest();
        $controller->set('contentsItems', $this->getConfig('items'));
        if (in_array($request->getParam('action'), [$this->editAction, 'edit_alias'])) {
            // フォームをセット
            $this->settingForm();
            // TODO 改善要
            // フォームを読み込む為のイベントを設定
            // 内部で useForm を参照できない為、ここに記述。
            // フォームの設定しかできないイベントになってしまっている。
            EventManager::instance()->on(new BcContentsEventListener());
        }
    }

    /**
     * コンテンツ保存フォームを設定する
     * @return void
     */
    public function settingForm()
    {
        $controller = $this->getController();
        $entityName = Inflector::variable(Inflector::classify($controller->getName()));

        if ($entityName === "content") {
            $content = $controller->viewBuilder()->getVar($entityName);
            $contentPath = Inflector::classify($entityName) . ".";
        } else {
            $associated = $controller->viewBuilder()->getVar($entityName);
            $content = $associated->content;
            $contentPath = Inflector::classify($entityName) . ".content.";
        }
        $theme = $content->site->theme;

        $templates = array_merge(
            BcUtil::getTemplateList('Layouts', '', $theme),
            BcUtil::getTemplateList('Layouts', $controller->getPlugin(), $theme)
        );
        if ($content->id != 1) {
            $parentTemplate = $this->ContentService->getParentLayoutTemplate($content->id);
            if (in_array($parentTemplate, $templates)) {
                unset($templates[$parentTemplate]);
            }
            array_unshift($templates, ['' => __d('baser', '親フォルダの設定に従う') . '（' . $parentTemplate . '）']);
        }
        $controller->set('layoutTemplates', $templates);

        $content->name = urldecode($content->name);
        if (Configure::read('BcApp.autoUpdateContentCreatedDate')) {
            $content->modified_date = date('Y-m-d H:i:s');
        }

        $siteList = $this->Sites->find('list', ['fields' => ['id', 'display_name']]);
        $controller->set('sites', $siteList);
        $controller->set('mainSiteDisplayName', $this->SiteConfigService->getValue('main_site_display_name'));
        $controller->set('mainSiteId', $content->site->main_site_id);
        $controller->set('relatedContents', $this->Sites->getRelatedContents($content->id));
        $related = false;
        if (($content->site->relate_main_site && $content->main_site_content_id && $content->alias_id) ||
            $content->site->relate_main_site && $content->main_site_content_id && $content->type == 'ContentFolder') {
            $related = true;
        }
        $disableEditContent = false;

        if (!$entityName === "content") $associated->content = $content;

        if (!BcUtil::isAdminUser() || ($content->site->relate_main_site && $content->main_site_content_id &&
                ($content->alias_id || $content->type == 'ContentFolder'))) {
            $disableEditContent = true;
        }

        $controller->set('content', $content);
        $controller->set('contentPath', $contentPath);
        $controller->set('currentSiteId', $content->site_id);
        $controller->set('disableEditContent', $disableEditContent);
        $controller->set('related', $related);
    }
}
