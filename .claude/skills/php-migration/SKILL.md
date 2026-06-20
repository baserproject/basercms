---
name: php-migration
description: baserCMS の PHP バージョンアップ（8.2/8.4/8.5 ～）対応の非推奨・破壊的変更パターン集と修正レシピ。「暗黙的nullableの非推奨警告」「Creation of dynamic property ... is deprecated」「fgetcsv の escape 警告」「ReflectionProperty::setValue の非推奨」「(integer)/(boolean) 等の非正規キャスト非推奨」「null を配列オフセットに使う非推奨」「curl_close/imagedestroy 等の自動解放関数非推奨」等、PHP本体起因のアップグレード警告/エラーの調査・修正時に参照する。新しい PHP バージョン対応時は本書にバージョン別追記する。CakePHP本体起因の問題は cakephp-migration スキルを参照。
---

# PHP バージョン移行ガイド（baserCMS）

PHP のバージョンアップで遭遇した非推奨・破壊的変更と修正レシピ集。**バージョン別**に整理して育てる。CakePHP フレームワーク本体起因の問題は別スキル `cakephp-migration` を参照。

> 収録: PHP 8.2 / 8.4 / 8.5。新バージョン対応時は新しい「## PHP X.Y」節を追記する。
> 注意: PHP 8.5 は 9.0 の直前バージョン。**9.0 で非推奨機能はすべて削除される**ため、8.5 で出る非推奨警告は早めに潰しておく。

## 大原則

- **「致命的エラー」と「非推奨警告」を区別する**。デバッグモードでは非推奨警告が画面下部に表示され「エラー」に見えるが、`logs/debug.log` に `debug:` で出るものは警告（動作は継続）。Fatal/Exception が本当のエラー。
- **regression と既存failを切り分ける**。`git diff HEAD -- <file>` で当該ファイルが自分の変更対象か確認。
- 親クラスのシグネチャを変えたら、**子クラス・インターフェースも揃える**。
- 全非推奨の網羅検出には Rector 等の静的解析が有効。

> ユニットテストの実行方法・構文チェック・失敗の切り分けは `basercms-unittest` スキルを参照。`php -l`（構文チェック）では暗黙nullable等の非推奨警告も併せて出る。

---

## PHP 8.2

### 動的プロパティの非推奨
未宣言プロパティへの代入が非推奨。`Creation of dynamic property X::$prop is deprecated`。

- **対策A（推奨）**: クラスにプロパティを宣言する。
  ```php
  /** @var ThemeFoldersServiceInterface */
  protected $ThemeFoldersService;
  protected $_tempalteTypes = [];
  ```
  ※ コンストラクタで `$this->Xxx = $this->getService(...)` のように代入している箇所が典型。
- **対策B**: 多数の子クラスに波及する基底クラス（例: テスト基底 `BcTestCase` は400超のサブクラスを持つ）には `#[\AllowDynamicProperties]` を付与。**属性は子クラスに継承される**ため1箇所で済む。
  ```php
  #[\AllowDynamicProperties]
  class BcTestCase extends TestCase { ... }
  ```

---

## PHP 8.4

### 1. 暗黙的 nullable 引数の非推奨
`型 $x = null` は暗黙 nullable で非推奨。明示的に `?型` にする。
```php
// Before
function foo(int $contentId = null, BcAbstractDetector $agent = null)
// After
function foo(?int $contentId = null, ?BcAbstractDetector $agent = null)
```
- 検出（正規表現）: 先頭に `?` が付かず `型 $name = null` になっている引数にマッチ。
  例: `([A-Za-z_\\][A-Za-z0-9_\\]*)\s+(\$[A-Za-z0-9_]+)\s*=\s*null`（直前に `?` が無いもの）。
- 親クラスのシグネチャ変更時は子クラス/インターフェースも揃える。
- 警告: `Implicitly marking parameter $x as nullable is deprecated, the explicit nullable type must be used instead`。

### 2. `fgetcsv()` の `$escape` 非推奨
`$escape` 引数の省略が非推奨。明示する。
```php
// Before
$head = fgetcsv($fp, 10240);
// After
$head = fgetcsv($fp, 10240, ',', '"', '\\');
```
- 独自実装（`fgets`+正規表現でCSVをパースする関数等）は `fgetcsv` を使っていないので対象外。実際に `fgetcsv()` を呼んでいる箇所のみ修正する。

### 3. `ReflectionProperty::setValue()` の単一引数呼び出し非推奨
静的でないプロパティは対象オブジェクトを渡す。
```php
// Before
$property->setValue($value);
// After
$property->setValue($object, $value);
```
- 警告: `Calling ReflectionProperty::setValue() with a single argument is deprecated`。
- テスト用ユーティリティ（リクエスト生成やプライベートプロパティ操作のヘルパ）に潜みやすい。
- **静的プロパティの場合**は第1引数に `null` を渡す: `setValue(null, $value)`。これは **8.1〜8.5 すべてで有効**（8.1 互換を壊さない）。

### 4. 内部関数の `string` 引数に `null` を渡すのが非推奨（実体は 8.1+）
`parse_url(null)` / `version_compare($a, null)` / `str_replace(..., null)` 等、`?string` でない引数に `null` を渡すと `Passing null to parameter #N ($x) of type string is deprecated`。**`(string)` キャスト**で明示変換する。
```php
// Before → After
parse_url($url);                       →  parse_url((string)$url);
version_compare($current, $required);  →  version_compare((string)$current, (string)$required);
```
- 8.1 で導入された非推奨だが、移行作業（特に 8.4/8.5 への更新）で大量に顕在化するため本書にも収録。
- 実行時に値が null のときだけ出るため**静的 grep では拾い切れない**。全テストログの `Passing null to parameter` を集計して発生元を特定する。

---

## PHP 8.5（2025-11-20 リリース）

9.0 直前の最終マイナー。下記の大半は警告で動作は継続するが、9.0 で削除されるため対応する。アプリ/フレームワークに影響しやすい順に記載。**※「0.」のみ警告ではなく致命的エラー（破壊的変更）なので最優先。**

> ⚠️ **PHP 8.1 互換を維持する間の注意**（`composer.json` は `"php": ">=8.1"`）。
> 8.5 の非推奨を「推奨どおりの置き換え先」で直すと、その置き換え先が 8.1 に存在せず **8.1 が Fatal で動かなくなる**ものがある。下記の ❌ 印は **8.1 を切るまで据え置き**（8.5 で警告は出るが許容）にする。8.5 で動かすこと自体は 8.1 を壊さない（警告のみ）。
> - ❌ `str_increment()`（**8.3+**）→ 8.1 では `$s++` のまま
> - ❌ `http_get_last_response_headers()`（**8.4+**）→ 8.1 では `$http_response_header` のまま
> - ❌ 名前空間付き PDO（`Pdo\Mysql::ATTR_*` 等, **8.4+**）→ 8.1 では `PDO::MYSQL_ATTR_*` のまま
>
> 上記以外（非正規キャスト、`null` オフセット→`''`、自動解放関数の削除、バッククォート→`shell_exec`、`case :`、`__serialize`）は **8.1 でも安全**に適用できる。

### 0. 【破壊的変更・致命的】予約クラス名（`Array` 等）を `use` で参照すると Fatal
8.5 で `Array` などが特別なクラス名として予約され、`use ...\Array;` や `class Array` が **Fatal**（警告ではない）になる。テスト収集（オートロード）段階で落ちると**スイート全体が起動不能**になるため最優先。
```
Fatal error: Cannot use BcMail\Test\TestCase\Model\Array as Array because 'Array' is a special class name
```
- **baserCMS 実績**: `MailMessagesTableTest.php` 冒頭に CakePHP2 時代の残骸 `use ...\Array;` / `use ...\ClassRegistry;`（いずれも実在しないクラスへのデッド import）が残っており、8.4 までは無害だったが 8.5 で Fatal 化。**該当の `use` 行を削除**して解消（参照箇所は `markTestIncomplete` 済みの未実行コード）。
- **検出**: `grep -rnE "use [A-Za-z0-9_\\\\]+\\\\(Array|Object|Resource|Enum|Mixed|Never|Void|Null|False|True|Iterable|Numeric)\s*;" --include="*.php"`。
- **注意**: 致命的なので `php -l` ではファイル単体で検出可能。だが全テスト前に grep で一掃しておくと、スイートが収集段階で落ちる事故を防げる。

### 1. 非正規キャスト名の非推奨（影響大）
`(boolean)` `(integer)` `(double)` `(binary)` が非推奨。正規名に置換する。
```php
// Before → After
(boolean) → (bool)
(integer) → (int)
(double)  → (float)
(binary)  → (string)
```
- 検出: `grep -rnE "\((boolean|integer|double|binary)\)" --include="*.php"`。
- 古いコードに残りがち。機械的に置換可能。

### 2. `null` を配列オフセットに使うのが非推奨
`$arr[null]` / `array_key_exists(null, $arr)` 等で `null` をキーに使うのが非推奨（暗黙に `""` 変換されていた）。明示的に空文字 `""` を使う。
```php
// Before
$arr[null] = $v;  array_key_exists(null, $arr);
// After
$arr[''] = $v;    array_key_exists('', $arr);
```
- 変数がキーになる箇所（`$arr[$key]` で `$key` が null になりうる）に注意。
- **静的 grep では検出困難**（実行時に値が null のときだけ出る）。全テストを 8.5 で流して `grep "null as an array offset" phpunit.log` で発生元（`on line N of <file>`）を集計するのが確実。
- **修正方針**: ① 代入/参照のキーを `$key ?? ''`（従来の暗黙変換と同等。**set/get 両方を同じキーに**揃えること）、② `isset()`/参照の手前で `$key !== null &&` ガード、のいずれか。`getData('batch')` 等の null 返却を `?? ''` で受けるのも有効。
- **baserCMS 実績（計 308 件）**: `BcFileUploader`（`uploadingFiles[$bcUploadId]` の set/get を `?? ''` で整合）、`BcUploadBehavior`（`oldEntity[$alias][$entity->_bc_upload_id]` を `?? ''`）、`MailMessagesTable`（`$dists[$mailField->group_valid]`）、`SearchIndexes/Contents/PermissionsController`（`getData('batch') ?? ''`）、`BcTextHelper::arrayValue` / `BcUtil::decodeContent`（`$key !== null && isset(...)`）。

### 3. オブジェクトを自動解放するリソース解放関数の非推奨
対象はリソースではなくオブジェクトになっており、GC で自動解放されるため明示的な close/free 呼び出しが非推奨。**呼び出しを削除**すればよい。
- `curl_close()` / `curl_share_close()`（CurlHandle / CurlShareHandle）
- `finfo_close()`（finfo）
- `imagedestroy()`（GdImage）← 画像処理ユーティリティで使われがち
- `xml_parser_free()`（XMLParser）
- 検出: `grep -rnE "\b(curl_close|curl_share_close|finfo_close|imagedestroy|xml_parser_free)\s*\(" --include="*.php"`。
- **baserCMS 実績**: `Imageresizer.php`・`BcFileUploader.php` の `imagedestroy()` 4箇所。単純削除でもよいが、ループ内のメモリ即時解放の意図を保つため **`unset($img)` に置換**（GdImage はオブジェクトなので参照を外せば GC が解放。>=8.1 で安全）。

### 3-2. `ReflectionProperty/Method::setAccessible()` の非推奨（発生件数が最も多い）
`setAccessible()` は **PHP 8.1 以降 no-op**（private/protected も `getValue`/`setValue`/`invoke` で直接アクセス可）。8.5 で非推奨化。**呼び出し行を削除**すればよい（>=8.1 で安全）。
```php
// Before
$property = new ReflectionProperty($obj, 'foo');
$property->setAccessible(true);   // ← この行を削除
$property->setValue($obj, $v);
```
- **baserCMS 実績**: src/テスト合わせて33箇所（`BcUtil`/`BcTestCase`/`BaserCorePlugin`/`PreviewController`/`BcDatabaseService` ほか）。ホットパス（テスト基底等）にあるため**ランタイム発生件数が桁違いに多い**（全テストで約6,300件 ≒ 8.5 非推奨の大半）。まずこれを潰すとログが激減する。
- **一括削除**（単独文に限定）: `sed -i -E "/^[[:space:]]*\$[A-Za-z0-9_]+->setAccessible\(true\);[[:space:]]*$/d" <file>`。
- **検出時の注意**: grep パターンを `->setAccessible` のように **`-` で始めると grep がオプションと誤認**して 0 件になる。`grep -rn "setAccessible"` のように `-` 始まりを避ける（または `grep -e`）。

### 4. 非数値文字列のインクリメント非推奨
`$str++`（非数値文字列）が非推奨。`str_increment()` を使う。**※ `str_increment()` は 8.3+。8.1 維持中は `$str++` のまま据え置く。**
```php
// Before
$code++;            // $code が 'AZ' 等
// After
$code = str_increment($code);
```

### 5. `__sleep()` / `__wakeup()` のソフト非推奨
`__serialize()` / `__unserialize()` を使う（PHP7 互換が不要なら移行）。

### 6. `$http_response_header` の非推奨
スーパーグローバル `$http_response_header` が非推奨。`http_get_last_response_headers()` を使う。**※ `http_get_last_response_headers()` は 8.4+。8.1 維持中は `$http_response_header` のまま据え置く。**

### 7. バッククォート演算子の非推奨
`` `command` ``（`shell_exec()` のエイリアス）が非推奨。`shell_exec()` を直接使う。

### 8. `case` 文を `;` で終端するのが非推奨
`case 1;` は非推奨。`case 1:` にする。
- 検出: `grep -rnE "case\s+.+;\s*$" --include="*.php"`（誤検出が多いので目視確認）。

### 8-2. `array_key_exists(null, ...)` の非推奨（serialize の不正形で誘発）
`array_key_exists()` の第1引数 null が非推奨（空文字を使う）。**フレームワーク内部（CakePHP `JsonView::_dataToSerialize`）で出る場合、原因は自前コードの `serialize` オプションの不正形**。`setOption('serialize', ['foo', 'message' => $message])` のように**変数名のリストに `'key' => 値` を混ぜる**と、`$message` が null のとき serialize 配列に `'message' => null` が入り、`array_key_exists(null, ...)` が走る。
- **修正**: serialize は**ビュー変数名の羅列**にする。`['foo', 'message' => $message]` → `['foo', 'message']`（値は `$this->set()` 済み）。baserCMS 実績: `MailMessagesController` の index/view アクション。
- **発生元がフレームワーク内のときの特定法**: vendor を一時計測（`if ($key === null) file_put_contents(..., var_export($serialize,true).getTraceAsString())`）→ 該当プラグインのテストを流す → スタックトレースの先頭の自前コントローラを特定 → vendor を戻す。

### 8-3. `ArrayObject` にオブジェクトを backing array として渡す非推奨
`new ArrayObject($obj)` で**オブジェクト**（`json_decode()` の stdClass 等）を渡すのが非推奨。配列にキャストする。
- **修正**: `new ArrayObject(json_decode($body)->devices)` → `new ArrayObject((array)json_decode($body)->devices)`。baserCMS 実績: `SitesControllerTest`。

### 9. その他（該当時のみ）
- **PDO ドライバ固有の定数/メソッド**が名前空間付きに移行（`PDO::MYSQL_ATTR_*` → `Pdo\Mysql::ATTR_*`、`PDO::sqliteCreateFunction()` → `Pdo\Sqlite::createFunction()` 等）。多くはフレームワーク内部だが、独自に DB ドライバ定数を使っていれば対応。PDO の `"uri:"` DSN スキームも非推奨。**※ 名前空間付き PDO クラスは 8.4+。8.1 維持中は `PDO::MYSQL_ATTR_*` のまま据え置く。**
- `openssl_pkey_derive()` の `$key_length`、`finfo_buffer()` の `$context` 引数が非推奨（無視される）。
- 非CLI SAPI での `register_argc_argv` INI が非推奨（`$_GET` / `$_SERVER['QUERY_STRING']` を使う）。
- static クロージャへの bind 等、一部のクロージャ binding が非推奨。
- **`clone null` のエラーメッセージ変更（テスト影響）**: 8.4 まで `__clone method called on non-object`、8.5 は `clone(): Argument #1 ($object) must be of type object, null given`。null を `clone` する異常系を文字列一致で検証しているテストは期待値の更新が必要（baserCMS 実績: `UploaderCategoriesControllerTest` の Admin/Api 各 `test_copy`）。

> ⚠️ **全テスト実行時の連鎖エラーはまず再実行で切り分ける**: 本コードベースは full-run で稀に「ある先行テストが global 状態（プラグイン設定 Configure 等）を汚染 → 以降ほぼ全テストが Application bootstrap で `PluginCollection::create(): $config ... null given` 等で連鎖エラー」という一過性の汚染が起きることがある。プラグイン単独スイート（例 `plugins/baser-core/tests/TestCase`）が通り、再実行でクリーンなら**コード起因ではなくフレーキー**。isolation と再実行で必ず確認する。

---

## 調査の進め方（チェックリスト）

1. `php -l`（構文チェック）や `logs/debug.log` で**非推奨警告の発生ファイル・行**を特定。
2. 該当バージョン節のどのパターンか分類して修正。機械置換可能なもの（非正規キャスト等）は grep で一括検出。
3. `php -l` で再確認（警告が消えたか）。
4. 当該テストを `--filter` で単体確認 → 仕上げに全テスト再実行。

関連: フレームワーク起因（イベント戻り値・Association重複・ResultSet・FormProtection 等）は `cakephp-migration` スキルへ。

---

## 出典

- [PHP: Backward Incompatible Changes / Deprecated Features (PHP 8.5 Manual)](https://www.php.net/manual/en/migration85.deprecated.php)
- [PHP Changes Cheatsheet（バージョン別 非推奨/破壊的変更）](https://eusonlito.github.io/php-changes-cheatsheet/deprecated.html)
