<?php
declare(strict_types=1);

namespace BcMcp\Mcp\BcCustomContent;

use BcCcFile\Utility\BcCcFileUtil;
use BcCustomContent\Service\CustomEntriesService;
use BcCustomContent\Service\CustomEntriesServiceInterface;
use Cake\ORM\TableRegistry;
use PhpMcp\Server\ServerBuilder;
use BcMcp\Mcp\BaseMcpTool;
use InvalidArgumentException;

/**
 * カスタムエントリーツールクラス
 *
 * カスタムエントリーのCRUD操作を提供
 */
class CustomEntriesTool extends BaseMcpTool
{
    /**
     * カスタムエントリー関連のツールを ServerBuilder に追加
     */
    public function addToolsToBuilder(ServerBuilder $builder): ServerBuilder
    {
        return $builder
            ->withTool(
                handler: [self::class, 'addCustomEntry'],
                name: 'addCustomEntry',
                description: 'カスタムエントリーを追加します。カスタムエントリーを追加するには、カスタムテーブルが必要です。事前に作成するか既存のカスタムテーブルIDを指定してください。フロントエンドに表示させるには、カスタムテーブルがカスタムコンテンツと紐づいている必要があります。',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'customTableId' => ['type' => 'number', 'description' => 'カスタムテーブルID（必須）'],
                        'title' => ['type' => 'string', 'description' => 'タイトル（必須）'],
                        'parentId' => ['type' => 'number', 'description' => '親カスタムリンクID（対象となるカスタムテーブルの type がマスタの場合のみ指定可能）'],
                        'name' => ['type' => 'string', 'default' => '', 'description' => 'スラッグ（初期値空文字）'],
                        'creatorId' => ['type' => 'number', 'description' => '投稿者ID（省略時はログインユーザーID）'],
                        'status' => ['type' => 'boolean', 'default' => false, 'description' => '公開状態（デフォルト：false）'],
                        'publishBegin' => ['type' => 'string', 'description' => '公開開始日（YYYY-MM-DD HH:mm:ss形式、省略可）'],
                        'publishEnd' => ['type' => 'string', 'description' => '公開終了日（YYYY-MM-DD HH:mm:ss形式、省略可）'],
                        'published' => ['type' => 'string', 'description' => '公開日（YYYY-MM-DD HH:mm:ss形式、省略時は当日）'],
                        'customFields' => [
                            'type' => 'object',
                            'additionalProperties' => true,
                            'description' => 'カスタムフィールドの値（フィールド名をキーとするオブジェクト）、ファイルアップロードのフィールドの場合は、外部画像URLを直接指定'
                        ]
                    ],
                    'required' => ['customTableId']
                ]
            )
            ->withTool(
                handler: [self::class, 'editCustomEntry'],
                name: 'editCustomEntry',
                description: '指定されたIDのカスタムエントリーを編集します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'customTableId' => ['type' => 'number', 'description' => 'カスタムテーブルID（必須）'],
                        'id' => ['type' => 'number', 'description' => 'カスタムエントリーID（必須）'],
                        'title' => ['type' => 'string', 'description' => 'タイトル（必須）'],
                        'parentId' => ['type' => 'number', 'description' => '親カスタムリンクID（対象となるカスタムテーブルの type がマスタの場合のみ指定可能）'],
                        'name' => ['type' => 'string', 'default' => '', 'description' => 'スラッグ（初期値空文字）'],
                        'creatorId' => ['type' => 'number', 'description' => '投稿者ID'],
                        'status' => ['type' => 'boolean', 'default' => false, 'description' => '公開状態（デフォルト：false）'],
                        'publishBegin' => ['type' => 'string', 'description' => '公開開始日（YYYY-MM-DD HH:mm:ss形式、省略可）'],
                        'publishEnd' => ['type' => 'string', 'description' => '公開終了日（YYYY-MM-DD HH:mm:ss形式、省略可）'],
                        'published' => ['type' => 'string', 'description' => '公開日（YYYY-MM-DD HH:mm:ss形式、省略時は当日）'],
                        'customFields' => [
                            'type' => 'object',
                            'additionalProperties' => true,
                            'description' => 'カスタムフィールドの値（フィールド名をキーとするオブジェクト）、ファイルアップロードのフィールドの場合は、外部画像URLを直接指定'
                        ]
                    ],
                    'required' => ['customTableId', 'id']
                ]
            )
            ->withTool(
                handler: [self::class, 'getCustomEntries'],
                name: 'getCustomEntries',
                description: 'カスタムエントリーの一覧を取得します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'customTableId' => ['type' => 'number', 'description' => 'カスタムテーブルID（必須）'],
                        'limit' => ['type' => 'number', 'default' => 20, 'description' => '取得件数（デフォルト: 20）'],
                        'page' => ['type' => 'number', 'default' => 1, 'description' => 'ページ番号（デフォルト: 1）'],
                        'status' => ['type' => 'number', 'description' => 'ステータス（null: 非公開, publish: 公開）']
                    ],
                    'required' => ['customTableId']
                ]
            )
            ->withTool(
                handler: [self::class, 'getCustomEntry'],
                name: 'getCustomEntry',
                description: '指定されたIDのカスタムエントリーを取得します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'customTableId' => ['type' => 'number', 'description' => 'カスタムテーブルID（必須）'],
                        'id' => ['type' => 'number', 'description' => 'カスタムエントリーID（必須）']
                    ],
                    'required' => ['customTableId', 'id']
                ]
            )
            ->withTool(
                handler: [self::class, 'deleteCustomEntry'],
                name: 'deleteCustomEntry',
                description: '指定されたIDのカスタムエントリーを削除します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'customTableId' => ['type' => 'number', 'description' => 'カスタムテーブルID（必須）'],
                        'id' => ['type' => 'number', 'description' => 'カスタムエントリーID（必須）']
                    ],
                    'required' => ['customTableId', 'id']
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
            case 'addCustomEntry':
                return ['POST' => "/bc-custom-content/custom_entries/add.json"];
            case 'editCustomEntry':
                if(empty($args['id'])) return false;
                return ['POST' => "/bc-custom-content/custom_entries/edit/{$args['id']}.json"];
            case 'getCustomEntries':
                return ['GET' => "/bc-custom-content/custom_entries.json"];
            case 'getCustomEntry':
                if(empty($args['id'])) return false;
                return ['GET' => "/bc-custom-content/custom_entries/view/{$args['id']}.json"];
            case 'deleteCustomEntry':
                if(empty($args['id'])) return false;
                return ['POST' => "/bc-custom-content/custom_entries/delete/{$args['id']}.json"];
            default:
                return false;
        }
    }

    /**
     * カスタムエントリーを追加
     */
    public function addCustomEntry(
        int $customTableId,
        string $title,
        ?int $parentId = null,
        ?string $name = null,
        ?int $creatorId = null,
        ?bool $status = null,
        ?string $publishBegin = null,
        ?string $publishEnd = null,
        ?string $published = null,
        ?array $customFields = null,
        ?int $loginUserId = null
    ): array
    {
        return $this->executeWithErrorHandling(function() use (
            $customTableId, $title, $parentId, $name, $creatorId, $status,
            $publishBegin, $publishEnd, $published, $customFields, $loginUserId
        ) {
            /** @var CustomEntriesService $customEntriesService */
            $customEntriesService = $this->getService(CustomEntriesServiceInterface::class);
            // BcCcFileUtil::setupUploader 内で呼び出されるテーブルと確実に同一インスタンスとなるように改めてテーブルを設定
            $customEntriesService->CustomEntries = TableRegistry::getTableLocator()->get('BcCustomContent.CustomEntries');
            BcCcFileUtil::setupUploader($customTableId);
            $customEntriesService->setup($customTableId);

            $data = [
                'custom_table_id' => $customTableId,
                'title' => $title,
                'parentId' => $parentId ?? null,
                'name' => $name ?? '',
                'creator_id' => $creatorId ?? 1,
                'status' => $status ?? false,
                'publish_begin' => $publishBegin ?? null,
                'publish_end' => $publishEnd ?? null,
                'published' => $published ?? date('Y-m-d H:i:s'),
            ];

            // カスタムフィールドの値を追加（ファイルアップロード処理を含む）
            if (!empty($customFields)) {
                $processedFields = $this->processCustomFields($customFields, $customTableId);
                $data = array_merge($data, $processedFields);
            }

            $result = $customEntriesService->create($data);

            if ($result) {
                return $this->createSuccessResponse(
                    $result->toArray(),
                    [],
                    sprintf('カスタムエントリー「%s」を追加しました。', $result->title),
                    $loginUserId
                );
            } else {
                return $this->createErrorResponse('カスタムエントリーの保存に失敗しました');
            }
        });
    }

    /**
     * カスタムエントリーを編集
     */
    public function editCustomEntry(
        int $customTableId,
        int $id,
        ?string $title = null,
        ?int $parentId = null,
        ?string $name = null,
        ?int $creatorId = null,
        ?bool $status = null,
        ?string $publishBegin = null,
        ?string $publishEnd = null,
        ?string $published = null,
        ?array $customFields = null,
        ?int $loginUserId = null
    ): array
    {
        return $this->executeWithErrorHandling(function() use (
            $id, $customTableId, $title, $parentId, $name, $creatorId, $status,
            $publishBegin, $publishEnd, $published, $customFields, $loginUserId
        ) {
            /** @var CustomEntriesService $customEntriesService */
            $customEntriesService = $this->getService(CustomEntriesServiceInterface::class);
            // BcCcFileUtil::setupUploader 内で呼び出されるテーブルと確実に同一インスタンスとなるように改めてテーブルを設定
            $customEntriesService->CustomEntries = TableRegistry::getTableLocator()->get('BcCustomContent.CustomEntries');
            BcCcFileUtil::setupUploader($customTableId);
            $customEntriesService->setup($customTableId);

            $entity = $customEntriesService->get($id);

            if (!$entity) return $this->createErrorResponse('指定されたIDのカスタムエントリーが見つかりません');

            $data = [];
            if ($title !== null) $data['title'] = $title;
            if ($parentId !== null) $data['parent_id'] = $parentId;
            if ($name !== null) $data['name'] = $name;
            if ($creatorId !== null) $data['creator_id'] = $creatorId;
            if ($status !== null) $data['status'] = $status;
            if ($publishBegin !== null) $data['publish_begin'] = $publishBegin;
            if ($publishEnd !== null) $data['publish_end'] = $publishEnd;
            if ($published !== null) $data['published'] = $published;

            // カスタムフィールドの値を追加（ファイルアップロード処理を含む）
            if (!empty($customFields)) {
                $processedFields = $this->processCustomFields($customFields, $customTableId);
                $data = array_merge($data, $processedFields);
            }

            $result = $customEntriesService->update($entity, $data);

            if ($result) {
                return $this->createSuccessResponse(
                    $result->toArray(),
                    [],
                    sprintf('カスタムエントリー「%s」を編集しました。', $result->title),
                    $loginUserId
                );
            } else {
                return $this->createErrorResponse('カスタムエントリーの更新に失敗しました');
            }
        });
    }

    /**
     * カスタムフィールドの値を処理（ファイルアップロードを含む）
     *
     * @param array $customFields
     * @param int $customTableId カスタムテーブルID
     * @return array
     */
    protected function processCustomFields(array $customFields, int $customTableId): array
    {
        $processedFields = [];

        foreach($customFields as $fieldName => $value) {
            if (is_array($value)) {
                // 配列の場合、json形式またはファイルアップロードの可能性をチェック
                $processedFields[$fieldName] = $value;
            } elseif ($this->isFileUpload($value, $customTableId, $fieldName)) {
                // ファイルアップロードデータの処理（フィールドタイプもチェック）
                $uploadResult = $this->processFileUpload($value);
                if ($uploadResult !== false) {
                    // 戻り値が配列の場合はUploadedFileオブジェクトに変換、文字列の場合はそのまま
                    if (is_array($uploadResult)) {
                        $processedFields[$fieldName] = $this->createUploadedFileFromArray($uploadResult);
                    } else {
                        $processedFields[$fieldName] = $uploadResult;
                    }
                } else {
                    throw new InvalidArgumentException("ファイルアップロードに失敗しました ({$fieldName})");
                }
            } else {
                // 通常の値
                $processedFields[$fieldName] = $value;
            }
        }

        return $processedFields;
    }

    /**
     * カスタムフィールドのタイプを取得
     *
     * @param int $customTableId カスタムテーブルID
     * @param string $fieldName フィールド名
     * @return string|null フィールドタイプ（BcCcFileなど）、見つからない場合はnull
     */
    protected function getCustomFieldType(int $customTableId, string $fieldName): ?string
    {
        try {
            $customLinksTable = \Cake\ORM\TableRegistry::getTableLocator()->get('BcCustomContent.CustomLinks');

            $customLink = $customLinksTable->find()
                ->contain(['CustomFields'])
                ->where([
                    'CustomLinks.custom_table_id' => $customTableId,
                    'CustomLinks.name' => $fieldName
                ])
                ->first();

            if ($customLink && $customLink->custom_field) {
                return $customLink->custom_field->type;
            }

            return null;
        } catch (\Exception $e) {
            // エラーログを出力
            error_log('カスタムフィールドタイプの取得に失敗: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * カスタムフィールドがファイルアップロードフィールドかどうかを判定
     *
     * @param int $customTableId カスタムテーブルID
     * @param string $fieldName フィールド名
     * @return bool BcCcFileフィールドの場合true
     */
    protected function isFileUploadField(int $customTableId, string $fieldName): bool
    {
        $fieldType = $this->getCustomFieldType($customTableId, $fieldName);
        return $fieldType === 'BcCcFile';
    }

    /**
     * ファイルアップロードデータかどうかを判定（カスタムエントリー用）
     *
     * @param mixed $value 判定対象の値
     * @param int $customTableId カスタムテーブルID
     * @param string $fieldName フィールド名
     * @return bool ファイルアップロードデータの場合true
     */
    protected function isFileUpload($value, int $customTableId, string $fieldName): bool
    {
        // フィールドタイプがBcCcFileでない場合は対象外
        if (!$this->isFileUploadField($customTableId, $fieldName)) {
            return false;
        }

        // 値の形式チェック
        return $this->isFileUploadable($value);
    }

    /**
     * カスタムエントリー一覧を取得
     */
    public function getCustomEntries(
        int $customTableId,
        ?string $title = null,
        ?int $creatorId = null,
        ?string $published = null,
        ?int $limit = 20,
        ?int $page = 1,
        ?string $status = null
    ): array
    {
        return $this->executeWithErrorHandling(function() use ($customTableId, $title, $creatorId, $published, $limit, $page, $status) {
            /** @var CustomEntriesService $customEntriesService */
            $customEntriesService = $this->getService(CustomEntriesServiceInterface::class);
            $customEntriesService->setup($customTableId);
            $conditions = [
                'limit' => $limit ?? 20,
                'page' => $page ?? 1
            ];
            if (isset($status)) $conditions['status'] = $status;
            if (!is_null($title)) $conditions['title'] = $title;
            if (!is_null($creatorId)) $conditions['creator_id'] = $creatorId;
            if (!is_null($published)) $conditions['published'] = $published;

            $results = $customEntriesService->getIndex($conditions)->toArray();

            return $this->createSuccessResponse([
                'results' => $results,
                'pagination' => [
                    'page' => $conditions['page'],
                    'limit' => $conditions['limit'],
                    'count' => count($results)
                ]
            ]);
        });
    }

    /**
     * カスタムエントリーを取得
     */
    public function getCustomEntry(int $customTableId, int $id): array
    {
        return $this->executeWithErrorHandling(function() use ($customTableId, $id) {
            /** @var CustomEntriesService $customEntriesService */
            $customEntriesService = $this->getService(CustomEntriesServiceInterface::class);
            $customEntriesService->setup($customTableId);
            $result = $customEntriesService->get($id);

            if ($result) {
                return $this->createSuccessResponse($result->toArray());
            } else {
                return $this->createErrorResponse('指定されたIDのカスタムエントリーが見つかりません');
            }
        });
    }

    /**
     * カスタムエントリーを削除
     */
    public function deleteCustomEntry(int $customTableId, int $id, ?int $loginUserId = null): array
    {
        return $this->executeWithErrorHandling(function() use ($customTableId, $id, $loginUserId) {
            /** @var CustomEntriesService $customEntriesService */
            $customEntriesService = $this->getService(CustomEntriesServiceInterface::class);
            $customEntriesService->setup($customTableId);

            // 削除前にタイトルを取得
            $entity = $customEntriesService->get($id);
            if (!$entity) {
                return $this->createErrorResponse('指定されたIDのカスタムエントリーが見つかりません');
            }

            $title = $entity->title;
            $result = $customEntriesService->delete($id);

            if ($result) {
                return $this->createSuccessResponse(
                    'カスタムエントリーを削除しました',
                    [],
                    sprintf('カスタムエントリー「%s」を削除しました。', $title),
                    $loginUserId
                );
            } else {
                return $this->createErrorResponse('カスタムエントリーの削除に失敗しました');
            }
        });
    }
}
