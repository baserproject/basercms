<?php
declare(strict_types=1);

namespace BcMcp\Mcp\BcCustomContent;

use BcCustomContent\Service\CustomFieldsService;
use BcCustomContent\Service\CustomFieldsServiceInterface;
use PhpMcp\Server\ServerBuilder;
use BcMcp\Mcp\BaseMcpTool;

/**
 * カスタムフィールドツールクラス
 *
 * カスタムフィールドのCRUD操作を提供
 */
class CustomFieldsTool extends BaseMcpTool
{
    /**
     * タイプ
     */
    private const TYPES = [
        'BcCcAutoZip' => '郵便番号',
        'BcCcCheckbox' => 'チェックボックス',
        'BcCcDate' => '日付',
        'BcCcDateTime' => '日時',
        'BcCcEmail' => 'メールアドレス',
        'BcCcFile' => 'ファイルアップロード',
        'BcCcHidden' => '隠しフィールド',
        'BcCcMultiple' => '複数選択',
        'BcCcPassword' => 'パスワード',
        'BcCcPref' => '都道府県リスト',
        'BcCcRadio' => 'ラジオボタン',
        'BcCcRelated' => '関連データ',
        'BcCcSelect' => 'セレクトボックス',
        'BcCcTel' => '電話番号',
        'BcCcText' => '1行テキスト',
        'BcCcTextarea' => '複数行テキスト',
        'BcCcWysiwyg' => 'WYSIWYGエディタ',
        'CuCcBurgerEditor' => 'ブロックエディタ',
    ];

    private const VALIDATION_RULES = [
        'EMAIL' => 'Eメール形式チェック',
        'EMAIL_CONFIRM' => 'Eメール比較チェック、比較対象のフィールド名を、`meta` フィールドに配列として、キー `BcCustomContent` 配下に、キー `email_confirm` として指定',
        'NUMBER' => '数値チェック',
        'HANKAKU' => '半角英数チェック',
        'ZENKAKU_KATAKANA' => '全角カタカナチェック',
        'ZENKAKU_HIRAGANA' => '全角ひらがなチェック',
        'DATETIME' => '日付チェック',
        'MAX_FILE_SIZE' => 'ファイルアップロードサイズ制限、上限となる数値を単位MBで、`meta` フィールドに配列として、キー `BcCustomContent` 配下に、キー `max_file_size` として指定',
        'FILE_EXT' => 'ファイル拡張子チェック、アップロードを許可する拡張子をカンマ区切りで、`meta` フィールドに配列として、キー `BcCustomContent` 配下に、キー `file_ext` として指定',
    ];

    /**
     * カスタムフィールド関連のツールを ServerBuilder に追加
     */
    public function addToolsToBuilder(ServerBuilder $builder): ServerBuilder
    {
        $typeEnums = array_keys(self::TYPES);
        $validationRuleEnums = array_keys(self::VALIDATION_RULES);
        $typeDescriptions = implode('、', array_map(fn($key) => "{$key}（" . self::TYPES[$key] . "）", $typeEnums));
        $validationRuleDescriptions = implode('、', array_map(fn($key) => "{$key}（" . self::VALIDATION_RULES[$key] . "）", $validationRuleEnums));
        return $builder
            ->withTool(
                handler: [self::class, 'addCustomField'],
                name: 'addCustomField',
                description: 'カスタムエントリーの入力欄を定義する、カスタムフィールドを追加します。',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'name' => ['type' => 'string', 'description' => 'フィールド名（必須）'],
                        'title' => ['type' => 'string', 'description' => 'フィールドタイトル（必須）'],
                        'type' => ['type' => 'string', 'enum' => $typeEnums, 'description' => 'フィールドタイプ（必須）：' . $typeDescriptions],
                        'status' => ['type' => 'number', 'description' => 'ステータス（0: 無効, 1: 有効）（初期値は1）'],
                        'defaultValue' => ['type' => 'string', 'description' => 'カスタムエントリーの入力欄の初期値'],
                        'validate' => ['type' => 'string', 'enum' => $validationRuleEnums, 'description' => 'バリデーションルール（配列で複数選択可）：' . $validationRuleDescriptions],
                        'regex' => ['type' => 'string', 'description' => '正規表現バリデーション（正規表現でバリデーションを実行したい場合に指定する）'],
                        'regexErrorMessage' => ['type' => 'string', 'description' => '正規表現エラーメッセージ（`regex` を指定した場合に、正規表現にマッチしなかった場合に表示するエラーメッセージを指定する）'],
                        'counter' => ['type' => 'boolean', 'description' => '文字数カウンター（`true` を指定した場合、入力欄の下に文字数カウンターを表示する、1行テキスト、複数行テキストで利用可能）'],
                        'autoConvert' => ['type' => 'string', 'enum' => ['CONVERT_HANKAKU（半角変換）', 'CONVERT_ZENKAKU（全角変換）'], 'description' => '自動変換（入力値を自動で変換する）'],
                        'placeholder' => ['type' => 'string', 'description' => 'プレースホルダー（入力欄に薄く表示されるヒントテキスト）'],
                        'size' => ['type' => 'number', 'description' => '横幅サイズ（1行テキスト、複数行テキスト、パスワード、メールアドレス、電話番号、郵便番号で利用可能）'],
                        'line' => ['type' => 'number', 'description' => '行数（複数行テキストで利用可能）'],
                        'maxLength' => ['type' => 'number', 'description' => '最大文字数（1行テキスト、複数行テキスト、パスワード、メールアドレス、電話番号で利用可能）'],
                        'source' => ['type' => 'string', 'description' => '選択肢（ラジオボタンやセレクトボックスの場合、改行で区切って指定する）'],
                        'meta' => ['type' => 'string', 'description' => 'メタ情報（多次元配列形式で追加情報を指定する、バリデーションルールの詳細設定や、WYSIWYGエディタの幅指定などに利用、WYSIWYG幅:[BcCcWysiwyg][width] / WYSIWYG高さ:[BcCcWysiwyg][height] / WYSIWYGツールタイプ（simple / normal）:[BcCcWysiwyg][editor_tool_type]）']
                    ],
                    'required' => ['name', 'title', 'type']
                ]
            )
            ->withTool(
                handler: [self::class, 'editCustomField'],
                name: 'editCustomField',
                description: 'カスタムエントリーの入力欄を定義する、カスタムフィールドを編集します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => 'カスタムフィールドID（必須）'],
                        'name' => ['type' => 'string', 'description' => 'フィールド名'],
                        'title' => ['type' => 'string', 'description' => 'フィールドタイトル'],
                        'type' => ['type' => 'string', 'enum' => $typeEnums, 'description' => 'フィールドタイプ：' . $typeDescriptions],
                        'status' => ['type' => 'number', 'description' => 'ステータス（0: 無効, 1: 有効）（初期値は1）'],
                        'defaultValue' => ['type' => 'string', 'description' => 'カスタムエントリーの入力欄の初期値'],
                        'validate' => ['type' => 'string', 'enum' => $validationRuleEnums, 'description' => 'バリデーションルール（配列で複数選択可）：' . $validationRuleDescriptions],
                        'regex' => ['type' => 'string', 'description' => '正規表現バリデーション（正規表現でバリデーションを実行したい場合に指定する）'],
                        'regexErrorMessage' => ['type' => 'string', 'description' => '正規表現エラーメッセージ（`regex` を指定した場合に、正規表現にマッチしなかった場合に表示するエラーメッセージを指定する）'],
                        'counter' => ['type' => 'boolean', 'description' => '文字数カウンター（`true` を指定した場合、入力欄の下に文字数カウンターを表示する、1行テキスト、複数行テキストで利用可能）'],
                        'autoConvert' => ['type' => 'string', 'enum' => ['CONVERT_HANKAKU（半角変換）', 'CONVERT_ZENKAKU（全角変換）'], 'description' => '自動変換（入力値を自動で変換する）'],
                        'placeholder' => ['type' => 'string', 'description' => 'プレースホルダー（入力欄に薄く表示されるヒントテキスト）'],
                        'size' => ['type' => 'number', 'description' => '横幅サイズ（1行テキスト、複数行テキスト、パスワード、メールアドレス、電話番号、郵便番号で利用可能）'],
                        'line' => ['type' => 'number', 'description' => '行数（複数行テキストで利用可能）'],
                        'maxLength' => ['type' => 'number', 'description' => '最大文字数（1行テキスト、複数行テキスト、パスワード、メールアドレス、電話番号で利用可能）'],
                        'source' => ['type' => 'string', 'description' => '選択肢（ラジオボタンやセレクトボックスの場合、改行で区切って指定する）'],
                        'meta' => ['type' => 'string', 'description' => 'メタ情報（多次元配列形式で追加情報を指定する、バリデーションルールの詳細設定や、WYSIWYGエディタの幅指定などに利用、WYSIWYG幅:[BcCcWysiwyg][width] / WYSIWYG高さ:[BcCcWysiwyg][height] / WYSIWYGツールタイプ（simple / normal）:[BcCcWysiwyg][editor_tool_type]）']
                    ],
                    'required' => ['id']
                ]
            )
            ->withTool(
                handler: [self::class, 'getCustomFields'],
                name: 'getCustomFields',
                description: 'カスタムエントリーの入力欄を定義する、カスタムフィールドの一覧を取得します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'name' => ['type' => 'string', 'description' => 'フィールド名での絞り込み'],
                        'title' => ['type' => 'string', 'description' => 'フィールドタイトルでの絞り込み（部分一致）'],
                        'type' => ['type' => 'string', 'description' => 'フィールドタイプでの絞り込み'],
                        'status' => ['type' => 'number', 'description' => 'ステータス（0: 無効, 1: 有効）']
                    ]
                ]
            )
            ->withTool(
                handler: [self::class, 'getCustomField'],
                name: 'getCustomField',
                description: 'カスタムエントリーの入力欄を定義する、カスタムフィールドをIDを指定して取得します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => 'カスタムフィールドID（必須）']
                    ],
                    'required' => ['id']
                ]
            )
            ->withTool(
                handler: [self::class, 'deleteCustomField'],
                name: 'deleteCustomField',
                description: 'カスタムエントリーの入力欄を定義する、カスタムフィールドをIDを指定して削除します',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => 'カスタムフィールドID（必須）']
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
            case 'addCustomField':
                return ['POST' => "/bc-custom-content/custom_fields/add.json"];
            case 'editCustomField':
                if(empty($args['id'])) return false;
                return ['POST' => "/bc-custom-content/custom_fields/edit/{$args['id']}.json"];
            case 'getCustomFields':
                return ['GET' => "/bc-custom-content/custom_fields/index.json"];
            case 'getCustomField':
                if(empty($args['id'])) return false;
                return ['GET' => "/bc-custom-content/custom_fields/view/{$args['id']}.json"];
            case 'deleteCustomField':
                if(empty($args['id'])) return false;
                return ['POST' => "/bc-custom-content/custom_fields/delete/{$args['id']}.json"];
            default:
                return false;
        }
    }

    /**
     * カスタムフィールドを追加
     */
    public function addCustomField(
        string $name,
        string $title,
        string $type,
        int $status = 1,
        ?string $defaultValue = null,
        ?array $validate = null,
        ?string $regex = null,
        ?string $regexErrorMessage = null,
        ?bool $counter = null,
        ?string $autoConvert = null,
        ?string $placeholder = null,
        ?int $size = null,
        ?int $line = null,
        ?int $maxLength = null,
        ?string $source = null,
        ?string $meta = null,
        ?int $loginUserId = null
    ): array
    {
        return $this->executeWithErrorHandling(function() use (
            $name, $title, $type, $status, $defaultValue, $validate, $regex, $regexErrorMessage,
            $counter, $autoConvert, $placeholder, $size, $line, $maxLength, $source, $meta, $loginUserId
        ) {
            /** @var CustomFieldsService $customFieldsService */
            $customFieldsService = $this->getService(CustomFieldsServiceInterface::class);

            $data = [
                'name' => $name,
                'title' => $title,
                'type' => $type,
                'source' => $source ?? null,
                'status' => $status,
                'default_value' => $defaultValue ?? null,
                'validate' => $validate ?? null,
                'regex' => $regex ?? null,
                'regex_error_message' => $regexErrorMessage ?? null,
                'counter' => $counter ?? null,
                'auto_convert' => $autoConvert ?? null,
                'placeholder' => $placeholder ?? null,
                'size' => $size ?? null,
                'line' => $line ?? null,
                'max_length' => $maxLength ?? null,
                'meta' => $meta ?? null
            ];

            $result = $customFieldsService->create($data);

            if ($result) {
                return $this->createSuccessResponse(
                    $result->toArray(),
                    [],
                    sprintf('カスタムフィールド「%s」を追加しました。', $result->title),
                    $loginUserId
                );
            } else {
                return $this->createErrorResponse('カスタムフィールドの保存に失敗しました');
            }
        });
    }

    /**
     * カスタムフィールド一覧を取得
     */
    public function getCustomFields(
        ?string $name = null,
        ?string $title = null,
        ?string $type = null,
        ?int $status = null
    ): array
    {
        return $this->executeWithErrorHandling(function() use ($name, $title, $type, $status) {
            /** @var CustomFieldsService $customFieldsService */
            $customFieldsService = $this->getService(CustomFieldsServiceInterface::class);

            $conditions = [];
            if (!empty($name)) $conditions['name'] = $name;
            if (!empty($title)) $conditions['title'] = $title;
            if (!empty($type)) $conditions['type'] = $type;
            if (isset($status)) $conditions['status'] = $status;

            $results = $customFieldsService->getIndex($conditions)->toArray();

            return $this->createSuccessResponse($results);
        });
    }

    /**
     * カスタムフィールドを取得
     */
    public function getCustomField(int $id): array
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            /** @var CustomFieldsService $customFieldsService */
            $customFieldsService = $this->getService(CustomFieldsServiceInterface::class);

            $result = $customFieldsService->get($id);

            if ($result) {
                return $this->createSuccessResponse($result->toArray());
            } else {
                return $this->createErrorResponse('指定されたIDのカスタムフィールドが見つかりません');
            }
        });
    }

    /**
     * カスタムフィールドを編集
     */
    public function editCustomField(
        int $id,
        ?string $name = null,
        ?string $title = null,
        ?string $type = null,
        ?int $status = null,
        ?string $defaultValue = null,
        ?array $validate = null,
        ?string $regex = null,
        ?string $regexErrorMessage = null,
        ?bool $counter = null,
        ?string $autoConvert = null,
        ?string $placeholder = null,
        ?int $size = null,
        ?int $line = null,
        ?int $maxLength = null,
        ?string $source = null,
        ?string $meta = null,
        ?int $loginUserId = null
    ): array
    {
        return $this->executeWithErrorHandling(function() use (
            $id, $name, $title, $type, $status, $defaultValue, $validate, $regex, $regexErrorMessage,
            $counter, $autoConvert, $placeholder, $size, $line, $maxLength, $source, $meta, $loginUserId
        ) {
            $customFieldsService = $this->getService(CustomFieldsServiceInterface::class);

            $entity = $customFieldsService->get($id);

            if (!$entity) {
                return $this->createErrorResponse('指定されたIDのカスタムフィールドが見つかりません');
            }

            $data = [];
            if ($name !== null) $data['name'] = $name;
            if ($title !== null) $data['title'] = $title;
            if ($type !== null) $data['type'] = $type;
            if ($status !== null) $data['status'] = $status;
            if ($defaultValue !== null) $data['default_value'] = $defaultValue;
            if ($validate !== null) $data['validate'] = $validate;
            if ($regex !== null) $data['regex'] = $regex;
            if ($regexErrorMessage !== null) $data['regex_error_message'] = $regexErrorMessage;
            if ($counter !== null) $data['counter'] = $counter;
            if ($autoConvert !== null) $data['auto_convert'] = $autoConvert;
            if ($placeholder !== null) $data['placeholder'] = $placeholder;
            if ($size !== null) $data['size'] = $size;
            if ($line !== null) $data['line'] = $line;
            if ($maxLength !== null) $data['max_length'] = $maxLength;
            if ($source !== null) $data['source'] = $source;
            if ($meta !== null) $data['meta'] = $meta;

            $result = $customFieldsService->update($entity, $data);

            if ($result) {
                return $this->createSuccessResponse(
                    $result->toArray(),
                    [],
                    sprintf('カスタムフィールド「%s」を編集しました。', $result->title),
                    $loginUserId
                );
            } else {
                return $this->createErrorResponse('カスタムフィールドの更新に失敗しました');
            }
        });
    }

    /**
     * カスタムフィールドを削除
     */
    public function deleteCustomField(int $id, ?int $loginUserId = null): array
    {
        return $this->executeWithErrorHandling(function() use ($id, $loginUserId) {
            /** @var CustomFieldsService $customFieldsService */
            $customFieldsService = $this->getService(CustomFieldsServiceInterface::class);

            // 削除前にタイトルを取得
            $entity = $customFieldsService->get($id);
            if (!$entity) {
                return $this->createErrorResponse('指定されたIDのカスタムフィールドが見つかりません');
            }

            $title = $entity->title;
            $result = $customFieldsService->delete($id);

            if ($result) {
                return $this->createSuccessResponse(
                    'カスタムフィールドを削除しました',
                    [],
                    sprintf('カスタムフィールド「%s」を削除しました。', $title),
                    $loginUserId
                );
            } else {
                return $this->createErrorResponse('カスタムフィールドの削除に失敗しました');
            }
        });
    }
}
