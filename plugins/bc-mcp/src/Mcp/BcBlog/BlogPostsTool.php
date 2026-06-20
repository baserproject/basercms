<?php
declare(strict_types=1);

namespace BcMcp\Mcp\BcBlog;

use BcBlog\Service\BlogPostsServiceInterface;
use BcBlog\Service\BlogContentsServiceInterface;
use BcBlog\Service\BlogCategoriesServiceInterface;
use BaserCore\Service\UsersServiceInterface;
use PhpMcp\Server\ServerBuilder;
use BcMcp\Mcp\BaseMcpTool;

/**
 * ブログ記事ツールクラス
 *
 * ブログ記事のCRUD操作を提供
 */
class BlogPostsTool extends BaseMcpTool
{

    /**
     * ブログ記事関連のツールを ServerBuilder に追加
     */
    public function addToolsToBuilder(ServerBuilder $builder): ServerBuilder

    {
        return $builder
            ->withTool(
                handler: [self::class, 'getBlogPosts'],
                name: 'getBlogPosts',
                description: 'ブログ記事の一覧を取得します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'blogContentId' => ['type' => 'number', 'description' => 'ブログコンテンツID（省略時はデフォルト）'],
                        'keyword' => ['type' => 'string', 'description' => '検索キーワード'],
                        'status' => ['type' => 'number', 'description' => '公開ステータス（null: 全て, publish: 公開）（省略時は全て）'],
                        'limit' => ['type' => 'number', 'description' => '取得件数（省略時は10件）'],
                        'page' => ['type' => 'number', 'description' => 'ページ番号（省略時は1ページ目）'],
                    ]
                ]
            )
            ->withTool(
                handler: [self::class, 'getBlogPost'],
                name: 'getBlogPost',
                description: '指定されたIDのブログ記事を取得します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => '記事ID（必須）']
                    ],
                    'required' => ['id']
                ]
            )
            ->withTool(
                handler: [self::class, 'addBlogPost'],
                name: 'addBlogPost',
                description: 'ブログ記事を追加します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'title' => ['type' => 'string', 'description' => '記事タイトル（必須）'],
                        'detail' => ['type' => 'string', 'description' => '記事詳細（必須）、マークダウン不可、HTML推奨'],
                        'blogContent' => ['type' => 'string', 'description' => 'ブログコンテンツ名（省略時はデフォルト）'],
                        'name' => ['type' => 'string', 'description' => '記事のスラッグ。URLにおける記事を特定する識別子（省略時はなし）'],
                        'content' => ['type' => 'string', 'description' => '記事概要（省略時はなし）、マークダウン不可、HTML推奨'],
                        'category' => ['type' => 'string', 'description' => 'カテゴリ名（省略時はカテゴリなし）'],
                        'email' => ['type' => 'string', 'format' => 'email', 'description' => 'ユーザーのメールアドレス（省略時はログインユーザー）'],
                        'status' => ['type' => 'number', 'description' => '公開ステータス（0: 非公開, 1: 公開）、（省略時は0）'],
                        'posted' => ['type' => 'string', 'format' => 'date-time', 'description' => '投稿日（省略時は現在日時）'],
                        'publishBegin' => ['type' => 'string', 'format' => 'date-time', 'description' => '公開開始日時（省略時はなし）'],
                        'publishEnd' => ['type' => 'string', 'format' => 'date-time', 'description' => '公開終了日時（省略時はなし）'],
                        'eyeCatch' => ['type' => 'string', 'description' => 'アイキャッチ画像。外部画像URLを直接指定'],
                    ],
                    'required' => ['title', 'detail']
                ]
            )
            ->withTool(
                handler: [self::class, 'editBlogPost'],
                name: 'editBlogPost',
                description: 'ブログ記事を編集します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => '記事ID（必須）'],
                        'title' => ['type' => 'string', 'description' => '記事タイトル'],
                        'detail' => ['type' => 'string', 'description' => '記事詳細、マークダウン不可、HTML推奨'],
                        'blogContent' => ['type' => 'string', 'description' => 'ブログコンテンツ名'],
                        'name' => ['type' => 'string', 'description' => '記事のスラッグ。URLにおける記事を特定する識別子'],
                        'content' => ['type' => 'string', 'description' => '記事概要、マークダウン不可、HTML推奨'],
                        'category' => ['type' => 'string', 'description' => 'カテゴリ名'],
                        'email' => ['type' => 'string', 'format' => 'email', 'description' => 'ユーザーのメールアドレス'],
                        'status' => ['type' => 'number', 'description' => '公開ステータス（0: 非公開, 1: 公開）'],
                        'posted' => ['type' => 'string', 'format' => 'date-time', 'description' => '投稿日'],
                        'publishBegin' => ['type' => 'string', 'format' => 'date-time', 'description' => '公開開始日時'],
                        'publishEnd' => ['type' => 'string', 'format' => 'date-time', 'description' => '公開終了日時'],
                        'eyeCatch' => ['type' => 'string', 'description' => 'アイキャッチ画像。外部画像URLを直接指定'],
                    ],
                    'required' => ['id']
                ]
            )
            ->withTool(
                handler: [self::class, 'deleteBlogPost'],
                name: 'deleteBlogPost',
                description: '指定されたIDのブログ記事を削除します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => '記事ID（必須）']
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
            case 'addBlogPost':
                return ['POST' => "/bc-blog/blog_posts/add.json"];
            case 'editBlogPost':
                if(empty($args['id'])) return false;
                return ['POST' => "/bc-blog/blog_posts/edit/{$args['id']}.json"];
            case 'getBlogPosts':
                return ['GET' => '/bc-blog/blog_posts/index.json'];
            case 'getBlogPost':
                if(empty($args['id'])) return false;
                return ['GET' => "/bc-blog/blog_posts/view/{$args['id']}.json"];
            case 'deleteBlogPost':
                if(empty($args['id'])) return false;
                return ['POST' => "/bc-blog/blog_posts/delete/{$args['id']}.json"];
            default:
                return false;
        }
    }

    /**
     * ブログ記事を追加
     */
    public function addBlogPost(
        string $title,
        string $detail,
        ?string $blogContent = null,
        ?string $name = null,
        ?string $content = null,
        ?string $category = null,
        ?string $email = null,
        ?int $status = 0,
        ?string $posted = null,
        ?string $publishBegin = null,
        ?string $publishEnd = null,
        ?string $eyeCatch = null,
        ?int $loginUserId = null
    ): array
    {
        return $this->executeWithErrorHandling(function() use (
            $title,
            $detail,
            $blogContent,
            $name,
            $content,
            $category,
            $email,
            $status,
            $posted,
            $publishBegin,
            $publishEnd,
            $eyeCatch,
            $loginUserId
        ) {
            // 必須パラメータのチェック
            if (empty($title)) {
                return $this->createErrorResponse('タイトルは必須です');
            }
            if (empty($detail)) {
                return $this->createErrorResponse('詳細は必須です');
            }

            $blogContentId = $this->getBlogContentId($blogContent);
            $blogCategoryId = $this->getBlogCategoryId($category, $blogContentId);

            $data = [
                'title' => $title,
                'detail' => $detail,
                'blog_content_id' => $blogContentId,
                'name' => $name,
                'content' => $content,
                'blog_category_id' => $blogCategoryId,
                'user_id' => $this->getAuthorId($email, $loginUserId),
                'status' => $status,
                'posted' => $posted ?? date('Y-m-d H:i:s'),
                'publish_begin' => $publishBegin,
                'publish_end' => $publishEnd,
            ];

            // アイキャッチ画像の処理
            if (!empty($eyeCatch) && $this->isFileUploadable($eyeCatch)) {
                if (!is_array($eyeCatch)) {
                    $eyeCatchData = $this->processFileUpload($eyeCatch, 'eye_catch');
                }
                if ($eyeCatchData !== false && is_array($eyeCatchData)) {
                    // 配列データをCakePHPのUploadedFileオブジェクトに変換
                    $data['eye_catch'] = $this->createUploadedFileFromArray($eyeCatchData);
                }
            } elseif (!empty($eyeCatch)) {
                // その他の形式の場合はエラーとして扱う
                return $this->createErrorResponse('アイキャッチ画像の形式が不正です');
            }

            $blogPostsService = $this->getService(BlogPostsServiceInterface::class);

            // ファイルアップロードの設定を実施
            $blogPostsService->setupUpload($blogContentId);

            $result = $blogPostsService->create($data);
            if ($result) {
                return $this->createSuccessResponse(
                    $result->toArray(),
                    [],
                    sprintf('ブログ記事「%s」を追加しました。', $result->title),
                    $loginUserId
                );
            } else {
                return $this->createErrorResponse('ブログ記事の保存に失敗しました');
            }
        });
    }


    /**
     * ブログ記事を編集
     */
    public function editBlogPost(
        int $id,
        ?string $title = null,
        ?string $detail = null,
        ?string $blogContent = null,
        ?string $name = null,
        ?string $content = null,
        ?string $category = null,
        ?string $email = null,
        ?int $status = null,
        ?string $posted = null,
        ?string $publishBegin = null,
        ?string $publishEnd = null,
        ?string $eyeCatch = null,
        ?int $loginUserId = null
    ): array
    {
        return $this->executeWithErrorHandling(function() use (
            $id,
            $title,
            $detail,
            $blogContent,
            $name,
            $content,
            $category,
            $email,
            $status,
            $posted,
            $publishBegin,
            $publishEnd,
            $eyeCatch,
            $loginUserId
        ) {
            // 必須パラメータのチェック
            if (empty($id)) {
                return $this->createErrorResponse('IDは必須です');
            }

            $blogPostsService = $this->getService(BlogPostsServiceInterface::class);
            $entity = $blogPostsService->get($id);
            if (!$entity) {
                return $this->createErrorResponse('指定されたIDのブログ記事が見つかりません');
            }

            // ファイルアップロードの設定を実施
            $blogPostsService->setupUpload($entity->blog_content_id);

            // 更新データを構築（null以外の値のみ）
            $data = [];
            if ($title !== null) $data['title'] = $title;
            if ($detail !== null) $data['detail'] = $detail;
            if ($blogContent !== null) $data['blog_content_id'] = $this->getBlogContentId($blogContent);
            if ($name !== null) $data['name'] = $name;
            if ($content !== null) $data['content'] = $content;
            if ($category !== null) $data['blog_category_id'] = $this->getBlogCategoryId($category, $data['blog_content_id'] ?? $entity->blog_content_id);
            if ($email !== null) $data['user_id'] = $this->getAuthorId($email);
            if ($status !== null) $data['status'] = $status;
            if ($posted !== null) $data['posted'] = $posted;
            if ($publishBegin !== null) $data['publish_begin'] = $publishBegin;
            if ($publishEnd !== null) $data['publish_end'] = $publishEnd;

            // アイキャッチ画像の処理
            if ($eyeCatch !== null) {
                if (!empty($eyeCatch) && $this->isFileUploadable($eyeCatch)) {
                    if (!is_array($eyeCatch)) {
                        $eyeCatchData = $this->processFileUpload($eyeCatch, 'eye_catch');
                    }
                    if ($eyeCatchData !== false && is_array($eyeCatchData)) {
                        // 配列データをCakePHPのUploadedFileオブジェクトに変換
                        $data['eye_catch'] = $this->createUploadedFileFromArray($eyeCatchData);
                    }
                } else {
                    // 空文字列の場合は削除
                    $data['eye_catch'] = null;
                }
            }

            $result = $blogPostsService->update($entity, $data);

            if ($result) {
                return $this->createSuccessResponse(
                    $result->toArray(),
                    [],
                    sprintf('ブログ記事「%s」を編集しました。', $result->title),
                    $loginUserId
                );
            } else {
                return $this->createErrorResponse('ブログ記事の更新に失敗しました');
            }
        });
    }

    /**
     * 投稿者のユーザーIDを取得
     * @param string|null $email
     * @param int $loginUserId
     * @return mixed
     * @throws \Exception
     */
    public function getAuthorId(?string $email, ?int $loginUserId = null)
    {
        $usersService = $this->getService(UsersServiceInterface::class);
        if (!empty($email)) {
            $conditions = ['email' => $email];
            $user = $usersService->getIndex($conditions)->first();
        } elseif ($loginUserId) {
            $user = $usersService->get($loginUserId);
        }
        if (empty($user)) {
            throw new \Exception('投稿者を指定できませんでした。');
        }
        return $user->id;
    }

    /**
     * ブログ記事一覧を取得
     */
    public function getBlogPosts(
        ?int $blogContentId = null,
        ?string $keyword = null,
        ?string $status = null,
        ?int $limit = 10,
        ?int $page = 1
    ): array
    {
        return $this->executeWithErrorHandling(function() use (
            $blogContentId,
            $keyword,
            $status,
            $limit,
            $page
        ) {
            /** @var \BcBlog\Service\BlogPostsService $blogPostsService */
            $blogPostsService = $this->getService(BlogPostsServiceInterface::class);

            $conditions = [];
            if (!empty($blogContentId)) $conditions['blog_content_id'] = $blogContentId;
            if (!empty($keyword)) $conditions['keyword'] = $keyword;
            if ($status) $conditions['status'] = $status;
            $conditions['limit'] = $limit ?? 10;
            $conditions['page'] = $page ?? 1;

            $results = $blogPostsService->getIndex($conditions)->toArray();

            return $this->createSuccessResponse([
                'data' => $results,
                'pagination' => [
                    'page' => $page ?? 1,
                    'limit' => $limit ?? 10,
                    'count' => count($results)
                ]
            ]);
        });
    }

    /**
     * ブログ記事を取得
     */
    public function getBlogPost(int $id): array
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            // 必須パラメータのチェック
            if (empty($id)) return $this->createErrorResponse('IDは必須です');

            /** @var \BcBlog\Service\BlogPostsService $blogPostsService */
            $blogPostsService = $this->getService(BlogPostsServiceInterface::class);
            $result = $blogPostsService->get($id);

            if ($result) {
                return $this->createSuccessResponse($result->toArray());
            } else {
                return $this->createErrorResponse('指定されたIDのブログ記事が見つかりません');
            }
        });
    }

    /**
     * ブログ記事を削除
     */
    public function deleteBlogPost(int $id, ?int $loginUserId = null): array
    {
        return $this->executeWithErrorHandling(function() use ($id, $loginUserId) {
            // 必須パラメータのチェック
            if (empty($id)) return $this->createErrorResponse('IDは必須です');

            /** @var \BcBlog\Service\BlogPostsService $blogPostsService */
            $blogPostsService = $this->getService(BlogPostsServiceInterface::class);

            // 削除前にタイトルを取得
            $entity = $blogPostsService->get($id);
            if (!$entity) {
                return $this->createErrorResponse('指定されたIDのブログ記事が見つかりません');
            }

            $title = $entity->title;
            $result = $blogPostsService->delete($id);

            if ($result) {
                return $this->createSuccessResponse(
                    ['message' => 'ブログ記事を削除しました'],
                    [],
                    sprintf('ブログ記事「%s」を削除しました。', $title),
                    $loginUserId
                );
            } else {
                return $this->createErrorResponse('ブログ記事の削除に失敗しました');
            }
        });
    }

    /**
     * ブログコンテンツIDを取得
     */
    protected function getBlogContentId(?string $blogContentName): int
    {
        try {
            $blogContentsService = $this->getService(BlogContentsServiceInterface::class);
            $conditions = [];
            if($blogContentName) {
                $conditions = ['name' => $blogContentName];
            }
            $blogContent = $blogContentsService->getIndex($conditions)->first();
            if(!$blogContent) {
                throw new \Exception('ブログコンテンツが見つかりません。');
            }
            return $blogContent->id;
        } catch (\Exception $e) {
            throw new \Exception('ブログコンテンツ検索中にエラーが発生しました。' . $e->getMessage());
        }
    }

    /**
     * ブログカテゴリIDを取得
     */
    protected function getBlogCategoryId(?string $categoryName, int $blogContentId): ?int
    {
        try {
            $blogCategoriesService = $this->getService(BlogCategoriesServiceInterface::class);
            $conditions = [
                'name' => $categoryName
            ];
            $category = $blogCategoriesService->getIndex($blogContentId, $conditions)->first();

            return $category? $category->id : null;
        } catch (\Exception $e) {
            return null; // エラー時はnull
        }
    }

}
