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

なお、既存のコードをコメントアウトする場合は、範囲を指定するよう

```php
// TODO 未実装のためコメントアウト
/* >>>
$hoge = 1;
$hoge = 2;
<<< */
```
