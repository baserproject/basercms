<?php
declare(strict_types=1);

namespace BcMcp\Mcp\BcCustomContent;

use BcCustomContent\Service\CustomContentsService;
use BcCustomContent\Service\CustomContentsServiceInterface;
use PhpMcp\Server\ServerBuilder;
use BcMcp\Mcp\BaseMcpTool;

/**
 * カスタムコンテンツツールクラス
 *
 * カスタムコンテンツのCRUD操作を提供
 */
class CustomContentsTool extends BaseMcpTool
{

    /**
     * カスタムコンテンツ関連のツールを ServerBuilder に追加
     */
    public function addToolsToBuilder(ServerBuilder $builder): ServerBuilder
    {
        return $builder
            ->withTool(
                handler: [self::class, 'addCustomContent'],
                name: 'addCustomContent',
                description: 'カスタムテーブルと紐づくカスタムコンテンツを追加します。カスタムコンテンツを追加するにはカスタムテーブルのIDが必要です。事前に作成するか既存のカスタムテーブルIDを指定してください。',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'name' => ['type' => 'string', 'description' => 'カスタムコンテンツ名、URLに影響します（必須）'],
                        'title' => ['type' => 'string', 'description' => 'カスタムコンテンツのタイトル（必須）'],
                        'customTableId' => ['type' => 'number', 'description' => 'カスタムテーブルID（必須）'],
                        'siteId' => ['type' => 'number', 'default' => 1, 'description' => 'サイトID（初期値: 1）'],
                        'parentId' => ['type' => 'number', 'default' => 1, 'description' => '親フォルダID（初期値: 1）'],
                        'description' => ['type' => 'string', 'description' => '説明文'],
                        'authorId' => ['type' => 'number', 'default' => 1, 'description' => '作成者ID'],
                        'layoutTemplate' => ['type' => 'string', 'description' => 'レイアウトテンプレート名（初期値: default）'],
                        'status' => ['type' => 'number', 'description' => '公開状態（0: 非公開状態, 1: 公開状態）'],
                        'publishBegin' => ['type' => 'string', 'description' => '公開開始日時（YYYY-MM-DD HH:MM:SS形式）'],
                        'publishEnd' => ['type' => 'string', 'description' => '公開終了日時（YYYY-MM-DD HH:MM:SS形式）'],
                        'excludeSearch' => ['type' => 'boolean', 'description' => '検索結果から除外するかどうか（初期値: false）'],
                        'excludeMenu' => ['type' => 'boolean', 'description' => 'メニューから除外するかどうか（初期値: false）'],
                        'blankLink' => ['type' => 'boolean', 'description' => 'リンクを新しいタブで開くかどうか（初期値: false）'],
                        'template' => ['type' => 'string', 'default' => 'default', 'description' => 'テンプレート名（初期値: default）'],
                        'widgetArea' => ['type' => 'number', 'description' => 'ウィジェットエリアID（初期値: システムのデフォルト）'],
                        'listCount' => ['type' => 'number', 'default' => 10, 'description' => 'リスト表示件数（初期値: 10）'],
                        'listOrder' => ['type' => 'string', 'default' => 'id', 'description' => 'リスト表示順序（初期値: published）'],
                        'listDirection' => ['type' => 'string', 'enum' => ['ASC', 'DESC'], 'default' => 'DESC', 'description' => 'リスト表示方向（ASC|DESC、初期値: DESC）'],
                    ],
                    'required' => ['name', 'title', 'customTableId']
                ]
            )
            ->withTool(
                handler: [self::class, 'getCustomContents'],
                name: 'getCustomContents',
                description: 'カスタムテーブルと紐づくカスタムコンテンツの一覧を取得します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'limit' => ['type' => 'number', 'description' => '取得件数（省略時は制限なし）'],
                        'page' => ['type' => 'number', 'description' => 'ページ番号（省略時は1ページ目）'],
                        'status' => ['type' => 'number', 'description' => '公開ステータス（null: 非公開, publish: 公開）']
                    ]
                ]
            )
            ->withTool(
                handler: [self::class, 'getCustomContent'],
                name: 'getCustomContent',
                description: 'カスタムテーブルと紐づくカスタムコンテンツをIDを指定して取得します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => 'カスタムコンテンツID（必須）']
                    ],
                    'required' => ['id']
                ]
            )
            ->withTool(
                handler: [self::class, 'editCustomContent'],
                name: 'editCustomContent',
                description: 'カスタムテーブルと紐づくカスタムコンテンツを編集します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => 'カスタムコンテンツID（必須）'],
                        'name' => ['type' => 'string', 'description' => 'カスタムコンテンツ名、URLに影響します'],
                        'title' => ['type' => 'string', 'description' => 'カスタムコンテンツのタイトル'],
                        'customTableId' => ['type' => 'number', 'description' => 'カスタムテーブルID'],
                        'siteId' => ['type' => 'number', 'default' => 1, 'description' => 'サイトID'],
                        'parentId' => ['type' => 'number', 'default' => 1, 'description' => '親フォルダID'],
                        'description' => ['type' => 'string', 'description' => '説明文'],
                        'authorId' => ['type' => 'number', 'default' => 1, 'description' => '作成者ID'],
                        'layoutTemplate' => ['type' => 'string', 'description' => 'レイアウトテンプレート名'],
                        'status' => ['type' => 'number', 'description' => '公開状態（0: 非公開状態, 1: 公開状態）'],
                        'publishBegin' => ['type' => 'string', 'description' => '公開開始日時（YYYY-MM-DD HH:MM:SS形式）'],
                        'publishEnd' => ['type' => 'string', 'description' => '公開終了日時（YYYY-MM-DD HH:MM:SS形式）'],
                        'excludeSearch' => ['type' => 'boolean', 'description' => '検索結果から除外するかどうか'],
                        'excludeMenu' => ['type' => 'boolean', 'description' => 'メニューから除外するかどうか'],
                        'blankLink' => ['type' => 'boolean', 'description' => 'リンクを新しいタブで開くかどうか'],
                        'template' => ['type' => 'string', 'default' => 'default', 'description' => 'テンプレート名'],
                        'widgetArea' => ['type' => 'number', 'description' => 'ウィジェットエリアID'],
                        'listCount' => ['type' => 'number', 'default' => 10, 'description' => 'リスト表示件数'],
                        'listOrder' => ['type' => 'string', 'default' => 'id', 'description' => 'リスト表示順序'],
                        'listDirection' => ['type' => 'string', 'enum' => ['ASC', 'DESC'], 'default' => 'DESC', 'description' => 'リスト表示方向（ASC|DESC）'],
                    ],
                    'required' => ['id']
                ]
            )
            ->withTool(
                handler: [self::class, 'deleteCustomContent'],
                name: 'deleteCustomContent',
                description: 'カスタムテーブルと紐づくカスタムコンテンツをIDを指定して削除します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => 'カスタムコンテンツID（必須）']
                    ],
                    'required' => ['id']
                ]
            );
    }

    /**
     * 権限チェック用のURLを取得する
     * @param $action
     * @param $args
     * @return false|string[]
     */
    public static function getPermissionUrl($action, $args = [])
    {
        switch ($action) {
            case 'addCustomContent':
                return ['POST' => "/bc-custom-content/custom_contents/add.json"];
            case 'editCustomContent':
                if(empty($args['id'])) return false;
                return ['POST' => "/bc-custom-content/custom_contents/edit/{$args['id']}.json"];
            case 'getCustomContents':
                return ['GET' => "/bc-custom-content/custom_contents/index.json"];
            case 'getCustomContent':
                if(empty($args['id'])) return false;
                return ['GET' => "/bc-custom-content/custom_contents/view/{$args['id']}.json"];
            case 'deleteCustomContent':
                if(empty($args['id'])) return false;
                return ['POST' => "/bc-custom-content/custom_contents/delete/{$args['id']}.json"];
            default:
                return false;
        }
    }

    /**
     * カスタムコンテンツを追加
     */
    public function addCustomContent(
        string $name,
        string $title,
        int $customTableId,
        ?int $siteId = 1,
        ?int $parentId = 1,
        ?string $description = null,
        ?int $authorId = null,
        ?string $layoutTemplate = null,
        ?bool $status = false,
        ?string $publishBegin = null,
        ?string $publishEnd = null,
        ?bool $excludeSearch = false,
        ?bool $excludeMenu = false,
        ?bool $blankLink = false,
        ?string $template = 'default',
        ?int $widgetArea = null,
        ?int $listCount = 10,
        ?string $listOrder = 'published',
        ?string $listDirection = 'DESC',
        ?int $loginUserId = null
    ): array
    {
        return $this->executeWithErrorHandling(function() use (
            $name, $title, $customTableId, $siteId, $parentId, $description, $authorId, $layoutTemplate, $status,
            $publishBegin, $publishEnd, $excludeSearch, $excludeMenu, $blankLink, $template, $widgetArea, $listCount,
            $listOrder, $listDirection, $loginUserId
        ) {

            /** @var CustomContentsService $customContentsService */
            $customContentsService = $this->getService(CustomContentsServiceInterface::class);

            // Content entity data structure required by baserCMS
            $data = [
                'name' => $name,
                'title' => $title,
                'custom_table_id' => $customTableId,
                'description' => $description,
                'template' => $template,
                'widget_area' => $widgetArea,
                'list_count' => $listCount,
                'list_direction' => $listDirection,
                'list_order' => $listOrder,
                'content' => [
                    'name' => $name,
                    'plugin' => 'BcCustomContent',
                    'type' => 'CustomContent',
                    'title' => $title,
                    'description' => $description ?? '',
                    'site_id' => $siteId,
                    'parent_id' => $parentId,
                    'author_id' => $authorId ?? $loginUserId ?? 1,
                    'layout_template' => $layoutTemplate ?? '',
                    'exclude_search' => $excludeSearch,
                    'self_status' => $status ?? false,
                    'publish_begin' => $publishBegin ?? null,
                    'publish_end' => $publishEnd ?? null,
                    'exclude_menu' => $excludeMenu ?? false,
                    'blank_link' => $blankLink ?? false
                ]
            ];

            $result = $customContentsService->create($data);

            if ($result) {
                return $this->createSuccessResponse(
                    $result->toArray(),
                    [],
                    sprintf('カスタムコンテンツ「%s」を追加しました。', $result->content->title),
                    $loginUserId
                );
            } else {
                return $this->createErrorResponse('カスタムコンテンツの保存に失敗しました');
            }
        });
    }

    /**
     * カスタムコンテンツを編集
     */
    public function editCustomContent(
        int $id,
        string $name = null,
        string $title = null,
        int $customTableId = null,
        ?int $siteId = null,
        ?int $parentId = null,
        ?string $description = null,
        ?string $authorId = null,
        ?string $layoutTemplate = null,
        ?bool $status = false,
        ?string $publishBegin = null,
        ?string $publishEnd = null,
        ?bool $excludeSearch = false,
        ?bool $excludeMenu = false,
        ?bool $blankLink = false,
        ?string $template = null,
        ?int $widgetArea = null,
        ?int $listCount = null,
        ?string $listOrder = null,
        ?string $listDirection = null,
        ?int $loginUserId = null
    ): array
    {
        return $this->executeWithErrorHandling(function() use (
            $id, $name, $title, $customTableId, $siteId, $parentId, $description, $authorId, $layoutTemplate, $status,
            $publishBegin, $publishEnd, $excludeSearch, $excludeMenu, $blankLink, $template, $widgetArea, $listCount,
            $listOrder, $listDirection, $loginUserId
        ) {
            /** @var CustomContentsService $customContentsService */
            $customContentsService = $this->getService(CustomContentsServiceInterface::class);

            $entity = $customContentsService->get($id);

            if (!$entity) return $this->createErrorResponse('指定されたIDのカスタムコンテンツが見つかりません');

            $data = [];
            if ($name !== null) $data['name'] = $name;
            if ($title !== null) $data['title'] = $title;
            if ($customTableId !== null) $data['custom_table_id'] = $customTableId;
            if ($siteId !== null) $data['content']['site_id'] = $siteId;
            if ($parentId !== null) $data['content']['parent_id'] = $parentId;
            if ($description !== null) $data['content']['description'] = $description;
            if ($authorId !== null) $data['content']['author_id'] = $authorId;
            if ($layoutTemplate !== null) $data['content']['layout_template'] = $layoutTemplate;
            if ($status !== null) $data['content']['self_status'] = $status;
            if ($publishBegin !== null) $data['content']['publish_begin'] = $publishBegin;
            if ($publishEnd !== null) $data['content']['publish_end'] = $publishEnd;
            if ($excludeSearch !== null) $data['content']['exclude_search'] = $excludeSearch;
            if ($excludeMenu !== null) $data['content']['exclude_menu'] = $excludeMenu;
            if ($blankLink !== null) $data['content']['blank_link'] = $blankLink;
            if ($description !== null) $data['description'] = $description;
            if ($template !== null) $data['template'] = $template;
            if ($widgetArea !== null) $data['widget_area'] = $widgetArea;
            if ($listCount !== null) $data['list_count'] = $listCount;
            if ($listOrder !== null) $data['list_order'] = $listOrder;
            if ($listDirection !== null) $data['list_direction'] = $listDirection;

            $result = $customContentsService->update($entity, $data);

            if ($result) {
                return $this->createSuccessResponse(
                    $result->toArray(),
                    [],
                    sprintf('カスタムコンテンツ「%s」を編集しました。', $result->content->title),
                    $loginUserId
                );
            } else {
                return $this->createErrorResponse('カスタムコンテンツの更新に失敗しました');
            }
        });
    }

    /**
     * カスタムコンテンツ一覧を取得
     */
    public function getCustomContents(
        ?string $status = null,
        ?int $limit = null,
        ?int $page = 1
    ): array
    {
        return $this->executeWithErrorHandling(function() use ($status, $limit, $page) {
            $customContentsService = $this->getService(CustomContentsServiceInterface::class);

            $conditions = [];
            if (isset($status)) $conditions['status'] = $status;
            if (!empty($limit)) $conditions['limit'] = $limit;
            if (!empty($page)) $conditions['page'] = $page;

            $results = $customContentsService->getIndex($conditions)->toArray();

            return $this->createSuccessResponse([
                'data' => $results,
                'pagination' => [
                    'page' => $page ?? 1,
                    'limit' => $limit ?? null,
                    'count' => count($results)
                ]
            ]);
        });
    }

    /**
     * カスタムコンテンツを取得
     */
    public function getCustomContent(int $id): array
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            /** @var CustomContentsService $customContentsService */
            $customContentsService = $this->getService(CustomContentsServiceInterface::class);
            $result = $customContentsService->get($id);

            if ($result) {
                return $this->createSuccessResponse($result->toArray());
            } else {
                return $this->createErrorResponse('指定されたIDのカスタムコンテンツが見つかりません');
            }
        });
    }

    /**
     * カスタムコンテンツを削除
     */
    public function deleteCustomContent(int $id, ?int $loginUserId = null): array
    {
        return $this->executeWithErrorHandling(function() use ($id, $loginUserId) {
            /** @var CustomContentsService $customContentsService */
            $customContentsService = $this->getService(CustomContentsServiceInterface::class);

            // 削除前にタイトルを取得
            $entity = $customContentsService->get($id);
            if (!$entity) {
                return $this->createErrorResponse('指定されたIDのカスタムコンテンツが見つかりません');
            }

            $title = $entity->content->title;
            $result = $customContentsService->delete($id);

            if ($result) {
                return $this->createSuccessResponse(
                    'カスタムコンテンツを削除しました',
                    [],
                    sprintf('カスタムコンテンツ「%s」を削除しました。', $title),
                    $loginUserId
                );
            } else {
                return $this->createErrorResponse('カスタムコンテンツの削除に失敗しました');
            }
        });
    }
}
