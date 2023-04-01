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

namespace BcCustomContent\Service\Front;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Service\Front\BcFrontContentsService;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcSiteConfig;
use BaserCore\Utility\BcUtil;
use BcCustomContent\Model\Entity\CustomContent;
use BcCustomContent\Service\CustomContentsService;
use BcCustomContent\Service\CustomContentsServiceInterface;
use BcCustomContent\Service\CustomEntriesService;
use BcCustomContent\Service\CustomEntriesServiceInterface;
use BcCustomContent\Service\CustomTablesService;
use BcCustomContent\Service\CustomTablesServiceInterface;
use Cake\Controller\Controller;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\Http\Exception\NotFoundException;

/**
 * CustomContentFrontService
 */
class CustomContentFrontService extends BcFrontContentsService implements CustomContentFrontServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * カスタムエントリーサービス
     *
     * @var CustomEntriesService
     */
    public $entriesService;

    /**
     * カスタムコンテンツサービス
     * @var CustomContentsService
     */
    public $contentsService;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->entriesService = $this->getService(CustomEntriesServiceInterface::class);
        $this->contentsService = $this->getService(CustomContentsServiceInterface::class);
    }

    /**
     * カスタムエントリーの単一データを取得する
     *
     * @param int $entityId
     * @param array $options
     * @return mixed
     */
    public function getCustomContent(int $entityId)
    {
        return $this->getService(CustomContentsServiceInterface::class)->get($entityId, [
            'status' => 'publish'
        ]);
    }

    /**
     * カスタムエントリーの一覧を取得する
     *
     * @param int $customTableId
     * @return mixed
     */
    public function getCustomEntries(CustomContent $customContent, array $queryParams = [])
    {

        $this->entriesService->setup($customContent->custom_table_id);
        $params = array_merge_recursive([
            'contain' => ['CustomTables' => ['CustomContents' => ['Contents']]],
            'status' => 'publish',
            'order' => $customContent->list_order,
            'direction' => $customContent->list_direction,
            'limit' => $customContent->list_count
        ], $queryParams);
        return $this->entriesService->getIndex($params);
    }

    /**
     * 一覧用の View 変数を取得する
     *
     * @param EntityInterface $customContent
     * @param ResultSetInterface $customEntries
     * @return array
     */
    public function getViewVarsForIndex(EntityInterface $customContent, ResultSetInterface $customEntries): array
    {
        /** @var CustomContent $customContent */
        /** @var CustomTablesService $customTables */
        $customTables = $this->getService(CustomTablesServiceInterface::class);
        // finder を threaded から all に変更
        $customTables->CustomTables->hasMany('CustomLinks')
            ->setClassName('BcCustomContent.CustomLinks')
            ->setForeignKey('custom_table_id')
            ->setSort(['CustomLinks.lft' => 'ASC'])
            ->setFinder('all');
        $customTable = $customTables->get($customContent->custom_table_id, [
            'contain' => [
                'CustomLinks' => [
                    'conditions' => ['CustomLinks.status' => true],
                    'CustomFields'
                ]
            ]
        ]);

        return [
            'customContent' => $customContent,
            'customEntries' => $customEntries,
            'customTable' => $customTable,
            'currentWidgetAreaId' => $customContent->widget_area?? BcSiteConfig::get('widget_area'),
            'editLink' => BcUtil::loginUser()? [
                'prefix' => 'Admin',
                'plugin' => 'BcCustomContent',
                'controller' => 'CustomContents',
                'action' => 'edit',
                $customContent->id
            ] : null,
        ];
    }

    /**
     * 詳細ページ用の View 変数を取得する
     *
     * @param EntityInterface $customContent
     * @param int $entryId
     * @return array
     */
    public function getViewVarsForView(EntityInterface $customContent, mixed $entryId)
    {
        $this->entriesService->setup($customContent->custom_table_id);
        $entity = $this->entriesService->get($entryId, ['status' => 'publish']);
        /** @var CustomContent $customContent */
        return [
            'customContent' => $customContent,
            'customEntry' => $entity,
            'currentWidgetAreaId' => $customContent->widget_area?? BcSiteConfig::get('widget_area'),
            'editLink' => BcUtil::loginUser()? [
                'prefix' => 'Admin',
                'plugin' => 'BcCustomContent',
                'controller' => 'CustomEntries',
                'action' => 'edit',
                $entity->custom_table_id,
                $entity->id
            ] : '',
        ];
    }

    /**
     * 一覧用のテンプレートを取得する
     *
     * @param CustomContent $customContent
     * @return string
     */
    public function getIndexTemplate(CustomContent $customContent): string
    {
        return 'CustomContent' . DS . $customContent->template . DS . 'index';
    }

    /**
     * 詳細ページ用のテンプレートを取得する
     *
     * @param CustomContent $customContent
     * @return string
     */
    public function getViewTemplate(CustomContent $customContent): string
    {
        return 'CustomContent' . DS . $customContent->template . DS . 'view';
    }

    /**
     * カスタムエントリーの詳細ページ用のプレビューのセットアップを行う
     *
     * @param Controller $controller
     */
    public function setupPreviewForView(Controller $controller): void
    {
        $request = $controller->getRequest();
        $entryId = $request->getParam('pass.0');
        if(!$entryId) throw new NotFoundException();

        $customContent = $this->contentsService->get($request->getParam('entityId'));
        $controller->set($this->getViewVarsForView($customContent, $entryId));
        $entity = $this->entriesService->CustomEntries->patchEntity(
            $controller->viewBuilder()->getVar('customEntry'),
            $request->getData()
        );
        $controller->set(['customEntry' => $entity]);

        // テンプレートの変更
        $controller->viewBuilder()->setTemplate($this->getViewTemplate($customContent));
    }

    /**
     * カスタムエントリーの詳細ページ用のプレビューのセットアップを行う
     *
     * @param Controller $controller
     */
    public function setupPreviewForIndex(Controller $controller): void
    {
        $request = $controller->getRequest();
        $customContent = $this->contentsService->get($request->getParam('entityId'));
        $customContent = $this->contentsService->CustomContents->patchEntity($customContent, $request->getData());
        $controller->setRequest($request->withAttribute('currentContent', $customContent->content));
        $controller->set($this->getViewVarsForIndex(
            $customContent,
            $controller->paginate($this->getCustomEntries($customContent))
        ));

        // テンプレートの変更
        $controller->viewBuilder()->setTemplate($this->getIndexTemplate($customContent));
    }

}
