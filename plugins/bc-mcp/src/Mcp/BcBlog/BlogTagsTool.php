<?php
declare(strict_types=1);

namespace BcMcp\Mcp\BcBlog;

use BcBlog\Service\BlogTagsService;
use BcBlog\Service\BlogTagsServiceInterface;
use PhpMcp\Server\ServerBuilder;
use BcMcp\Mcp\BaseMcpTool;

/**
 * ブログタグツールクラス
 *
 * ブログタグのCRUD操作を提供
 */
class BlogTagsTool extends BaseMcpTool
{

    /**
     * ブログタグ関連のツールを ServerBuilder に追加
     */
    public function addToolsToBuilder(ServerBuilder $builder): ServerBuilder
    {
        return $builder
            ->withTool(
                handler: [self::class, 'addBlogTag'],
                name: 'addBlogTag',
                description: 'ブログタグを追加します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'name' => ['type' => 'string', 'description' => 'タグ名（必須）']
                    ],
                    'required' => ['name']
                ]
            )
            ->withTool(
                handler: [self::class, 'getBlogTags'],
                name: 'getBlogTags',
                description: 'ブログタグの一覧を取得します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'name' => ['type' => 'string', 'description' => 'タグ名での検索'],
                        'limit' => ['type' => 'number', 'description' => '取得件数（省略時は10件）'],
                        'page' => ['type' => 'number', 'description' => 'ページ番号（省略時は1ページ目）'],
                    ]
                ]
            )
            ->withTool(
                handler: [self::class, 'getBlogTag'],
                name: 'getBlogTag',
                description: '指定されたIDのブログタグを取得します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => 'ブログタグID（必須）']
                    ],
                    'required' => ['id']
                ]
            )
            ->withTool(
                handler: [self::class, 'editBlogTag'],
                name: 'editBlogTag',
                description: '指定されたIDのブログタグを編集します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => 'ブログタグID（必須）'],
                        'name' => ['type' => 'string', 'description' => 'タグ名（必須）']
                    ],
                    'required' => ['id', 'name']
                ]
            )
            ->withTool(
                handler: [self::class, 'deleteBlogTag'],
                name: 'deleteBlogTag',
                description: '指定されたIDのブログタグを削除します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => 'ブログタグID（必須）']
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
            case 'addBlogTag':
                return ['POST' => "/bc-blog/blog_tags/add.json"];
            case 'editBlogTag':
                if(empty($args['id'])) return false;
                return ['POST' => "/bc-blog/blog_tags/edit/{$args['id']}.json"];
            case 'getBlogTags':
                return ['GET' => "/bc-blog/blog_tags/index.json"];
            case 'getBlogTag':
                if(empty($args['id'])) return false;
                return ['GET' => "/bc-blog/blog_tags/view/{$args['id']}.json"];
            case 'deleteBlogTag':
                if(empty($args['id'])) return false;
                return ['POST' => "/bc-blog/blog_tags/delete/{$args['id']}.json"];
            default:
                return false;
        }
    }

    /**
     * ブログタグを追加
     */
    public function addBlogTag(string $name, ?int $loginUserId = null): array
    {
        return $this->executeWithErrorHandling(function() use ($name, $loginUserId) {
            /** @var BlogTagsService $blogTagsService */
            $blogTagsService = $this->getService(BlogTagsServiceInterface::class);
            $result = $blogTagsService->create([
                'name' => $name
            ]);
            if ($result) {
                return $this->createSuccessResponse(
                    $result->toArray(),
                    [],
                    sprintf('ブログタグ「%s」を追加しました。', $result->name),
                    $loginUserId
                );
            } else {
                return $this->createErrorResponse('ブログタグの保存に失敗しました');
            }
        });
    }

    /**
     * ブログタグ一覧を取得
     */
    public function getBlogTags(
        ?string $name = null,
        ?int $limit = 10,
        ?int $page = 1
    ): array
    {
        return $this->executeWithErrorHandling(function() use ($name, $limit, $page) {

            /** @var BlogTagsService $blogTagsService */
            $blogTagsService = $this->getService(BlogTagsServiceInterface::class);

            $conditions = [];
            if (!empty($name)) $conditions['name'] = $name;
            if (!empty($limit)) $conditions['limit'] = $limit;
            if (!empty($page)) $conditions['page'] = $page;
            $results = $blogTagsService->getIndex($conditions)->toArray();

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
     * ブログタグを取得
     */
    public function getBlogTag(int $id): array
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            /** @var BlogTagsService $blogTagsService */
            $blogTagsService = $this->getService(BlogTagsServiceInterface::class);
            $result = $blogTagsService->get($id);

            if ($result) {
                return $this->createSuccessResponse($result->toArray());
            } else {
                return $this->createErrorResponse('指定されたIDのブログタグが見つかりません');
            }
        });
    }

    /**
     * ブログタグを編集
     */
    public function editBlogTag(int $id, string $name, ?int $loginUserId = null): array
    {
        return $this->executeWithErrorHandling(function() use ($id, $name, $loginUserId) {
            /** @var BlogTagsService $blogTagsService */
            $blogTagsService = $this->getService(BlogTagsServiceInterface::class);
            $entity = $blogTagsService->get($id);

            if (!$entity) return $this->createErrorResponse('指定されたIDのブログタグが見つかりません');

            $result = $blogTagsService->update($entity, [
                'name' => $name
            ]);

            if ($result) {
                return $this->createSuccessResponse(
                    $result->toArray(),
                    [],
                    sprintf('ブログタグ「%s」を編集しました。', $result->name),
                    $loginUserId
                );
            } else {
                return $this->createErrorResponse('ブログタグの更新に失敗しました');
            }
        });
    }

    /**
     * ブログタグを削除
     */
    public function deleteBlogTag(int $id, ?int $loginUserId = null): array
    {
        return $this->executeWithErrorHandling(function() use ($id, $loginUserId) {
            /** @var BlogTagsService $blogTagsService */
            $blogTagsService = $this->getService(BlogTagsServiceInterface::class);

            // 削除前にタグ名を取得
            $entity = $blogTagsService->get($id);
            if (!$entity) {
                return $this->createErrorResponse('指定されたIDのブログタグが見つかりません');
            }

            $name = $entity->name;
            $result = $blogTagsService->delete($id);

            if ($result) {
                return $this->createSuccessResponse(
                    ['message' => 'ブログタグを削除しました'],
                    [],
                    sprintf('ブログタグ「%s」を削除しました。', $name),
                    $loginUserId
                );
            } else {
                return $this->createErrorResponse('ブログタグの削除に失敗しました');
            }
        });
    }
}
