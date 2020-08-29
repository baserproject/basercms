# 開発メモ

## プログラムコードのヘッダー
以下に統一
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

## クラスヘッダー
つける
```php
/**
 * Class BcAdminAppController
 * @package BaserCore\Controller\Admin
 */
```

## フォームコントロールのテンプレート
`/baser-core/config/bc_form.php` で定義できる

## フォームコントロールの出力
`$this->BcForm->input()` から、`$this->BcAdminForm->control()` に変更

# 問題点

## 並べ替えボタンのCSSについて

クラス `btn-direction bca-table-listup__a` を付与しなければ、CSSが反映されないが、並べ替えの A タグに暮らすを付与できない仕様となってしまっていた。
CSS側を調整する必要あり

## Vue.js の読み込みについて

メニューの表示に Vue を利用しているが、利用する javascript ファイルにて、node_modules より読み込みたいが、そちらで読み込むと何故かメニューが表示されない。
ダウンロードしたものを配置してそちらを読み込むと表示できる。

