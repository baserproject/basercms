<?php
declare(strict_types=1);

namespace BcMcp\Mcp\BcBlog;

use BcBlog\Service\BlogContentsService;
use Cake\Core\Configure;
use BcMcp\Mcp\BaseMcpTool;
use BcBlog\Service\BlogContentsServiceInterface;
use PhpMcp\Server\ServerBuilder;

/**
 * ブログコンテンツツールクラス
 *
 * ブログコンテンツのCRUD操作を提供
 */
class BlogContentsTool extends BaseMcpTool
{

    /**
     * ブログコンテンツ関連のツールを ServerBuilder に追加
     */
    public function addToolsToBuilder(ServerBuilder $builder): ServerBuilder
    {
        return $builder
            ->withTool(
                handler: [self::class, 'addBlogContent'],
                name: 'addBlogContent',
                description: 'baserCMSは複数のブログを持つことができます。一つ一つのブログをブログコンテンツと呼び、そのブログコンテンツを追加します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'name' => ['type' => 'string', 'description' => 'ブログコンテンツ名、URLに影響します（必須）'],
                        'title' => ['type' => 'string', 'description' => 'ブログコンテンツのタイトル（必須）'],
                        'siteId' => ['type' => 'number', 'description' => 'サイトID（省略時は1）'],
                        'parentId' => ['type' => 'number', 'description' => '親ID（省略時は1）'],
                        'description' => ['type' => 'string', 'description' => '説明文'],
                        'authorId' => ['type' => 'number', 'default' => 1, 'description' => '作成者ID'],
                        'layoutTemplate' => ['type' => 'string', 'description' => 'レイアウトテンプレート名（初期値: default）'],
                        'status' => ['type' => 'number', 'description' => '公開状態（0: 非公開状態, 1: 公開状態）、（省略時は0）'],
                        'publishBegin' => ['type' => 'string', 'description' => '公開開始日時（YYYY-MM-DD HH:MM:SS形式）'],
                        'publishEnd' => ['type' => 'string', 'description' => '公開終了日時（YYYY-MM-DD HH:MM:SS形式）'],
                        'excludeSearch' => ['type' => 'boolean', 'description' => '検索結果から除外するかどうか（初期値: false）'],
                        'excludeMenu' => ['type' => 'boolean', 'description' => 'メニューから除外するかどうか（初期値: false）'],
                        'blankLink' => ['type' => 'boolean', 'description' => 'リンクを新しいタブで開くかどうか（初期値: false）'],
                        'template' => ['type' => 'string', 'description' => 'テンプレート名（省略時は "default"）'],
                        'listCount' => ['type' => 'number', 'description' => '一覧表示件数（省略時は10）'],
                        'listDirection' => ['type' => 'string', 'enum' => ['ASC', 'DESC'], 'description' => '一覧表示方向（ASC|DESC）、（省略時はDESC）'],
                        'feedCount' => ['type' => 'number', 'description' => 'RSSフィードに表示する件数（省略時は10）'],
                        'commentUse' => ['type' => 'boolean', 'description' => 'コメント機能を使用するか（省略時はfalse）'],
                        'commentApprove' => ['type' => 'boolean', 'description' => 'コメント機能について各コメントの公開について承認制にするか（省略時はfalse）'],
                        'tagUse' => ['type' => 'boolean', 'description' => 'タグ機能を使用するか（省略時はfalse）'],
                        'eyeCatchSizeThumbWidth' => ['type' => 'number', 'description' => 'アイキャッチサムネイル幅（PC）（省略時はシステムデフォルト値）'],
                        'eyeCatchSizeThumbHeight' => ['type' => 'number', 'description' => 'アイキャッチサムネイル高さ（PC）（省略時はシステムデフォルト値）'],
                        'eyeCatchSizeMobileThumbWidth' => ['type' => 'number', 'description' => 'アイキャッチサムネイル幅（モバイル）（省略時はシステムデフォルト値）'],
                        'eyeCatchSizeMobileThumbHeight' => ['type' => 'number', 'description' => 'アイキャッチサムネイル高さ（モバイル）（省略時はシステムデフォルト値）'],
                        'useContent' => ['type' => 'boolean', 'description' => '概要入力欄を使用するか（省略時はfalse）'],
                        'widgetArea' => ['type' => 'number', 'description' => 'ウィジェットエリアID（省略時はシステムデフォルト値）']
                    ],
                    'required' => ['name', 'title']
                ]
            )
            ->withTool(
                handler: [self::class, 'editBlogContent'],
                name: 'editBlogContent',
                description: 'baserCMSは複数のブログを持つことができます。一つ一つのブログをブログコンテンツと呼び、指定されたIDのブログコンテンツを編集します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => 'ブログコンテンツID（必須）'],
                        'name' => ['type' => 'string', 'description' => 'ブログコンテンツ名、URLに影響します'],
                        'title' => ['type' => 'string', 'description' => 'ブログコンテンツのタイトル'],
                        'siteId' => ['type' => 'number', 'description' => 'サイトID'],
                        'parentId' => ['type' => 'number', 'description' => '親ID'],
                        'description' => ['type' => 'string', 'description' => '説明文'],
                        'authorId' => ['type' => 'number', 'default' => 1, 'description' => '作成者ID'],
                        'layoutTemplate' => ['type' => 'string', 'description' => 'レイアウトテンプレート名'],
                        'status' => ['type' => 'number', 'description' => '公開状態（0: 非公開状態, 1: 公開状態）'],
                        'publishBegin' => ['type' => 'string', 'description' => '公開開始日時（YYYY-MM-DD HH:MM:SS形式）'],
                        'publishEnd' => ['type' => 'string', 'description' => '公開終了日時（YYYY-MM-DD HH:MM:SS形式）'],
                        'excludeSearch' => ['type' => 'boolean', 'description' => '検索結果から除外するかどうか'],
                        'excludeMenu' => ['type' => 'boolean', 'description' => 'メニューから除外するかどうか'],
                        'blankLink' => ['type' => 'boolean', 'description' => 'リンクを新しいタブで開くかどうか'],
                        'template' => ['type' => 'string', 'description' => 'テンプレート名'],
                        'listCount' => ['type' => 'number', 'description' => '一覧表示件数'],
                        'listDirection' => ['type' => 'string', 'enum' => ['ASC', 'DESC'], 'description' => '一覧表示方向（ASC|DESC）'],
                        'feedCount' => ['type' => 'number', 'description' => 'RSSフィードに表示する件数'],
                        'commentUse' => ['type' => 'boolean', 'description' => 'コメント機能を使用するか'],
                        'commentApprove' => ['type' => 'boolean', 'description' => 'コメント機能について各コメントの公開について承認制にするか'],
                        'tagUse' => ['type' => 'boolean', 'description' => 'タグ機能を使用するか'],
                        'eyeCatchSizeThumbWidth' => ['type' => 'number', 'description' => 'アイキャッチサムネイル幅（PC）'],
                        'eyeCatchSizeThumbHeight' => ['type' => 'number', 'description' => 'アイキャッチサムネイル高さ（PC）'],
                        'eyeCatchSizeMobileThumbWidth' => ['type' => 'number', 'description' => 'アイキャッチサムネイル幅（モバイル）'],
                        'eyeCatchSizeMobileThumbHeight' => ['type' => 'number', 'description' => 'アイキャッチサムネイル高さ（モバイル）'],
                        'useContent' => ['type' => 'boolean', 'description' => '概要入力欄を使用するか'],
                        'widgetArea' => ['type' => 'number', 'description' => 'ウィジェットエリアID']
                    ],
                    'required' => ['id']
                ]
            )
            ->withTool(
                handler: [self::class, 'getBlogContents'],
                name: 'getBlogContents',
                description: 'baserCMSは複数のブログを持つことができます。一つ一つのブログをブログコンテンツと呼び、そのブログコンテンツの一覧を取得します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'title' => ['type' => 'string', 'description' => 'ブログコンテンツのタイトル（部分一致）'],
                        'status' => ['type' => 'number', 'description' => '公開ステータス（null: 全て, publish: 公開）'],
                        'limit' => ['type' => 'number', 'description' => '取得件数（省略時は制限なし）'],
                        'page' => ['type' => 'number', 'description' => 'ページ番号（省略時は1ページ目）'],
                    ]
                ]
            )
            ->withTool(
                handler: [self::class, 'getBlogContent'],
                name: 'getBlogContent',
                description: 'baserCMSは複数のブログを持つことができます。一つ一つのブログをブログコンテンツと呼び、指定されたIDのブログコンテンツを取得します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => 'ブログコンテンツID（必須）']
                    ],
                    'required' => ['id']
                ]
            )
            ->withTool(
                handler: [self::class, 'deleteBlogContent'],
                name: 'deleteBlogContent',
                description: 'baserCMSは複数のブログを持つことができます。一つ一つのブログをブログコンテンツと呼び、指定されたIDのブログコンテンツを削除します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => 'ブログコンテンツID（必須）']
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
            case 'addBlogContent':
                return ['POST' => "/bc-blog/blog_contents/add.json"];
            case 'editBlogContent':
                if(empty($args['id'])) return false;
                return ['POST' => "/bc-blog/blog_contents/edit/{$args['id']}.json"];
            case 'getBlogContents':
                return ['GET' => "/bc-blog/blog_contents/index.json"];
            case 'getBlogContent':
                if(empty($args['id'])) return false;
                return ['GET' => "/bc-blog/blog_contents/view/{$args['id']}.json"];
            case 'deleteBlogContent':
                if(empty($args['id'])) return false;
                return ['POST' => "/bc-blog/blog_contents/delete/{$args['id']}.json"];
            default:
                return false;
        }
    }

    /**
     * ブログコンテンツを追加
     */
    public function addBlogContent(
        string $name,
        string $title,
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
        ?int $listCount = 10,
        ?string $listDirection = 'DESC',
        ?int $feedCount = 10,
        ?bool $commentUse = false,
        ?bool $commentApprove = false,
        ?bool $tagUse = false,
        ?int $eyeCatchSizeThumbWidth = null,
        ?int $eyeCatchSizeThumbHeight = null,
        ?int $eyeCatchSizeMobileThumbWidth = null,
        ?int $eyeCatchSizeMobileThumbHeight = null,
        ?bool $useContent = false,
        ?int $widgetArea = null,
        ?int $loginUserId = null
    ): array
    {
        return $this->executeWithErrorHandling(function() use (
            $name, $title, $siteId, $parentId, $description, $authorId, $layoutTemplate, $status,
            $publishBegin, $publishEnd, $excludeSearch, $excludeMenu, $blankLink, $template, $listCount,
            $listDirection, $feedCount, $commentUse, $commentApprove, $tagUse, $eyeCatchSizeThumbWidth,
            $eyeCatchSizeThumbHeight, $eyeCatchSizeMobileThumbWidth, $eyeCatchSizeMobileThumbHeight,
            $useContent, $widgetArea, $loginUserId
        ) {
            $blogContentsService = $this->getService(BlogContentsServiceInterface::class);

            // baserCMSでは、BlogContentとContentの両方を作成する必要があります
            // Contentエンティティの基本データ
            $contentData = [
                'name' => $name,
                'plugin' => 'BcBlog',
                'type' => 'BlogContent',
                'title' => $title,
                'site_id' => $siteId,
                'parent_id' => $parentId,
                'description' => $description ?? '',
                'author_id' => $authorId ?? ($loginUserId ?? 1), // 作成者ID、指定がなければデフォルトユーザー
                'layout_template' => $layoutTemplate ?? '',
                'self_status' => (bool)$status,
                'publish_begin' => $publishBegin,
                'publish_end' => $publishEnd,
                'exclude_search' => $excludeSearch,
                'exclude_menu' => $excludeMenu,
                'blank_link' => $blankLink
            ];

            // BlogContentエンティティの基本データ
            $blogContentData = [
                'description' => $description ?? '',
                'template' => $template,
                'list_count' => $listCount,
                'list_direction' => $listDirection,
                'feed_count' => $feedCount,
                'comment_use' => $commentUse,
                'comment_approve' => $commentApprove,
                'tag_use' => $tagUse,
                'eye_catch_size_thumb_width' => $eyeCatchSizeThumbWidth ?? Configure::read('BcBlog.eye_catch_size_thumb_width'),
                'eye_catch_size_thumb_height' => $eyeCatchSizeThumbHeight ?? Configure::read('BcBlog.eye_catch_size_thumb_height'),
                'eye_catch_size_mobile_thumb_width' => $eyeCatchSizeMobileThumbWidth ?? Configure::read('BcBlog.eye_catch_size_mobile_thumb_width'),
                'eye_catch_size_mobile_thumb_height' => $eyeCatchSizeMobileThumbHeight ?? Configure::read('BcBlog.eye_catch_size_mobile_thumb_height'),
                'use_content' => $useContent,
                'widget_area' => $widgetArea
            ];

            // Contentデータを含めた統合データ構造
            $data = array_merge($blogContentData, [
                'content' => $contentData
            ]);

            $result = $blogContentsService->create($data);

            if ($result) {
                return $this->createSuccessResponse(
                    $result->toArray(),
                    [],
                    sprintf('ブログコンテンツ「%s」を追加しました。', $result->content->title),
                    $loginUserId
                );
            } else {
                return $this->createErrorResponse('ブログコンテンツの保存に失敗しました');
            }
        });
    }

    /**
     * ブログコンテンツを編集
     */
    public function editBlogContent(
        int $id,
        ?string $name = null,
        ?string $title = null,
        ?int $siteId = null,
        ?int $parentId = null,
        ?string $description = null,
        ?int $authorId = null,
        ?string $layoutTemplate = null,
        ?bool $status = null,
        ?string $publishBegin = null,
        ?string $publishEnd = null,
        ?bool $excludeSearch = null,
        ?bool $excludeMenu = null,
        ?bool $blankLink = null,
        ?string $template = null,
        ?int $listCount = null,
        ?string $listDirection = null,
        ?int $feedCount = null,
        ?bool $commentUse = null,
        ?bool $commentApprove = null,
        ?bool $tagUse = null,
        ?int $eyeCatchSizeThumbWidth = null,
        ?int $eyeCatchSizeThumbHeight = null,
        ?int $eyeCatchSizeMobileThumbWidth = null,
        ?int $eyeCatchSizeMobileThumbHeight = null,
        ?bool $useContent = null,
        ?int $widgetArea = null,
        ?int $loginUserId = null
    ): array
    {
        return $this->executeWithErrorHandling(function() use (
            $id, $name, $title, $siteId, $parentId, $description, $authorId, $layoutTemplate, $status,
            $publishBegin, $publishEnd, $excludeSearch, $excludeMenu, $blankLink, $template, $listCount,
            $listDirection, $feedCount, $commentUse, $commentApprove, $tagUse, $eyeCatchSizeThumbWidth,
            $eyeCatchSizeThumbHeight, $eyeCatchSizeMobileThumbWidth, $eyeCatchSizeMobileThumbHeight,
            $useContent, $widgetArea, $loginUserId
        ) {
            if (empty($id)) return $this->createErrorResponse('IDは必須です');

            /** @var BlogContentsService $blogContentsService */
            $blogContentsService = $this->getService(BlogContentsServiceInterface::class);
            $entity = $blogContentsService->get($id);

            if (!$entity) return $this->createErrorResponse('指定されたIDのブログコンテンツが見つかりません');

            // 更新データを構築（null以外の値のみ）
            $data = [];
            if ($description !== null) $data['description'] = $description;
            if ($template !== null) $data['template'] = $template;
            if ($listCount !== null) $data['list_count'] = $listCount;
            if ($listDirection !== null) $data['list_direction'] = $listDirection;
            if ($feedCount !== null) $data['feed_count'] = $feedCount;
            if ($commentUse !== null) $data['comment_use'] = $commentUse;
            if ($commentApprove !== null) $data['comment_approve'] = $commentApprove;
            if ($tagUse !== null) $data['tag_use'] = $tagUse;
            if ($eyeCatchSizeThumbWidth !== null) $data['eye_catch_size_thumb_width'] = $eyeCatchSizeThumbWidth;
            if ($eyeCatchSizeThumbHeight !== null) $data['eye_catch_size_thumb_height'] = $eyeCatchSizeThumbHeight;
            if ($eyeCatchSizeMobileThumbWidth !== null) $data['eye_catch_size_mobile_thumb_width'] = $eyeCatchSizeMobileThumbWidth;
            if ($eyeCatchSizeMobileThumbHeight !== null) $data['eye_catch_size_mobile_thumb_height'] = $eyeCatchSizeMobileThumbHeight;
            if ($useContent !== null) $data['use_content'] = $useContent;
            if ($widgetArea !== null) $data['widget_area'] = $widgetArea;

            // Contentエンティティの更新データも含める（もし関連するContentフィールドが変更される場合）
            $contentData = [];
            if ($name !== null) $contentData['name'] = $name;
            if ($title !== null) $contentData['title'] = $title;
            if ($siteId !== null) $contentData['site_id'] = $siteId;
            if ($parentId !== null) $contentData['parent_id'] = $parentId;
            if ($description !== null) $contentData['description'] = $description;
            if ($authorId !== null) $contentData['author_id'] = $authorId;
            if ($layoutTemplate !== null) $contentData['layout_template'] = $layoutTemplate;
            if ($status !== null) $contentData['self_status'] = (bool)$status;
            if ($publishBegin !== null) $contentData['publish_begin'] = $publishBegin;
            if ($publishEnd !== null) $contentData['publish_end'] = $publishEnd;
            if ($excludeSearch !== null) $contentData['exclude_search'] = (bool)$excludeSearch;
            if ($excludeMenu !== null) $contentData['exclude_menu'] = (bool)$excludeMenu;
            if ($blankLink !== null) $contentData['blank_link'] = (bool)$blankLink;

            if (!empty($contentData)) $data['content'] = $contentData;
            $result = $blogContentsService->update($entity, $data);

            if ($result) {
                return $this->createSuccessResponse(
                    $result->toArray(),
                    [],
                    sprintf('ブログコンテンツ「%s」を編集しました。', $result->content->title),
                    $loginUserId
                );
            } else {
                return $this->createErrorResponse('ブログコンテンツの更新に失敗しました');
            }
        });
    }

    /**
     * ブログコンテンツ一覧を取得
     */
    public function getBlogContents(
        ?string $title = null,
        ?int $status = null,
        ?int $limit = null,
        ?int $page = null
    ): array
    {
        return $this->executeWithErrorHandling(function() use ($title, $status, $limit, $page) {
            /** @var BlogContentsService $blogContentsService */
            $blogContentsService = $this->getService(BlogContentsServiceInterface::class);

            $conditions = [];
            if (!empty($title)) $conditions['title'] = $title;
            if (!empty($status)) $conditions['status'] = $status;
            if (!empty($limit)) $conditions['limit'] = $limit;
            if (!empty($page)) $conditions['page'] = $page;

            $results = $blogContentsService->getIndex($conditions)->toArray();

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
     * ブログコンテンツを取得
     */
    public function getBlogContent(int $id): array
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            if (empty($id)) return $this->createErrorResponse('IDは必須です');
            /** @var BlogContentsService $blogContentsService */
            $blogContentsService = $this->getService(BlogContentsServiceInterface::class);

            $result = $blogContentsService->get($id);

            if ($result) {
                return $this->createSuccessResponse($result->toArray());
            } else {
                return $this->createErrorResponse('指定されたIDのブログコンテンツが見つかりません');
            }
        });
    }

    /**
     * ブログコンテンツを削除
     */
    public function deleteBlogContent(int $id, ?int $loginUserId = null): array
    {
        return $this->executeWithErrorHandling(function() use ($id, $loginUserId) {
            if (empty($id)) return $this->createErrorResponse('IDは必須です');
            /** @var BlogContentsService $blogContentsService */
            $blogContentsService = $this->getService(BlogContentsServiceInterface::class);

            // 削除前にタイトルを取得
            $entity = $blogContentsService->get($id);
            if (!$entity) {
                return $this->createErrorResponse('指定されたIDのブログコンテンツが見つかりません');
            }

            $title = $entity->content->title;
            $result = $blogContentsService->delete($id);

            if ($result) {
                return $this->createSuccessResponse(
                    ['message' => 'ブログコンテンツを削除しました'],
                    [],
                    sprintf('ブログコンテンツ「%s」を削除しました。', $title),
                    $loginUserId
                );
            } else {
                return $this->createErrorResponse('ブログコンテンツの削除に失敗しました');
            }
        });
    }
}
