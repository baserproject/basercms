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
use BaserCore\Annotation\Note;
use Cake\Controller\Component;
use BaserCore\Annotation\NoTodo;
use BaserCore\Error\BcException;
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
     * @noTodo
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
     * @note(value="TODO内容を江頭さんに確認")
     */
    public function beforeRender(): void
    {
        $controller = $this->getController();
        $request = $controller->getRequest();
        $controller->set('contentsItems', $this->getConfig('items'));
        if (in_array($request->getParam('action'), [$this->editAction, 'edit_alias'])) {
            // フォームをセット
            $this->settingForm();
            // TODO ucmitz 改善要
            // フォームを読み込む為のイベントを設定
            // 内部で useForm を参照できない為、ここに記述。
            // フォームの設定しかできないイベントになってしまっている。
            EventManager::instance()->on(new BcContentsEventListener());
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
        $entityName = Inflector::variable(Inflector::classify($controller->getName()));

        if ($entityName === "content") {
            $content = $controller->viewBuilder()->getVar($entityName);
        } else {
            $associated = $controller->viewBuilder()->getVar($entityName);
            $content = $associated->content;
        }
        $site = $content->site;
        $theme = $site->theme;
        $templates = BcUtil::getTemplateList('Layouts', [$controller->getPlugin(), $theme]);
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
        $controller->set('mainSiteId', $site->main_site_id);
        $controller->set('relatedContents', $this->Sites->getRelatedContents($content->id));
        $related = false;
        if (($site->relate_main_site && $content->main_site_content_id && $content->alias_id) ||
            $site->relate_main_site && $content->main_site_content_id && $content->type == 'ContentFolder') {
            $related = true;
        }
        if (!$entityName === "content") $associated->content = $content;
        // ContentController以外の場合適切にformIdを生成するためcontentEntitiesを確認
        if ($controller->getName() !== 'Contents') $this->checkContentEntities($controller);
        $controller->set('content', $content);
        $controller->set('currentSiteId', $content->site_id);
        $controller->set('related', $related);
        $controller->set('publishLink', $this->ContentService->getUrl($content->url, true, $site->useSubDomain));
    }

    /**
     * 適切にContentEntitiesが設定されてるか確認する
     *
     * @return void
     * @throws BcException
     * @checked
     * @unitTest
     * @noTodo
     */
    protected function checkContentEntities($controller)
    {
        $entities = $controller->viewBuilder()->getVar('contentEntities');
        if (is_array($entities) && count($entities) === 2 && array_key_exists('Content', $entities)) {
            if (array_key_first($entities) === 'Content') {
                $entities = array_reverse($entities);
                $controller->set('contentEntities', $entities);
            }
        } else {
            throw new BcException(__d('baser', 'contentEntitiesが適切に設定されていません'));
        }
    }
}
