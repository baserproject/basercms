<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcMcp\Mcp\BaserCore;

use BaserCore\Service\ContentsService;
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Service\PagesService;
use BaserCore\Service\PagesServiceInterface;
use BcMcp\Mcp\BaseMcpTool;

/**
 * 固定ページツールクラス
 *
 * 固定ページのCRUD操作を提供する。
 *
 * 固定ページは pages テーブルと contents テーブルの複合構造であり、名前が
 * 紛らわしい点に注意する。
 * - pages.contents … ページ本文（HTML）
 * - pages.content（Contents アソシエーション）… タイトル・URL・公開状態・親フォルダ
 */
class PagesTool extends BaseMcpTool
{

    /**
     * 固定ページ関連のツールをサーバーに登録する
     *
     * @param \Mcp\Server\McpServer $server SDK のサーバー
     * @return \Mcp\Server\McpServer
     */
    public function registerTools(\Mcp\Server\McpServer $server): \Mcp\Server\McpServer
    {
        return $server
            ->tool(
                name: 'getPages',
                description: '固定ページの一覧を取得します',
                callback: [$this, 'getPages'],
                outputSchema: self::OUTPUT_SCHEMA,
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'keyword' => ['type' => 'string', 'description' => '検索キーワード（ページ本文を対象に検索）'],
                        'siteId' => ['type' => 'number', 'description' => 'サイトID（省略時は全て）'],
                        'status' => ['type' => 'number', 'description' => '公開ステータス（1: 公開のみ）（省略時は全て）'],
                        'limit' => ['type' => 'number', 'description' => '取得件数（省略時は10件）'],
                        'page' => ['type' => 'number', 'description' => 'ページ番号（省略時は1ページ目）'],
                    ]
                ]
            )
            ->tool(
                name: 'getPage',
                description: '指定されたIDの固定ページを取得します',
                callback: [$this, 'getPage'],
                outputSchema: self::OUTPUT_SCHEMA,
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => '固定ページID（必須）'],
                    ],
                    'required' => ['id']
                ]
            )
            ->tool(
                name: 'addPage',
                description: '固定ページを追加します',
                callback: [$this, 'addPage'],
                outputSchema: self::OUTPUT_SCHEMA,
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'title' => ['type' => 'string', 'description' => 'ページタイトル（必須）'],
                        'content' => ['type' => 'string', 'description' => 'ページ本文、マークダウン不可、HTML推奨'],
                        'name' => ['type' => 'string', 'description' => 'URLのスラッグ。URLにおけるページを特定する識別子（省略時は自動採番）'],
                        'parentId' => ['type' => 'number', 'description' => '親フォルダのコンテンツID（省略時はサイトルート）'],
                        'siteId' => ['type' => 'number', 'description' => 'サイトID（省略時は1）'],
                        'status' => ['type' => 'number', 'description' => '公開ステータス（0: 非公開, 1: 公開）（省略時は0）'],
                        'description' => ['type' => 'string', 'description' => 'ページの説明'],
                        'publishBegin' => ['type' => 'string', 'format' => 'date-time', 'description' => '公開開始日時（省略時はなし）'],
                        'publishEnd' => ['type' => 'string', 'format' => 'date-time', 'description' => '公開終了日時（省略時はなし）'],
                        'pageTemplate' => ['type' => 'string', 'description' => 'ページテンプレート名（省略時はデフォルト）'],
                        'eyeCatch' => ['type' => 'string', 'description' => 'アイキャッチ画像。外部画像URLを直接指定'],
                    ],
                    'required' => ['title']
                ]
            )
            ->tool(
                name: 'editPage',
                description: '固定ページを編集します',
                callback: [$this, 'editPage'],
                outputSchema: self::OUTPUT_SCHEMA,
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => '固定ページID（必須）'],
                        'title' => ['type' => 'string', 'description' => 'ページタイトル'],
                        'content' => ['type' => 'string', 'description' => 'ページ本文、マークダウン不可、HTML推奨'],
                        'name' => ['type' => 'string', 'description' => 'URLのスラッグ。URLにおけるページを特定する識別子'],
                        'parentId' => ['type' => 'number', 'description' => '親フォルダのコンテンツID'],
                        'status' => ['type' => 'number', 'description' => '公開ステータス（0: 非公開, 1: 公開）'],
                        'description' => ['type' => 'string', 'description' => 'ページの説明'],
                        'publishBegin' => ['type' => 'string', 'format' => 'date-time', 'description' => '公開開始日時'],
                        'publishEnd' => ['type' => 'string', 'format' => 'date-time', 'description' => '公開終了日時'],
                        'pageTemplate' => ['type' => 'string', 'description' => 'ページテンプレート名'],
                        'eyeCatch' => ['type' => 'string', 'description' => 'アイキャッチ画像。外部画像URLを直接指定'],
                    ],
                    'required' => ['id']
                ]
            )
            ->tool(
                name: 'deletePage',
                description: '指定されたIDの固定ページを削除します。ゴミ箱には残らず完全に削除されます',
                callback: [$this, 'deletePage'],
                outputSchema: self::OUTPUT_SCHEMA,
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => '固定ページID（必須）'],
                    ],
                    'required' => ['id']
                ]
            );
    }

    /**
     * 権限チェック用のURLを取得する
     *
     * @param string $action アクション名
     * @param array $args 引数
     * @return array|false
     */
    public static function getPermissionUrl($action, $args = [])
    {
        switch ($action) {
            case 'addPage':
                return ['POST' => '/baser-core/pages/add.json'];
            case 'editPage':
                if (empty($args['id'])) return false;
                return ['POST' => "/baser-core/pages/edit/{$args['id']}.json"];
            case 'deletePage':
                if (empty($args['id'])) return false;
                return ['POST' => "/baser-core/pages/delete/{$args['id']}.json"];
            case 'getPages':
                return ['GET' => '/baser-core/pages/index.json'];
            case 'getPage':
                if (empty($args['id'])) return false;
                return ['GET' => "/baser-core/pages/view/{$args['id']}.json"];
            default:
                return false;
        }
    }

    /**
     * 固定ページの一覧を取得する
     *
     * @param string|null $keyword 検索キーワード
     * @param int|null $siteId サイトID
     * @param int|null $status 公開ステータス
     * @param int|null $limit 取得件数
     * @param int|null $page ページ番号
     * @return array
     */
    public function getPages(
        ?string $keyword = null,
        ?int $siteId = null,
        ?int $status = null,
        ?int $limit = 10,
        ?int $page = 1
    ): array
    {
        return $this->executeWithErrorHandling(function() use ($keyword, $siteId, $status, $limit, $page) {
            /** @var PagesService $pagesService */
            $pagesService = $this->getService(PagesServiceInterface::class);

            $params = ['limit' => $limit ?? 10];
            // getIndex は contents（ページ本文）に対する LIKE 検索に対応する
            if ($keyword !== null) $params['contents'] = $keyword;
            if ($status === 1) $params['status'] = 'publish';

            $query = $pagesService->getIndex($params);
            if ($siteId !== null) {
                $query->where(['Contents.site_id' => $siteId]);
            }
            $page = $page ?? 1;
            if ($page > 1) {
                $query->offset(($page - 1) * ($limit ?? 10));
            }

            $pages = [];
            foreach($query->all() as $entity) {
                $pages[] = $entity->toArray();
            }
            return $this->createSuccessResponse($pages);
        });
    }

    /**
     * 指定されたIDの固定ページを取得する
     *
     * @param int $id 固定ページID
     * @return array
     */
    public function getPage(int $id): array
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            /** @var PagesService $pagesService */
            $pagesService = $this->getService(PagesServiceInterface::class);
            $page = $pagesService->get($id);
            return $this->createSuccessResponse($page->toArray());
        });
    }

    /**
     * 固定ページを追加する
     *
     * 引数の $content（ページ本文）は pages.contents へ、タイトルや URL などは
     * content キー（Contents アソシエーション）へ格納する。
     *
     * @param string $title ページタイトル
     * @param string|null $content ページ本文
     * @param string|null $name URLのスラッグ
     * @param int|null $parentId 親フォルダのコンテンツID
     * @param int|null $siteId サイトID
     * @param int|null $status 公開ステータス
     * @param string|null $description 説明
     * @param string|null $publishBegin 公開開始日時
     * @param string|null $publishEnd 公開終了日時
     * @param string|null $pageTemplate ページテンプレート
     * @param string|null $eyeCatch アイキャッチ画像
     * @param int|null $loginUserId ログインユーザーID
     * @return array
     */
    public function addPage(
        string $title,
        ?string $content = null,
        ?string $name = null,
        ?int $parentId = null,
        ?int $siteId = null,
        ?int $status = 0,
        ?string $description = null,
        ?string $publishBegin = null,
        ?string $publishEnd = null,
        ?string $pageTemplate = null,
        ?string $eyeCatch = null,
        ?int $loginUserId = null
    ): array
    {
        // 認証済みの操作者はリクエストのコンテキストから解決する
        $loginUserId = $this->resolveLoginUserId($loginUserId);
        return $this->executeWithErrorHandling(function() use (
            $title, $content, $name, $parentId, $siteId, $status,
            $description, $publishBegin, $publishEnd, $pageTemplate, $eyeCatch, $loginUserId
        ) {
            if (empty($title)) {
                return $this->createErrorResponse('タイトルは必須です');
            }

            /** @var PagesService $pagesService */
            $pagesService = $this->getService(PagesServiceInterface::class);

            $siteId = $siteId ?? 1;
            $contentData = [
                'title' => $title,
                // plugin と type は固定値のためツール側で補う
                'plugin' => 'BaserCore',
                'type' => 'Page',
                'site_id' => $siteId,
                'parent_id' => $parentId ?? $this->getSiteRootContentId($siteId),
                'self_status' => (bool)$status,
                'author_id' => $loginUserId,
            ];
            if ($name !== null) $contentData['name'] = $name;
            if ($description !== null) $contentData['description'] = $description;
            if ($publishBegin !== null) $contentData['publish_begin'] = $publishBegin;
            if ($publishEnd !== null) $contentData['publish_end'] = $publishEnd;
            if ($eyeCatch !== null) $contentData['eyecatch'] = $eyeCatch;

            $postData = [
                // ページ本文は pages.contents に保存する
                'contents' => $content ?? '',
                // draft を NULL のままにすると PagesService::getIndex() が付与する
                // draft の LIKE 条件に一致せず、一覧に出てこなくなる
                'draft' => '',
                'content' => $contentData,
            ];
            if ($pageTemplate !== null) $postData['page_template'] = $pageTemplate;

            $page = $pagesService->create($postData);

            return $this->createSuccessResponse(
                $page->toArray(),
                [],
                sprintf('固定ページ「%s」を追加しました。', $title),
                $loginUserId
            );
        });
    }

    /**
     * 固定ページを編集する
     *
     * 指定された項目のみを更新する。
     *
     * @param int $id 固定ページID
     * @param string|null $title ページタイトル
     * @param string|null $content ページ本文
     * @param string|null $name URLのスラッグ
     * @param int|null $parentId 親フォルダのコンテンツID
     * @param int|null $status 公開ステータス
     * @param string|null $description 説明
     * @param string|null $publishBegin 公開開始日時
     * @param string|null $publishEnd 公開終了日時
     * @param string|null $pageTemplate ページテンプレート
     * @param string|null $eyeCatch アイキャッチ画像
     * @param int|null $loginUserId ログインユーザーID
     * @return array
     */
    public function editPage(
        int $id,
        ?string $title = null,
        ?string $content = null,
        ?string $name = null,
        ?int $parentId = null,
        ?int $status = null,
        ?string $description = null,
        ?string $publishBegin = null,
        ?string $publishEnd = null,
        ?string $pageTemplate = null,
        ?string $eyeCatch = null,
        ?int $loginUserId = null
    ): array
    {
        // 認証済みの操作者はリクエストのコンテキストから解決する
        $loginUserId = $this->resolveLoginUserId($loginUserId);
        return $this->executeWithErrorHandling(function() use (
            $id, $title, $content, $name, $parentId, $status,
            $description, $publishBegin, $publishEnd, $pageTemplate, $eyeCatch, $loginUserId
        ) {
            /** @var PagesService $pagesService */
            $pagesService = $this->getService(PagesServiceInterface::class);
            $target = $pagesService->get($id);

            $contentData = ['id' => $target->content->id];
            if ($title !== null) $contentData['title'] = $title;
            if ($name !== null) $contentData['name'] = $name;
            if ($parentId !== null) $contentData['parent_id'] = $parentId;
            if ($status !== null) $contentData['self_status'] = (bool)$status;
            if ($description !== null) $contentData['description'] = $description;
            if ($publishBegin !== null) $contentData['publish_begin'] = $publishBegin;
            if ($publishEnd !== null) $contentData['publish_end'] = $publishEnd;
            if ($eyeCatch !== null) $contentData['eyecatch'] = $eyeCatch;

            $postData = ['id' => $id];
            // ページ本文は pages.contents に保存する
            if ($content !== null) $postData['contents'] = $content;
            if ($pageTemplate !== null) $postData['page_template'] = $pageTemplate;
            if (count($contentData) > 1) $postData['content'] = $contentData;

            $page = $pagesService->update($target, $postData);

            return $this->createSuccessResponse(
                $page->toArray(),
                [],
                sprintf('固定ページ「%s」を編集しました。', $page->content->title),
                $loginUserId
            );
        });
    }

    /**
     * 固定ページを削除する
     *
     * PagesService::delete() は完全削除であり、pages のレコードと
     * 紐づく contents のレコードがいずれも削除される。ゴミ箱にも残らないため
     * 復元できない点に注意する。
     *
     * @param int $id 固定ページID
     * @param int|null $loginUserId ログインユーザーID
     * @return array
     */
    public function deletePage(int $id, ?int $loginUserId = null): array
    {
        // 認証済みの操作者はリクエストのコンテキストから解決する
        $loginUserId = $this->resolveLoginUserId($loginUserId);
        return $this->executeWithErrorHandling(function() use ($id, $loginUserId) {
            /** @var PagesService $pagesService */
            $pagesService = $this->getService(PagesServiceInterface::class);
            $page = $pagesService->get($id);
            $title = $page->content->title;

            if (!$pagesService->delete($id)) {
                return $this->createErrorResponse('固定ページの削除に失敗しました');
            }

            return $this->createSuccessResponse(
                ['id' => $id, 'title' => $title],
                [],
                sprintf('固定ページ「%s」を削除しました。', $title),
                $loginUserId
            );
        });
    }

    /**
     * サイトルートのコンテンツIDを取得する
     *
     * 親フォルダが指定されなかった場合の配置先として使う。
     *
     * @param int $siteId サイトID
     * @return int|null
     */
    public function getSiteRootContentId(int $siteId): ?int
    {
        /** @var ContentsService $contentsService */
        $contentsService = $this->getService(ContentsServiceInterface::class);
        $siteRoot = $contentsService->getSiteRoot($siteId);
        return $siteRoot? $siteRoot->id : null;
    }

}
