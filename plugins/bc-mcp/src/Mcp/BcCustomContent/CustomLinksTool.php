<?php
declare(strict_types=1);

namespace BcMcp\Mcp\BcCustomContent;

use BcCustomContent\Service\CustomLinksService;
use BcCustomContent\Service\CustomLinksServiceInterface;
use PhpMcp\Server\ServerBuilder;
use BcMcp\Mcp\BaseMcpTool;

/**
 * カスタムリンクツールクラス
 *
 * カスタムリンクのCRUD操作を提供
 */
class CustomLinksTool extends BaseMcpTool
{

    /**
     * カスタムリンク関連のツールを ServerBuilder に追加
     */
    public function addToolsToBuilder(ServerBuilder $builder): ServerBuilder
    {
        return $builder
            ->withTool(
                handler: [self::class, 'addCustomLink'],
                name: 'addCustomLink',
                description: 'カスタムリンクを追加します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'name' => ['type' => 'string', 'description' => 'カスタムリンク名（必須）'],
                        'title' => ['type' => 'string', 'description' => 'カスタムリンクのタイトル（必須）'],
                        'customTableId' => ['type' => 'number', 'description' => 'カスタムテーブルID（必須）'],
                        'customFieldId' => ['type' => 'number', 'description' => 'カスタムフィールドID（必須）'],
                        'parentId' => ['type' => 'number', 'description' => '親カスタムリンクID'],
                        'beforeHead' => ['type' => 'string', 'description' => '入力欄の前見出し'],
                        'afterHead' => ['type' => 'string', 'description' => '入力欄の後見出し'],
                        'description' => ['type' => 'string', 'description' => 'ヘルプメッセージ'],
                        'attention' => ['type' => 'string', 'description' => '注意書き'],
                        'options' => ['type' => 'string', 'description' => 'フィールド属性。フィールドのコントロールに対して追加の属性を指定する場合に入力します。 属性名と値をパイプ（|）で区切って指定します。複数属性を連続で指定する事ができます。例）data-sample1|value1|data-sample2|value2'],
                        'class' => ['type' => 'string', 'description' => 'フィールドのクラス属性'],
                        'beforeLinefeed' => ['type' => 'string', 'description' => '入力欄の前に改行を入れる'],
                        'afterLinefeed' => ['type' => 'string', 'description' => '入力欄の後に改行を入れる'],
                        'displayAdminList' => ['type' => 'boolean', 'description' => '管理画面のエントリー一覧に項目を表示する'],
                        'displayFront' => ['type' => 'boolean', 'description' => 'テーマのヘルパーで呼び出せる'],
                        'searchTargetAdmin' => ['type' => 'boolean', 'description' => '管理画面で検索対象とする'],
                        'searchTargetFront' => ['type' => 'boolean', 'description' => 'テーマ、Web API において検索対象にする'],
                        'useApi' => ['type' => 'boolean', 'description' => 'Web API の返却値に含める'],
                        'required' => ['type' => 'boolean', 'description' => '必須項目とする'],
                        'status' => ['type' => 'boolean', 'description' => '公開状態（0: 無効, 1: 有効）'],
                    ],
                    'required' => ['name', 'title', 'customTableId', 'customFieldId']
                ]
            )
            ->withTool(
                handler: [self::class, 'getCustomLinks'],
                name: 'getCustomLinks',
                description: 'カスタムリンクの一覧を取得します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'customTableId' => ['type' => 'number', 'description' => 'カスタムテーブルID（必須）'],
                        'name' => ['type' => 'string', 'description' => 'カスタムリンク名'],
                        'status' => ['type' => 'number', 'description' => 'ステータス（null: 無効, publish: 有効）'],
                        'limit' => ['type' => 'number', 'description' => '取得件数（省略時は制限なし）'],
                        'page' => ['type' => 'number', 'description' => 'ページ番号（省略時は1ページ目）'],
                    ],
                    'required' => ['customTableId']
                ]
            )
            ->withTool(
                handler: [self::class, 'getCustomLink'],
                name: 'getCustomLink',
                description: '指定されたIDのカスタムリンクを取得します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => 'カスタムリンクID（必須）']
                    ],
                    'required' => ['id']
                ]
            )
            ->withTool(
                handler: [self::class, 'editCustomLink'],
                name: 'editCustomLink',
                description: '指定されたIDのカスタムリンクを編集します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => 'カスタムリンクID（必須）'],
                        'name' => ['type' => 'string', 'description' => 'カスタムリンク名'],
                        'title' => ['type' => 'string', 'description' => 'カスタムリンクのタイトル'],
                        'customTableId' => ['type' => 'number', 'description' => 'カスタムテーブルID'],
                        'customFieldId' => ['type' => 'number', 'description' => 'カスタムフィールドID'],
                        'parentId' => ['type' => 'number', 'description' => '親カスタムリンクID'],
                        'beforeHead' => ['type' => 'string', 'description' => '入力欄の前見出し'],
                        'afterHead' => ['type' => 'string', 'description' => '入力欄の後見出し'],
                        'description' => ['type' => 'string', 'description' => 'ヘルプメッセージ'],
                        'attention' => ['type' => 'string', 'description' => '注意書き'],
                        'options' => ['type' => 'string', 'description' => 'フィールド属性。フィールドのコントロールに対して追加の属性を指定する場合に入力します。 属性名と値をパイプ（|）で区切って指定します。複数属性を連続で指定する事ができます。例）data-sample1|value1|data-sample2|value2'],
                        'class' => ['type' => 'string', 'description' => 'フィールドのクラス属性'],
                        'beforeLinefeed' => ['type' => 'string', 'description' => '入力欄の前に改行を入れる'],
                        'afterLinefeed' => ['type' => 'string', 'description' => '入力欄の後に改行を入れる'],
                        'displayAdminList' => ['type' => 'boolean', 'description' => '管理画面のエントリー一覧に項目を表示する'],
                        'displayFront' => ['type' => 'boolean', 'description' => 'テーマのヘルパーで呼び出せる'],
                        'searchTargetAdmin' => ['type' => 'boolean', 'description' => '管理画面で検索対象とする'],
                        'searchTargetFront' => ['type' => 'boolean', 'description' => 'テーマ、Web API において検索対象にする'],
                        'useApi' => ['type' => 'boolean', 'description' => 'Web API の返却値に含める'],
                        'required' => ['type' => 'boolean', 'description' => '必須項目とする'],
                        'status' => ['type' => 'boolean', 'description' => '公開状態（0: 無効, 1: 有効）'],
                    ],
                    'required' => ['id']
                ]
            )
            ->withTool(
                handler: [self::class, 'deleteCustomLink'],
                name: 'deleteCustomLink',
                description: '指定されたIDのカスタムリンクを削除します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => 'カスタムリンクID（必須）']
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
            case 'addCustomLink':
                return ['POST' => "/bc-custom-content/custom_links/add.json"];
            case 'editCustomLink':
                if(empty($args['id'])) return false;
                return ['POST' => "/bc-custom-content/custom_links/edit/{$args['id']}.json"];
            case 'getCustomLinks':
                return ['GET' => "/bc-custom-content/custom_links.json"];
            case 'getCustomLink':
                if(empty($args['id'])) return false;
                return ['GET' => "/bc-custom-content/custom_links/view/{$args['id']}.json"];
            case 'deleteCustomLink':
                if(empty($args['id'])) return false;
                return ['POST' => "/bc-custom-content/custom_links/delete/{$args['id']}.json"];
            default:
                return false;
        }
    }

    /**
     * カスタムリンクを追加
     */
    public function addCustomLink(
        string $name,
        string $title,
        int $customTableId,
        int $customFieldId,
        ?int $parentId = null,
        ?string $beforeHead = null,
        ?string $afterHead = null,
        ?string $description = null,
        ?string $attention = null,
        ?string $options = null,
        ?string $class = null,
        ?string $beforeLinefeed = null,
        ?string $afterLinefeed = null,
        ?bool $displayAdminList = null,
        ?bool $displayFront = null,
        ?bool $searchTargetFront = null,
        ?bool $searchTargetAdmin = null,
        ?bool $useApi = null,
        ?bool $required = null,
        ?bool $status = null,
        ?int $loginUserId = null
    ): array
    {
        return $this->executeWithErrorHandling(function() use (
            $name, $title, $customTableId, $customFieldId, $parentId, $beforeHead, $afterHead, $description,
            $attention, $options, $class, $beforeLinefeed, $afterLinefeed, $displayAdminList, $displayFront,
            $searchTargetFront, $searchTargetAdmin, $useApi, $required, $status, $loginUserId
        ) {
            $customLinksService = $this->getService(CustomLinksServiceInterface::class);

            $data = [
                'name' => $name,
                'title' => $title,
                'customTableId' => $customTableId,
                'customFieldId' => $customFieldId,
                'parentId' => $parentId,
                'beforeHead' => $beforeHead,
                'afterHead' => $afterHead,
                'description' => $description,
                'attention' => $attention,
                'options' => $options,
                'class' => $class,
                'beforeLinefeed' => $beforeLinefeed,
                'afterLinefeed' => $afterLinefeed,
                'displayAdminList' => $displayAdminList,
                'displayFront' => $displayFront,
                'searchTargetFront' => $searchTargetFront,
                'searchTargetAdmin' => $searchTargetAdmin,
                'useApi' => $useApi,
                'required' => $required,
                'status' => $status,
            ];

            $result = $customLinksService->create($data);

            if ($result) {
                return $this->createSuccessResponse(
                    $result->toArray(),
                    ['customLink' => $result->toArray()],
                    sprintf('カスタムリンク「%s」を追加しました。', $title),
                    $loginUserId
                );
            } else {
                return $this->createErrorResponse('カスタムリンクの保存に失敗しました');
            }
        });
    }

    /**
     * カスタムリンク一覧を取得
     */
    public function getCustomLinks(
        int $customTableId,
        ?string $name = null,
        ?string $status = null,
        ?int $limit = null,
        ?int $page = 1
    ): array
    {
        return $this->executeWithErrorHandling(function() use ($customTableId, $name, $status, $limit, $page) {
            /** @var CustomLinksService $customLinksService */
            $customLinksService = $this->getService(CustomLinksServiceInterface::class);

            $conditions = ['finder' => 'all'];
            if (!empty($name)) $conditions['name'] = $name;
            if (isset($status)) $conditions['status'] = $status;
            if (!empty($limit)) $conditions['limit'] = $limit;
            if (!empty($page)) $conditions['page'] = $page;

            // CustomLinksService::getIndex() は custom_table_id を最初の引数として期待している
            $results = $customLinksService->getIndex($customTableId, $conditions)->toArray();

            return $this->createSuccessResponse([
                'results' => $results,
                'pagination' => [
                    'page' => $page ?? 1,
                    'limit' => $limit ?? null,
                    'count' => count($results)
                ]
            ]);
        });
    }

    /**
     * カスタムリンクを取得
     */
    public function getCustomLink(int $id): array
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            /** @var CustomLinksService $customLinksService */
            $customLinksService = $this->getService(CustomLinksServiceInterface::class);
            $result = $customLinksService->get($id);

            if ($result) {
                return $this->createSuccessResponse($result->toArray());
            } else {
                return $this->createErrorResponse('指定されたIDのカスタムリンクが見つかりません');
            }
        });
    }

    /**
     * カスタムリンクを編集
     */
    public function editCustomLink(
        int $id,
        ?string $name = null,
        ?string $title = null,
        ?int $customTableId = null,
        ?int $customFieldId = null,
        ?int $parentId = null,
        ?string $beforeHead = null,
        ?string $afterHead = null,
        ?string $description = null,
        ?string $attention = null,
        ?string $options = null,
        ?string $class = null,
        ?string $beforeLinefeed = null,
        ?string $afterLinefeed = null,
        ?bool $displayAdminList = null,
        ?bool $displayFront = null,
        ?bool $searchTargetFront = null,
        ?bool $searchTargetAdmin = null,
        ?bool $useApi = null,
        ?bool $required = null,
        ?bool $status = null,
        ?int $loginUserId = null
    ): array
    {
        return $this->executeWithErrorHandling(function() use (
            $id, $name, $title, $customTableId, $customFieldId, $parentId, $beforeHead, $afterHead, $description,
            $attention, $options, $class, $beforeLinefeed, $afterLinefeed, $displayAdminList, $displayFront,
            $searchTargetFront, $searchTargetAdmin, $useApi, $required, $status, $loginUserId
        ) {
            $customLinksService = $this->getService(CustomLinksServiceInterface::class);

            $entity = $customLinksService->get($id);

            if (!$entity) {
                return $this->createErrorResponse('指定されたIDのカスタムリンクが見つかりません');
            }

            $data = [];
            if ($name !== null) $data['name'] = $name;
            if ($title !== null) $data['title'] = $title;
            if ($customTableId !== null) $data['custom_table_id'] = $customTableId;
            if ($customFieldId !== null) $data['custom_field_id'] = $customFieldId;
            if ($parentId !== null) $data['parent_id'] = $parentId;
            if ($beforeHead !== null) $data['before_head'] = $beforeHead;
            if ($afterHead !== null) $data['after_head'] = $afterHead;
            if ($description !== null) $data['description'] = $description;
            if ($attention !== null) $data['attention'] = $attention;
            if ($options !== null) $data['options'] = $options;
            if ($class !== null) $data['class'] = $class;
            if ($beforeLinefeed !== null) $data['before_linefeed'] = $beforeLinefeed;
            if ($afterLinefeed !== null) $data['after_linefeed'] = $afterLinefeed;
            if ($displayAdminList !== null) $data['display_admin_list'] = $displayAdminList;
            if ($displayFront !== null) $data['display_front'] = $displayFront;
            if ($searchTargetFront !== null) $data['search_target_front'] = $searchTargetFront;
            if ($searchTargetAdmin !== null) $data['search_target_admin'] = $searchTargetAdmin;
            if ($useApi !== null) $data['useApi'] = $useApi;
            if ($required !== null) $data['required'] = $required;
            if ($status !== null) $data['status'] = $status;

            $result = $customLinksService->update($entity, $data);

            if ($result) {
                return $this->createSuccessResponse(
                    $result->toArray(),
                    ['customLink' => $result->toArray()],
                    sprintf('カスタムリンク「%s」を編集しました。', $result->title),
                    $loginUserId
                );
            } else {
                return $this->createErrorResponse('カスタムリンクの更新に失敗しました');
            }
        });
    }

    /**
     * カスタムリンクを削除
     */
    public function deleteCustomLink(int $id, ?int $loginUserId = null): array
    {
        return $this->executeWithErrorHandling(function() use ($id, $loginUserId) {
            /** @var CustomLinksService $customLinksService */
            $customLinksService = $this->getService(CustomLinksServiceInterface::class);

            // 削除前にタイトルを取得してログ用に保存
            $entity = $customLinksService->get($id);
            if (!$entity) {
                return $this->createErrorResponse('指定されたIDのカスタムリンクが見つかりません');
            }
            $title = $entity->title;

            $result = $customLinksService->delete($id);

            if ($result) {
                return $this->createSuccessResponse(
                    ['message' => 'カスタムリンクを削除しました'],
                    ['customLink' => ['title' => $title]],
                    sprintf('カスタムリンク「%s」を削除しました。', $title),
                    $loginUserId
                );
            } else {
                return $this->createErrorResponse('カスタムリンクの削除に失敗しました');
            }
        });
    }
}
