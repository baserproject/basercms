<?php
declare(strict_types=1);

namespace BcMcp\Mcp\BcBlog;

use BcBlog\Service\BlogCategoriesService;
use BcMcp\Mcp\BaseMcpTool;
use BcBlog\Service\BlogCategoriesServiceInterface;
use PhpMcp\Server\ServerBuilder;

/**
 * ブログカテゴリツールクラス
 *
 * ブログカテゴリのCRUD操作を提供
 */
class BlogCategoriesTool extends BaseMcpTool
{

    /**
     * ブログカテゴリ関連のツールを ServerBuilder に追加
     */
    public function addToolsToBuilder(ServerBuilder $builder): ServerBuilder
    {
        return $builder
            ->withTool(
                handler: [self::class, 'addBlogCategory'],
                name: 'addBlogCategory',
                description: 'ブログカテゴリを追加します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'title' => ['type' => 'string', 'description' => 'カテゴリタイトル（必須）'],
                        'name' => ['type' => 'string', 'description' => 'カテゴリ名（省略時はタイトルから自動生成）'],
                        'blogContentId' => ['type' => 'number', 'description' => 'ブログコンテンツID（省略時はデフォルト）'],
                        'parentId' => ['type' => 'number', 'description' => '親カテゴリID（省略時はルートカテゴリ）'],
                        'status' => ['type' => 'number', 'default' => 1, 'description' => '公開ステータス（0: 非公開, 1: 公開）']
                    ],
                    'required' => ['title']
                ]
            )
            ->withTool(
                handler: [self::class, 'getBlogCategories'],
                name: 'getBlogCategories',
                description: 'ブログカテゴリの一覧を取得します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'blogContentId' => ['type' => 'number', 'description' => 'ブログコンテンツID（省略時はデフォルト）'],
                        'title' => ['type' => 'string', 'description' => 'タイトル（部分一致）'],
                        'status' => ['type' => 'number', 'description' => '公開ステータス（null: 全て, publish: 公開）'],
                        'limit' => ['type' => 'number', 'description' => '取得件数（省略時は制限なし）'],
                        'page' => ['type' => 'number', 'description' => 'ページ番号（省略時は1ページ目）'],
                    ]
                ]
            )
            ->withTool(
                handler: [self::class, 'getBlogCategory'],
                name: 'getBlogCategory',
                description: '指定されたIDのブログカテゴリを取得します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => 'カテゴリID（必須）']
                    ],
                    'required' => ['id']
                ]
            )
            ->withTool(
                handler: [self::class, 'editBlogCategory'],
                name: 'editBlogCategory',
                description: '指定されたIDのブログカテゴリを編集します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => 'カテゴリID（必須）'],
                        'title' => ['type' => 'string', 'description' => 'カテゴリタイトル'],
                        'name' => ['type' => 'string', 'description' => 'カテゴリ名'],
                        'blogContentId' => ['type' => 'number', 'description' => 'ブログコンテンツID（省略時はデフォルト）'],
                        'parentId' => ['type' => 'number', 'description' => '親カテゴリID（省略時はルートカテゴリ）'],
                        'status' => ['type' => 'number', 'description' => '公開ステータス（0: 非公開, 1: 公開）']
                    ],
                    'required' => ['id']
                ]
            )
            ->withTool(
                handler: [self::class, 'deleteBlogCategory'],
                name: 'deleteBlogCategory',
                description: '指定されたIDのブログカテゴリを削除します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => 'カテゴリID（必須）']
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
            case 'addBlogCategory':
                if(empty($args['blogContentId'])) return false;
                return ['POST' => "/bc-blog/blog_categories/add/{$args['blogContentId']}.json"];
            case 'editBlogCategory':
                if(empty($args['id'])) return false;
                return ['POST' => "/bc-blog/blog_categories/edit/{$args['id']}.json"];
            case 'getBlogCategories':
                $blogContentId = $args['blogContentId'] ?? 1;
                return ['GET' => "/bc-blog/blog_categories/index/{$blogContentId}.json"];
            case 'getBlogCategory':
                if(empty($args['id'])) return false;
                return ['GET' => "/bc-blog/blog_categories/view/{$args['id']}.json"];
            case 'deleteBlogCategory':
                if(empty($args['id'])) return false;
                return ['POST' => "/bc-blog/blog_categories/delete/{$args['id']}.json"];
            default:
                return false;
        }
    }

    /**
     * ブログカテゴリを追加
     * @param string $title
     * @param string|null $name
     * @param int|null $blogContentId
     * @param int|null $parentId
     * @param int|null $status
     * @param int|null $loginUserId
     * @return array
     */
    public function addBlogCategory(
        string $title,
        ?string $name = null,
        ?int $blogContentId = 1,
        ?int $parentId = null,
        ?int $status = 1,
        ?int $loginUserId = null
    ): array
    {
        return $this->executeWithErrorHandling(function() use ($title, $name, $blogContentId, $parentId, $status, $loginUserId) {
            // 必須パラメータのチェック
            if (empty($title)) return $this->createErrorResponse('titleは必須です');

            $blogCategoriesService = $this->getService(BlogCategoriesServiceInterface::class);

            $result = $blogCategoriesService->create($blogContentId, [
                'title' => $title,
                'name' => $name ?? 'category_' . uniqid(),
                'parent_id' => $parentId,
                'status' => $status
            ]);

            if ($result) {
                return $this->createSuccessResponse(
                    $result->toArray(),
                    [],
                    sprintf('ブログカテゴリ「%s」を追加しました。', $result->title),
                    $loginUserId
                );
            } else {
                return $this->createErrorResponse('ブログカテゴリの保存に失敗しました');
            }
        });
    }

    /**
     * ブログカテゴリの一覧を取得
     * @param int|null $blogContentId
     * @param int|null $limit
     * @param int|null $page
     * @param string|null $title
     * @param string|null $status
     * @return array
     */
    public function getBlogCategories(
        ?int $blogContentId = 1,
        ?string $title = null,
        ?string $status = null,
        ?int $limit = null,
        ?int $page = null
    ): array
    {
        return $this->executeWithErrorHandling(function() use (
            $blogContentId,
            $title,
            $status,
            $limit,
            $page
        ) {
            /** @var BlogCategoriesService $blogCategoriesService */
            $blogCategoriesService = $this->getService(BlogCategoriesServiceInterface::class);

            $conditions = [];
            if (!empty($title)) $conditions['title'] = $title;
            if (!empty($status)) $conditions['status'] = $status;
            if (!empty($limit)) $conditions['limit'] = $limit;
            if (!empty($page)) $conditions['page'] = $page;

            //
            $query = $blogCategoriesService->getIndex($blogContentId ?? 1, $conditions);

            // 総件数を取得（ページネーション前）
            $totalCount = $blogCategoriesService->getIndex($blogContentId ?? 1, array_diff_key($conditions, array_flip(['limit', 'page'])))->count();

            $results = $query->toArray();

            return $this->createSuccessResponse($results, [
                'pagination' => [
                    'page' => $page ?? 1,
                    'limit' => $limit ?? null,
                    'count' => count($results),
                    'total' => $totalCount
                ]
            ]);
        });
    }

    /**
     * 指定されたIDのブログカテゴリを取得
     * @param int $id
     * @param int|null $blogContentId
     * @return array
     */
    public function getBlogCategory(int $id): array
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            // 必須パラメータのチェック
            if (empty($id)) return $this->createErrorResponse('idは必須です');

            $blogCategoriesService = $this->getService(BlogCategoriesServiceInterface::class);
            $result = $blogCategoriesService->get($id);

            if ($result) {
                return $this->createSuccessResponse($result->toArray());
            } else {
                return $this->createErrorResponse('指定されたIDのブログカテゴリが見つかりません');
            }
        });
    }

    /**
     * ブログカテゴリを編集
     * @param int $id
     * @param string|null $title
     * @param string|null $name
     * @param int|null $blogContentId
     * @param int|null $parentId
     * @param int|null $status
     * @param int|null $loginUserId
     * @return array
     */
    public function editBlogCategory(
        int $id,
        ?string $title = null,
        ?string $name = null,
        ?int $blogContentId = null,
        ?int $parentId = null,
        ?int $status = null,
        ?int $loginUserId = null
    ): array
    {
        return $this->executeWithErrorHandling(function() use (
            $id,
            $title,
            $name,
            $blogContentId,
            $parentId,
            $status,
            $loginUserId
        ) {
            // 必須パラメータのチェック
            if (empty($id)) return $this->createErrorResponse('idは必須です');

            $blogCategoriesService = $this->getService(BlogCategoriesServiceInterface::class);
            $entity = $blogCategoriesService->get($id);

            if (!$entity) return $this->createErrorResponse('指定されたIDのブログカテゴリが見つかりません');

            // 更新データを構築（null以外の値のみ）
            $data = [];
            if ($title !== null) $data['title'] = $title;
            if ($name !== null) $data['name'] = $name;
            if ($blogContentId !== null) $data['blog_content_id'] = $blogContentId;
            if ($parentId !== null) $data['parent_id'] = $parentId;
            if ($status !== null) $data['status'] = $status;

            // nameを更新する場合、バリデーションエラーを避けるために
            // 現在のblog_content_idを明示的に含める
            if (isset($data['name']) && !isset($data['blog_content_id'])) {
                $data['blog_content_id'] = $entity->blog_content_id;
            }

            // バリデーションコンテキストを設定
            $options = [];
            if (isset($data['name'])) $options['validate'] = false; // 重複チェックのバリデーションを一時的に無効化

            // バリデーションを無効化した場合は手動で重複チェックを実行
            if (isset($data['name']) && isset($options['validate']) && $options['validate'] === false) {
                // 同じblog_content_id内での重複をチェック
                $existingCategory = $blogCategoriesService->getIndex($entity->blog_content_id, [
                    'name' => $data['name']
                ])->first();

                if ($existingCategory && $existingCategory->id !== $id) {
                    return $this->createErrorResponse('指定されたカテゴリ名は既に使用されています');
                }
            }

            $result = $blogCategoriesService->update($entity, $data, $options);

            if ($result) {
                return $this->createSuccessResponse(
                    $result->toArray(),
                    [],
                    sprintf('ブログカテゴリ「%s」を編集しました。', $result->title),
                    $loginUserId
                );
            } else {
                return $this->createErrorResponse('ブログカテゴリの更新に失敗しました');
            }
        });
    }

    /**
     * ブログカテゴリを削除
     * @param int $id
     * @param int|null $loginUserId
     * @return array
     */
    public function deleteBlogCategory(int $id, ?int $loginUserId = null): array
    {
        return $this->executeWithErrorHandling(function() use ($id, $loginUserId) {
            // 必須パラメータのチェック
            if (empty($id)) return $this->createErrorResponse('idは必須です');

            $blogCategoriesService = $this->getService(BlogCategoriesServiceInterface::class);
            $entity = $blogCategoriesService->get($id);

            if (!$entity) return $this->createErrorResponse('指定されたIDのブログカテゴリが見つかりません');

            $title = $entity->title;
            $result = $blogCategoriesService->delete($id);

            if ($result) {
                return $this->createSuccessResponse(
                    ['message' => 'ブログカテゴリを削除しました'],
                    [],
                    sprintf('ブログカテゴリ「%s」を削除しました。', $title),
                    $loginUserId
                );
            } else {
                return $this->createErrorResponse('ブログカテゴリの削除に失敗しました');
            }
        });
    }
}
