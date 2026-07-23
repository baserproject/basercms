---
name: basercms5-custom-content-development
description: 'baserCMS 5系の標準プラグイン bc-custom-content（カスタムコンテンツ）でコンテンツ種別を新規構築・改修する際の正本パターン集。「custom_table/custom_fields/custom_links/custom_contents/custom_entries の関係」「CustomTablesService/CustomFieldsService/CustomLinksService/CustomContentsService/CustomEntriesService の使い方」「custom_fields.name はテーブル横断でグローバルユニーク」「フィールド名は ^[a-z0-9_]+$ のみ（camelCase不可）」「BcCcText/BcCcTextarea/BcCcSelect/BcCcRadio/BcCcDate/BcCcPref 等フィールドタイプ別のエスケープ挙動」「display_front の設定漏れ」「custom_entries.name が NULL だと詳細ページが404になる」「フロントテンプレート(templates/CustomContent/<template>/{index,view,archives,year}.php)のビュー変数」「bc-custom-contentは既存blog_postsへの後付けフィールド追加には使えない」で参照する。プラグイン開発の共通ルールは basercms5-plugin-development を参照。'
license: MIT
---

# baserCMS5 bc-custom-content 開発の正本パターン集

baserCMS 5系の標準プラグイン **bc-custom-content**（カスタムコンテンツ）を使って新しいコンテンツ種別を作る・改修するときのコーディングパターン集。

環境・命名規則などの共通ルールは **basercms5-development**、プラグイン開発一般は **basercms5-plugin-development** を参照。本スキルは bc-custom-content の**開発知識そのもの**に特化する（特定の4系プラグインからの移行固有の手順は、本スキルの対象外。移行を行う場合は、本スキルを前提知識として参照する移行専用スキルを別途用意すること）。

---

## 0. 大前提: bc-custom-content は「独立したコンテンツツリー配下のコンテンツ種別」機構であり、既存モデルへの後付けフィールド追加には使えない

`custom_tables`/`custom_contents`/`custom_fields`/`custom_links`/`custom_entries` という完全に独立したデータモデルを持つ。実データは動的生成テーブル `custom_entry_<table_id>_<name>` に保存される。**`blog_posts` 等の既存テーブルと連携するコードは存在しない**（grep で確認可能）。

「既存のブログ記事にフィールドを後付けしたい」という要件であれば、bc-custom-content では実現できない。対象を、ブログ記事から独立した bc-custom-content のコンテンツ種別として作り直すか、コアの `BcModelEventDispatcher` が全 Table に対し CakePHP 標準イベントを `Model.<ModelName>.<eventName>` 形式で自動発火する仕組みを使い `Model.BlogPosts.beforeSave`/`afterSave` 等を購読する独自実装で対応する（後者の場合は本スキルではなく basercms5-plugin-development の8章「イベント」を参照）。

---

## 1. データモデルと Service API

| テーブル | 役割 |
|---|---|
| `custom_tables` | コンテンツ種別の定義（`type=1` がコンテンツツリー紐付け型）。作成時に動的テーブル `custom_entry_<id>_<name>` が自動生成される |
| `custom_contents` | `custom_table` をコンテンツツリー（`contents`）に紐付け。`site_id` は `custom_contents` 自体でなく関連する `Contents` エンティティ側に持つ（`BcContentsBehavior` 経由） |
| `custom_fields` | フィールド定義（name/title/type/source 等）。**再利用可能で、custom_table に属さない** |
| `custom_links` | `custom_table` × `custom_field` の中間テーブル。表示順（Tree ビヘイビア、`no`）・`display_front`（後述）を持つ |
| `custom_entries` | 雛形のみ。実データは動的テーブル `custom_entry_<id>_<name>` に保存される |

Service は `BaserCore\Utility\BcContainerTrait` を使い `$this->getService(XxxServiceInterface::class)` でDIコンテナから取得する（Table を直接 `newEntity`+`saveOrFail` しない。各 Service は動的テーブル作成・カラム追加等の副作用をトランザクション付きで内包しているため）。

```php
// custom_table 作成（type=1 でコンテンツツリー紐付け型、display_field は一覧表示用フィールド名）
$customTable = $customTablesService->create([
    'name' => 'works', 'title' => '制作実績', 'type' => '1', 'display_field' => 'title',
]);
// フィールド定義作成（再利用可能）
$customField = $customFieldsService->create(['name' => 'work_url', 'title' => 'サイトURL', 'type' => 'BcCcText']);
// table と field を紐付け（表示順 no、display_front は後述）
$customLinksService->create([
    'custom_table_id' => $customTable->id, 'custom_field_id' => $customField->id,
    'name' => 'work_url', 'title' => 'サイトURL', 'type' => 'BcCcText', 'no' => 1,
    'display_front' => true,
]);
// コンテンツツリーへの紐付け（content キーで Contents を同時保存、site_id は content 側）
$customContentsService->create([
    'custom_table_id' => $customTable->id, 'template' => 'works', 'list_count' => 10,
    'content' => ['title' => '制作実績', 'site_id' => 1, 'parent_id' => $parentContentId],
]);
```

## 2. フィールド名は `^[a-z0-9_]+$` のみ（camelCase 不可）

`CustomFieldsTable`/`CustomLinksTable` の `validationDefault()` が `name` 列に `regex('name', '/^[a-z0-9_]+$/')` を課す。フィールド名は必ず snake_case で設計する（大文字・camelCaseは保存時に例外になる）。

## 3. `custom_fields.name` はテーブル横断（custom_table 横断）でグローバルにユニーク

`CustomFieldsTable` の `name` 列には `validateUnique` が付与されており、**custom_table ごとではなくシステム全体で一意**でなければならない。複数のコンテンツ種別で同名フィールド（例: 本文用 `content`）を共有したい場合、単純に毎回 `create()` すると2件目以降で `name.validateUnique` エラーになる。

**対策**: フィールド作成前に同名の既存 `CustomField` を検索し、あれば新規作成せず再利用する（`CustomLinks` のみ新規作成）。ただし **`type` の一致を必ず検証**し、不一致なら例外を投げて止める（型が食い違ったまま静かに保存されるのを防ぐ）:

```php
$customField = $customFieldsTable->find()->where(['name' => $field['name']])->first();
if (!$customField) {
    $customField = $customFieldsService->create([...]);
} elseif ($customField->type !== $field['type']) {
    throw new \RuntimeException(
        "カスタムフィールド「{$field['name']}」のtypeが既存のものと一致しません。"
        . "既存: {$customField->type} / 新規: {$field['type']}"
    );
}
```

## 4. `custom_links.display_front` を明示的に `true` にすること

`CustomContentHelper::getFieldValue()` は `if (empty($customLink->display_front)) return '';` を通るため、**`display_front` が未設定（NULL/false）だと、値が実際にDBに入っていてもフロント側では常に空文字が返る**。`CustomLinksService::create()` を呼ぶ際は必ず `'display_front' => true` を渡す。既存データで設定漏れがある場合は次のSQLで一括修正できる:

```sql
UPDATE custom_links SET display_front=1 WHERE display_front IS NULL OR display_front=0;
```

## 5. IDベースのURL運用にする場合は `custom_entries.name` を明示的に空文字にする（NULLのままだと詳細ページが404になる）

`CustomEntriesService::get($id, $options)` は、`$id` が数値かつ `$options['status'] === 'publish'`（公開状態のフロント表示時）の場合、`CustomEntries.name = ''` を**必須条件として追加**する:

```php
if (is_numeric($id)) {
    $conditions['CustomEntries.id'] = $id;
    if ($options['status'] === 'publish') {
        $conditions['CustomEntries.name'] = '';
    }
} else {
    $conditions['CustomEntries.name'] = rawurldecode($id); // 非数値ならスラッグ扱い
}
```

SQL上 `NULL = ''` は常に偽なので、**エントリ作成時に `name` を明示的に空文字 `''` で保存しないと、公開状態の詳細ページが数値IDアクセスで常に404になる**（一覧・管理画面は正常に見えるため気づきにくい）。IDベースの詳細URL運用にする場合は、エントリ保存時のデータに必ず `'name' => ''` を含める。逆にスラッグ運用にしたい場合は `name` に実際のスラッグ文字列を入れればそのまま使える（`custom_entries.name` がスラッグ機構そのもの）。

## 6. フィールドタイプ別のエスケープ・改行変換はヘルパー側で完結している（テンプレートで二重処理しない）

`CustomContentHelper::getFieldValue($entry, $fieldName)` は、フィールドタイプの `BcCc*Helper::get()` に委譲される。**多くの型で既にエスケープ・改行変換済みの文字列が返る**ため、テンプレート側でさらに `h()`/`nl2br()` を掛けると二重処理になり、`&lt;br /&gt;` のような文字列が画面に出る不具合になる。

| フィールドタイプ | `get()` の挙動 | テンプレートでの扱い |
|---|---|---|
| `BcCcText` | `h($value)` | そのまま echo（`h()` 不要） |
| `BcCcTextarea` | `nl2br(h($value))` | そのまま echo（`h()`/`nl2br()` 両方不要） |
| `BcCcPref`（都道府県コード） | `h($value)`（コードのまま） | `BaserCore.BcText::pref()` で名称変換 → 変換後の値もそのまま echo（`pref()` の戻り値に対して重ねて `h()` しない） |
| `BcCcRadio` | **エスケープなしの生値** | テンプレート側で `h()` が必要（唯一の例外） |
| `BcCcSelect` | インデックス値→ラベルへの解決込み | 基本そのまま echo |

**必ずテンプレート実装前に、対象フィールドタイプの実ヘルパー（`vendor/baserproject/bc-custom-content/plugins/BcCc*/src/View/Helper/*.php`）の `get()` 実装を読んで、エスケープ済みかどうかを確認する**。型ごとに挙動が違うため、一律の `h()`/`nl2br()` ラップは避ける。

## 7. Select/Radio 系フィールドの値はインデックス格納（ラベル文字列ではない）

`BcCcSelect`/`BcCcRadio` の値は、フォーム送信・DB保存時は選択肢リスト内の**インデックス番号（0始まり）**であり、ラベル文字列そのものではない。表示時に `custom_fields.source`（改行区切りの選択肢テキスト）を配列化し、インデックスで引いてラベルに解決する（`CustomContentArrayTrait::textToArray()` → `arrayValue()`）。`source` は改行区切りテキストで、`ラベル:内部値` の形式（例: `正社員:FULL_TIME`）にも対応している（`textToArray()` がキー:値形式もパース可能）。

## 8. フロントテンプレートのビュー変数と正しいAPI（設計ドキュメントの疑似コードを鵜呑みにしない）

`bc-custom-content` の URL は固定プレフィックスではなく `Contents.url`（コンテンツツリー配置）に従う。テンプレート探索パスは `templates/CustomContent/<custom_contents.template の値>/{index,view,archives,year}.php`。

`CustomContentController`/`CustomContentFrontService`（`vendor/baserproject/bc-custom-content/src/Controller/CustomContentController.php`・`src/Service/Front/CustomContentFrontService.php`）が実際に渡すビュー変数:

- `index.php`/`archives.php`/`year.php`: `$customContent`（CustomContentエンティティ）・`$customEntries`（`PaginatedInterface`）。archives/year のみ `$archivesName`（絞り込み値）。
- `view.php`: `$customContent`・`$customEntry`（単数）。

詳細URLの生成は `CustomContentHelper::getEntryUrl($entry)` を使う（`Url->build(['controller'=>'CustomContent',...])` ではない）。ページネーションは `$this->BcBaser->pagination('simple')`（`element/paginations/simple.php` を要求、テーマに既存のはず）。**「〜のように書く」という設計ドキュメント上の疑似コードは簡略化されていることがあるため、実装前に実際の Controller/Service のソースを読んで正しい変数名・メソッドを確認すること**。

## 9. CakePHP5 ミドルウェアは `MiddlewareInterface::process()` の実装が必須

独自ミドルウェアを追加する場合、`__invoke(Request, Handler): Response` 形式のクロージャ的な書き方では CakePHP5 の `MiddlewareQueue::add()` が要求する型（`MiddlewareInterface|Closure|array|string`）を満たさず `TypeError` になる。**`Psr\Http\Server\MiddlewareInterface` を implements し、`process(Request $request, RequestHandler $handler): ResponseInterface` メソッドとして実装する**こと。

```php
class XxxMiddleware implements \Psr\Http\Server\MiddlewareInterface
{
    public function process(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Server\RequestHandlerInterface $handler): \Psr\Http\Message\ResponseInterface
    {
        // ... 独自処理、最後は $handler->handle($request) にフォールバックさせる
    }
}
```
登録は `src/Application.php` の `middleware()` メソッド内で行う（他ミドルウェアとの順序に注意。URLリダイレクト等ルーティング解決前に処理したいものは `RoutingMiddleware` より前に追加する）。

## 10. 標準搭載フィールドタイプ一覧（追加開発不要な範囲の把握）

`vendor/baserproject/bc-custom-content/plugins/` 配下に標準搭載: `BcCcText`/`BcCcTextarea`/`BcCcWysiwyg`/`BcCcSelect`/`BcCcRadio`/`BcCcCheckbox`/`BcCcDate`/`BcCcDateTime`/`BcCcTel`/`BcCcEmail`/`BcCcPassword`/`BcCcHidden`/`BcCcFile`/`BcCcRelated`/`BcCcMultiple`/`BcCcAutoZip`/`BcCcPref`。必要な型がなければ、既存の `BcCc*` プラグインの構成（`config.php`・`config/setting.php`・`src/View/Helper/BcCcXxxHelper.php`・`templates/Admin/element/preview.php`）を手本に独自フィールドタイププラグインを開発する。外部提供のフィールドタイプ拡張プラグイン（リッチテキストエディタ系等）を追加導入するケースもあり、多くは Migrations を持たない最小構成で、他の `BcCc*` 同様 `plugins` テーブルへの直接INSERTで有効化できることが多い。
