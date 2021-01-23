# 開発メモ

## ヘッダーコメント

### プログラムコードのヘッダー
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

### クラスヘッダー
つける
```php
/**
 * Class BcAdminAppController
 * @package BaserCore\Controller\Admin
 */
```

　

## コアの構成

### コアプログラム
baserCMSのコア（BaserCore）は、CakePHPのプラグインとしての開発を前提とし、plugins 配下内に配置する仕様とした。 `/plugins/baser-core/`

### テーマ
CakePHPのプラグインとしての開発を前提とし、plugins 配下内に配置する仕様とした。 `/plugins/bc-admin-third/`
詳細については、[BcAdminThirdの開発](https://github.com/baserproject/ucmitz/blob/dev/plugins/bc-admin-third/README.md) を参考にします。

なお、テーマの定義は現在、`BaserCore\Controller\BcAdminAppController::beforeRender()` にて行っています。
### プラグイン
その他のプラグインも一旦、plugins 配下内に配置する。
プラグインのロードは、 `BaserCore\BcApplication::bootstrap()` にて実装する。
詳細については、[プラグインの呼び出し](https://github.com/baserproject/ucmitz/blob/dev/docs/call-plugin.md) を参考にします。

　

## ルーティング
ルーティングについては、`BaserCore\BcPlugin::routes()` にて定義します。

　

## リクエスト関連

### リクエストの取得
```php
$this->getRequest();    // Controller / View
Router::getRequest();    // Other
```
　

## セッションの取得
```php
$session = $request->getSession();
$session->read('etc');
```

　

## URL関連

### 現在のURLを取得する

#### パラメータなし
```php
$request->getPath();
```
#### パラメータあり
```php
$request->getRequestTarget();
```

　

## フォーム関連

### フォームコントロールのテンプレート
`/baser-core/config/bc_form.php` で定義できる

　

## モデル関連

### モデルの取得

```php
ClassRegistry::init('User');
 ↓
TableRegistry::getTableLocator()->get('Users');
```

　

## baserCMS関連

### フォームコントロールの出力
`$this->BcForm->input()` から、`$this->BcAdminForm->control()` に変更

### 検索フォーム、ヘルプの設定

#### コントロールの場合

```php
$this->search = $templateName;  // 旧
$this->help = $templateName;  // 旧
　↓
$this->setSearch($templateName);    // 新
$this->setHelp($templateName);    // 新
```

#### ビューの場合

```php
$this->BcAdmin->setSearch($templateName);
$this->BcAdmin->setHelp($templateName);
```

### タイトルの設定

#### コントロールの場合

```php
$this->pageTitle = $title;  // 旧
　↓
$this->setTitle($title);    // 新
```

#### ビューの場合

```php
$this->BcAdmin->setTitle($title);
```

## basics.php 関数について

`BcUtil` に静的メソッドとして統合。  
`getVersion()` → `BcUtil::getVersion()`
　

## ユニットテストについて
ユニットテストの作成と実行については [ユニットテスト](https://github.com/baserproject/ucmitz/blob/dev/docs/unittest.md) を参考にします。

　

## パッケージング
モノレポとして `monorepo-builder` を利用し、BaserCore、BcAdminThird も統合的に管理できるようにした。
リリース時にパッケージの分割しょりが必要なります。
詳細については [モノレポによるパッケージ管理](https://github.com/baserproject/ucmitz/blob/dev/docs/monorepo.md) を参考にします。

　

## 問題点

### 並べ替えボタンのCSSについて

クラス `btn-direction bca-table-listup__a` を付与しなければ、CSSが反映されないが、並べ替えの A タグに暮らすを付与できない仕様となってしまっていた。
CSS側を調整する必要あり

### Vue.js の読み込みについて

メニューの表示に Vue を利用しているが、利用する javascript ファイルにて、node_modules より読み込みたいが、そちらで読み込むと何故かメニューが表示されない。
ダウンロードしたものを配置してそちらを読み込むと表示できる。

