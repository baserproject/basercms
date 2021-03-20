# 移行上のルールと注意点

## ヘッダーコメント

### プログラムコードのヘッダー
各ファイルの最上位に記述する。以下に統一
```php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */
```

### クラスヘッダー
クラス定義の上部につける
```php
/**
 * Class BcAdminAppController
 * @package BaserCore\Controller\Admin
 */
```

## クラスメンバー変数へのアクセス

メンバー変数への直接アクセスはせず、セッター、ゲッターを配置するようにする。


## 移行時に動作に影響があるファイル

### 予備ファイル

baserCM4 から ucmitz にファイルを移行する際、そのままのファイル名で上書きすると ucmitz の動作に影響しそうなものは、ファイル名の末尾に「-4」を追加している。
このファイルは移行対象との差分を確認し、マージを行った後に削除が必要。

```
bootstrap.php → bootstrap-4.php
```

### 未チェッククラス

クラスで未チェックのものは、ファイルの冒頭に次のコードを記述し動作に影響がないようにしている。チェックをする際には削除する。

```php
// TODO コード確認要
return;
```

## TDOO の記録

チェック時、参照しているクラスメソッドが未実装である場合など、完全な動作確認ができない場合がある。その際は、TODOコメントを必ず記載する。
コメントについては他の人が見てもわかるようにする。

なお、既存のコードをコメントアウトする場合は、範囲を指定する

```php
部分的に動作しないのでコメントアウトしたい場合

// TODO 未実装のためコメントアウト
/* >>>
$hoge = 1;
$hoge = 2;
<<< */
```

```php
メソッドの冒頭部で代替措置を記述する場合

// TODO 未実装のため代替措置
// >>>
$request = $this->_View->getRequest();
if ($request->getParam('action') === 'login') {
    return 'AdminUsersLogin';
} else {
    return 'Admin';
}
// <<<

$options = array_merge([    // ※ 本来であればここからスタートだが代替措置で return されているため実行されない
    'home' => 'Home',
    'default' => 'Default',
    'error' => 'Error',
    'underscore' => false
], $options);
```

## コード移行時のマーキング

クラスメソッドやビューファイルの移行実装時には、ヘッダーコメントにアノテーションでマーキングをする

```
@checked : コードの精査が完了している
@noTodo : Todo が発生しない
@unitTest : unitTest が実装済である
```

これにより進捗管理表に自動反映し、進捗状況をわかるようにする。


```php
// 例）

    /**
     * コンテンツを特定する文字列を出力する
     *
     * URL を元に、第一階層までの文字列をキャメルケースで取得する
     * ※ 利用例、出力例については BcBaserHelper::getContentsName() を参照
     *
     * @param bool $detail 詳細モード true にした場合は、ページごとに一意となる文字列をキャメルケースで出力する（初期値 : false）
     * @param array $options オプション（初期値 : array()）
     *    ※ オプションの詳細については、BcBaserHelper::getContentsName() を参照
     * @return void
     * @checked
     * @noTodo
     */
```
