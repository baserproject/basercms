# 移行上のルール

## 既存コードの移植について

基本的には、テストも含めてbaserCMS4の既存コードを配置し、動作するように改修を加えていきます。

　
## ヘッダーコメント

### プログラムコードのヘッダー

各ファイルの最上位に記述します。
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

クラス定義の上部につけます
```php
/**
 * Class BcAdminAppController
 * @package BaserCore\Controller\Admin
 */
```

　
## クラスメンバー変数へのアクセス

メンバー変数への直接アクセスはせず、セッター、ゲッターを配置するようにする。


　
## 移行時に動作に影響があるファイルの対処

一旦、baserCMS4のコードをそのままこのプロジェクトに配置しています。動作に支障をきたすファイルについて、一時的に次のような対処を行っています。

　
### 未移行ディレクトリ（_NotYetMigrated）へ移動

名前空間などへの移行が完了していないクラスについて、各配置フォルダ直下に `_NotYetMigrated` ディレクトリを作成し、その中に移動しています。    
`_NotYetMigrated` 内のクラスを移行する際は、差分がわかるように、一度、あるべき位置に配置して一度コミットしてから修正作業を行ってください。

　
### 予備ファイル

baserCM4 から ucmitz にファイルを移行する際、そのままのファイル名で上書きすると ucmitz の動作に影響しそうなものは、ファイル名の末尾に「-4」を追加しています。
このファイルは移行対象との差分を確認し、マージを行った後に削除が必要です。

```
bootstrap.php → bootstrap-4.php
```

　
### 未チェッククラス

クラスで未チェックのものは、ファイルの冒頭に次のコードを記述し動作に影響がないようにしています。チェックをする際には、該当行を削除してください。

```php
// TODO コード確認要
return;
```

　
## TDOO の記録

チェック時、参照しているクラスメソッドが未実装である場合など、完全な動作確認ができない場合があります。その際は、TODOコメントを必ず記載してください。
また、コメントについては他の人が見てもわかるようにしてください。

なお、既存のコードをコメントアウトする場合は、範囲を指定するようにしてください。

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

$options = array_merge([    // 本来であればここからスタートだが代替措置で return されているため実行されない
    'home' => 'Home',
    'default' => 'Default',
    'error' => 'Error',
    'underscore' => false
], $options);
```

　
## コード移行時のマーキング

クラスメソッドやビューファイルの移行実装時、および新規ファイル追加時には、ヘッダーコメントにアノテーションでマーキングをします。

```
@checked : コードの精査が完了している
@noTodo : Todo が発生しない
@unitTest : unitTest が実装済である
```

これにより進捗管理表に自動反映し、進捗状況をわかるようにしています。

- [ucmitz 進行管理](https://docs.google.com/spreadsheets/d/1EGxMk-dy8WIg2NmgOKsS_fBXqDB6oJky9M0mB7TADEk/edit#gid=938641024)

なお、クラスの冒頭にアノテーションのインポートが必要となりますので忘れないようにしてください。

```php
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
```

　

マーキングの例
```php
// 例）
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

class BcBaserHelper extends Cake\View\Helper 
{
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

## ユニットテストについて

移植のタイミングで存在しないテストは必ず追加します。また、新しいメソッドについても必ずテストを追加してください。
その際、テストの実装が間に合わない場合は、 `markTestIncomplete()` を記載しておいてください。その際、アノテーションで、`@unitTest` を付けてはいけません。

```php
$this->markTestIncomplete('Not implemented yet.');
```

　　
## File / Folder の取り扱い

CakePHP4 から、File、Folder クラスは非推奨となり、SplFileInfo、SplFileObject の利用が推奨されていますが、baserCMSでは利用箇所が多いため、一旦、そのまま利用してください。
