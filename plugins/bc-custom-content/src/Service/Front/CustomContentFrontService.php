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
use BaserCore\Error\BcException;
use BaserCore\Service\Front\BcFrontContentsService;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcSiteConfig;
use BaserCore\Utility\BcUtil;
use BcCcFile\Utility\BcCcFileUtil;
use BcCustomContent\Model\Entity\CustomContent;
use BcCustomContent\Service\CustomContentsService;
use BcCustomContent\Service\CustomContentsServiceInterface;
use BcCustomContent\Service\CustomEntriesService;
use BcCustomContent\Service\CustomEntriesServiceInterface;
use BcCustomContent\Service\CustomTablesService;
use BcCustomContent\Service\CustomTablesServiceInterface;
use Cake\Controller\Controller;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\Paging\PaginatedInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\TableRegistry;

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
    public CustomEntriesServiceInterface|CustomEntriesService $EntriesService;

    /**
     * カスタムコンテンツサービス
     * @var CustomContentsService
     */
    public CustomContentsServiceInterface|CustomContentsService $ContentsService;

    /**
     * Constructor
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct()
    {
        $this->EntriesService = $this->getService(CustomEntriesServiceInterface::class);
        $this->ContentsService = $this->getService(CustomContentsServiceInterface::class);
    }

    /**
     * カスタムエントリーの単一データを取得する
     *
     * @param int $entityId
     * @param array $options
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest
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
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getCustomEntries(CustomContent $customContent, array $queryParams = [])
    {

        $this->EntriesService->setup($customContent->custom_table_id);
        $params = array_merge([
            'contain' => ['CustomTables' => ['CustomContents' => ['Contents']]],
            'status' => 'publish',
            'order' => $customContent->list_order,
            'direction' => $customContent->list_direction,
            'limit' => $customContent->list_count,
            'custom_content_id' => $customContent->id
        ], $queryParams);
        return $this->EntriesService->getIndex($params);
    }

    /**
     * 一覧用の View 変数を取得する
     *
     * @param EntityInterface $customContent
     * @param PaginatedInterface $customEntries
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForIndex(EntityInterface $customContent, PaginatedInterface $customEntries): array
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
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForView(EntityInterface $customContent, mixed $entryId, bool $preview = false)
    {
        $this->EntriesService->setup($customContent->custom_table_id);
        if($preview) {
            $entity = null;
            if($entryId) {
                $entity = $this->EntriesService->get($entryId);
            }
        } else {
            $options = ['status' => 'publish'];
            $entity = $this->EntriesService->get($entryId, $options);
        }

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
                $customContent->custom_table_id,
                $entity->id?? null
            ] : '',
        ];
    }

    /**
     * 一覧用のテンプレートを取得する
     *
     * @param CustomContent $customContent
     * @return string
     * @checked
     * @noTodo
     * @unitTest
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
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewTemplate(CustomContent $customContent): string
    {
        return 'CustomContent' . DS . $customContent->template . DS . 'view';
    }

    /**
     * カスタムエントリーの詳細ページ用のプレビューのセットアップを行う
     *
     * @param Controller $controller
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setupPreviewForView(Controller $controller): void
    {
        $request = $controller->getRequest();
        $entryId = $request->getParam('pass.0');

        $customContent = $this->ContentsService->get($request->getParam('entityId'));
        $controller->set($this->getViewVarsForView($customContent, $entryId, true));
        $customEntry = $controller->viewBuilder()->getVar('customEntry');

        BcCcFileUtil::setupUploader($customContent->custom_table_id);

        $customEntriesTable = TableRegistry::getTableLocator()->get('BcCustomContent.CustomEntries');
        $events = BcUtil::offEvent($customEntriesTable->getEventManager(), 'Model.beforeMarshal');

        $postEntity = $customEntriesTable->saveTmpFiles($request->getData(), mt_rand(0, 99999999));
        $postEntity = $postEntity?$postEntity->toArray(): $request->getData();

        $entity = $customEntriesTable->patchEntity(
            $customEntry ?? $customEntriesTable->newEmptyEntity(),
            $postEntity
        );

        BcUtil::onEvent($customEntriesTable->getEventManager(), 'Model.beforeMarshal', $events);

        $entity = $customEntriesTable->decodeRow($entity);
        $controller->set(['customEntry' => $entity]);

        // テンプレートの変更
        $controller->viewBuilder()->setTemplate($this->getViewTemplate($customContent));
    }

    /**
     * カスタムエントリーの詳細ページ用のプレビューのセットアップを行う
     *
     * @param Controller $controller
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setupPreviewForIndex(Controller $controller): void
    {
        $request = $controller->getRequest();
        $customContent = $this->ContentsService->get($request->getParam('entityId'));
        $customContent = $this->ContentsService->CustomContents->patchEntity($customContent, $request->getData());
        $controller->setRequest($request->withAttribute('currentContent', $customContent->content));

        $controller->setRequest($controller->getRequest()->withQueryParams(array_merge([
            'limit' => $customContent->list_count,
            'sort' => $customContent->list_order,
            'direction' => $customContent->list_direction
        ], $controller->getRequest()->getQueryParams())));

        if(!$customContent->custom_table_id) {
            $controller->viewBuilder()->setTheme('BcThemeSample');
             throw new NotFoundException(__d('baser_core', 'テーブルを指定してください。'));
        }

        $controller->set($this->getViewVarsForIndex(
            $customContent,
            $controller->paginate($this->getCustomEntries($customContent, [
                'status' => null,
                'contain' => ['CustomTables']
            ]))
        ));

        // テンプレートの変更
        $controller->viewBuilder()->setTemplate($this->getIndexTemplate($customContent));
    }

}
