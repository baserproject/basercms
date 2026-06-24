<?php
declare(strict_types=1);

namespace BcMcp\Mcp\BcCustomContent;

use BcCustomContent\Service\CustomFieldsService;
use BcCustomContent\Service\CustomFieldsServiceInterface;
use BcCustomContent\Service\CustomTablesService;
use BcCustomContent\Service\CustomTablesServiceInterface;
use PhpMcp\Server\ServerBuilder;
use BcMcp\Mcp\BaseMcpTool;

/**
 * カスタムテーブルツールクラス
 *
 * カスタムテーブルのCRUD操作を提供
 */
class CustomTablesTool extends BaseMcpTool
{

    /**
     * カスタムテーブル関連のツールを ServerBuilder に追加
     */
    public function addToolsToBuilder(ServerBuilder $builder): ServerBuilder
    {
        return $builder
            ->withTool(
                handler: [self::class, 'addCustomTable'],
                name: 'addCustomTable',
                description: 'カスタムテーブルを追加し、指定されたカスタムフィールドを関連付けます。フィールドを関連付けるためには、事前にカスタムフィールドが作成されている必要があります。',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'title' => ['type' => 'string', 'description' => 'テーブルタイトル（必須）'],
                        'name' => ['type' => 'string', 'description' => 'テーブル名（英数小文字、アンダースコアのみ）'],
                        'type' => ['type' => 'number', 'enum' => [1, 2], 'description' => 'テーブルタイプ（1:コンテンツ, 2:マスタ）（初期値は1）'],
                        'displayField' => ['type' => 'string', 'description' => '表示フィールド（type がコンテンツの場合に指定要、title / name / 関連付いたカスタムリンクの name から選択、初期値は title）'],
                        'hasChild' => ['type' => 'boolean', 'description' => '子テーブルを持つかどうか（false:持たない, true:持つ）（type がマスタの場合に指定が可能。初期値は0）'],
                        'customFieldNames' => [
                            'type' => 'array',
                            'items' => ['type' => 'string'],
                            'description' => '関連付けるカスタムフィールドの名前配列'
                        ]
                    ],
                    'required' => ['title']
                ]
            )
            ->withTool(
                handler: [self::class, 'editCustomTable'],
                name: 'editCustomTable',
                description: '指定されたIDのカスタムテーブルを編集します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => 'カスタムテーブルID（必須）'],
                        'title' => ['type' => 'string', 'description' => 'テーブルタイトル'],
                        'name' => ['type' => 'string', 'description' => 'テーブル名（英数小文字、アンダースコアのみ）'],
                        'type' => ['type' => 'number', 'enum' => [1, 2], 'description' => 'テーブルタイプ（1:コンテンツ, 2:マスタ）'],
                        'displayField' => ['type' => 'string', 'description' => '表示フィールド（type がコンテンツの場合に指定要、title / name / 関連付いたカスタムリンクの name から選択、初期値は title）'],
                        'hasChild' => ['type' => 'boolean', 'description' => '子テーブルを持つかどうか（false:持たない, true:持つ）（type がマスタの場合に指定が可能。初期値は0）'],
                        'customFieldNames' => [
                            'type' => 'array',
                            'items' => ['type' => 'string'],
                            'description' => '関連付けるカスタムフィールドの名前配列'
                        ]
                    ],
                    'required' => ['id']
                ]
            )
            ->withTool(
                handler: [self::class, 'getCustomTables'],
                name: 'getCustomTables',
                description: 'カスタムテーブルの一覧を取得します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'type' => ['type' => 'string', 'description' => 'テーブルタイプ']
                    ]
                ]
            )
            ->withTool(
                handler: [self::class, 'getCustomTable'],
                name: 'getCustomTable',
                description: '指定されたIDのカスタムテーブルを取得します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => 'カスタムテーブルID（必須）']
                    ],
                    'required' => ['id']
                ]
            )
            ->withTool(
                handler: [self::class, 'deleteCustomTable'],
                name: 'deleteCustomTable',
                description: '指定されたIDのカスタムテーブルを削除します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => 'カスタムテーブルID（必須）']
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
            case 'addCustomTable':
                return ['POST' => "/bc-custom-content/custom_tables/add.json"];
            case 'editCustomTable':
                if(empty($args['id'])) return false;
                return ['POST' => "/bc-custom-content/custom_tables/edit/{$args['id']}.json"];
            case 'getCustomTables':
                return ['GET' => "/bc-custom-content/custom_tables/index.json"];
            case 'getCustomTable':
                if(empty($args['id'])) return false;
                return ['GET' => "/bc-custom-content/custom_tables/view/{$args['id']}.json"];
            case 'deleteCustomTable':
                if(empty($args['id'])) return false;
                return ['POST' => "/bc-custom-content/custom_tables/delete/{$args['id']}.json"];
            default:
                return false;
        }
    }

    /**
     * カスタムテーブルを追加
     */
    public function addCustomTable(
        string $title,
        ?string $name = null,
        ?int $type = 1,
        ?string $displayField = 'title',
        ?int $hasChild = 0,
        ?array $customFieldNames = [],
        ?int $loginUserId = null
    ): array
    {
        return $this->executeWithErrorHandling(function() use (
            $title, $name, $type, $displayField, $hasChild, $customFieldNames, $loginUserId
        ) {
            $customTablesService = $this->getService(CustomTablesServiceInterface::class);

            $data = [
                'title' => $title,
                'name' => $name ?? 'table_' . time(),
                'type' => $type ?? 1,
                'display_field' => $displayField ?? 'title',
                'has_child' => $hasChild ?? 0
            ];

            $result = $customTablesService->create($data);

            if ($result && !empty($customFieldNames)) {
                // カスタムフィールドとの関連付け
                $customLinks = $this->createCustomLinks($customFieldNames);
                if ($customLinks) {
                    $customTable = $result->toArray();
                    $customTable['custom_links'] = $customLinks;
                    $result = $customTablesService->update($result, $customTable);
                }
            }

            if ($result) {
                return $this->createSuccessResponse(
                    $result->toArray(),
                    ['customTable' => $result->toArray()],
                    sprintf('カスタムテーブル「%s」を追加しました。', $title),
                    $loginUserId
                );
            } else {
                return $this->createErrorResponse('カスタムテーブルの保存に失敗しました');
            }
        });
    }

    /**
     * カスタムフィールド名の配列からカスタムリンクの配列を作成
     * @param $customFieldNames
     * @return array
     */
    private function createCustomLinks($customFieldNames)
    {
        $customFieldsService = $this->getService(CustomFieldsServiceInterface::class);
        $customLinks = [];
        if (!empty($customFieldNames)) {
            $i = 0;
            foreach($customFieldNames as $fieldName) {
                $customField = $customFieldsService->getIndex(['name' => $fieldName])->first();
                if ($customField) {
                    $customLinks["new_" . $i + 1] = [
                        "name" => $customField->name,
                        "custom_field_id" => $customField->id,
                        "type" => $customField->type,
                        "display_front" => true,
                        "use_api" => true,
                        "status" => true,
                        "title" => $customField->title,
                        "search_target_admin" => true,
                        "search_target_front" => true
                    ];
                    $i++;
                }
            }
        }
        return $customLinks;
    }

    /**
     * カスタムテーブル一覧を取得
     */
    public function getCustomTables($type = null): array
    {
        return $this->executeWithErrorHandling(function() use ($type) {
            /** @var CustomTablesService $customTablesService */
            $customTablesService = $this->getService(CustomTablesServiceInterface::class);

            $conditions = [];
            if (!empty($type)) $conditions['type'] = $type;

            $results = $customTablesService->getIndex($conditions)->toArray();
            return $this->createSuccessResponse($results);
        });
    }

    /**
     * カスタムテーブルを取得
     */
    public function getCustomTable(int $id): array
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            /** @var CustomFieldsService $customTablesService */
            $customTablesService = $this->getService(CustomTablesServiceInterface::class);
            $result = $customTablesService->get($id);

            if ($result) {
                return $this->createSuccessResponse($result->toArray());
            } else {
                return $this->createErrorResponse('指定されたIDのカスタムテーブルが見つかりません');
            }
        });
    }

    /**
     * カスタムテーブルを編集
     */
    public function editCustomTable(
        int $id,
        string $title,
        ?string $name = null,
        ?int $type = 1,
        ?string $displayField = 'title',
        ?int $hasChild = 0,
        ?array $customFieldNames = [],
        ?int $loginUserId = null
    ): array
    {
        return $this->executeWithErrorHandling(function() use (
            $id, $title, $name, $type, $displayField, $hasChild, $customFieldNames, $loginUserId
        ) {
            /** @var CustomTablesService $customTablesService */
            $customTablesService = $this->getService(CustomTablesServiceInterface::class);
            $entity = $customTablesService->get($id);

            if (!$entity) return $this->createErrorResponse('指定されたIDのカスタムテーブルが見つかりません');

            $data = [];
            if ($title !== null) $data['title'] = $title;
            if ($name !== null) $data['name'] = $name;
            if ($type !== null) $data['type'] = $type;
            if ($displayField !== null) $data['displayField'] = $displayField;
            if ($hasChild !== null) $data['hasChild'] = $hasChild;

            $result = $customTablesService->update($entity, $data);

            // カスタムフィールドとの関連付けを更新
            if ($result && !empty($customFieldNames)) {
                // カスタムフィールドとの関連付け
                $customLinks = $this->createCustomLinks($customFieldNames);
                if ($customLinks) {
                    $customTable = $result->toArray();
                    $customTable['custom_links'] = $customLinks;
                    $result = $customTablesService->update($result, $customTable);
                }
            }

            if ($result) {
                return $this->createSuccessResponse(
                    $result->toArray(),
                    ['customTable' => $result->toArray()],
                    sprintf('カスタムテーブル「%s」を編集しました。', $result->title),
                    $loginUserId
                );
            } else {
                return $this->createErrorResponse('カスタムテーブルの更新に失敗しました');
            }
        });
    }

    /**
     * カスタムテーブルを削除
     */
    public function deleteCustomTable(int $id, ?int $loginUserId = null): array
    {
        return $this->executeWithErrorHandling(function() use ($id, $loginUserId) {
            /** @var CustomTablesService $customTablesService */
            $customTablesService = $this->getService(CustomTablesServiceInterface::class);

            // 削除前にタイトルを取得してログ用に保存
            $entity = $customTablesService->get($id);
            if (!$entity) {
                return $this->createErrorResponse('指定されたIDのカスタムテーブルが見つかりません');
            }
            $title = $entity->title;

            $result = $customTablesService->delete($id);

            if ($result) {
                return $this->createSuccessResponse(
                    ['message' => 'カスタムテーブルを削除しました'],
                    ['customTable' => ['title' => $title]],
                    sprintf('カスタムテーブル「%s」を削除しました。', $title),
                    $loginUserId
                );
            } else {
                return $this->createErrorResponse('カスタムテーブルの削除に失敗しました');
            }
        });
    }

}
