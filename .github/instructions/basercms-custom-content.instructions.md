# Copilot Instructions baserCMS CustomContent
このルールは baserCMSのカスタムコンテンツの開発について定めたものです。

## カスタムコンテンツの仕様について
カスタムコンテンツの資料はこちらを参照してください。
https://baserproject.github.io/5/functions/bc-custom-content/

## カスタムコンテンツのテンプレートの場所について
カスタムコンテンツのテンプレートは、`/plugins/{テーマ名}/templates/CustomContent/` 配下に配置します。
例）`/plugins/BcThemeSample/templates/CustomContent/products/index.php`

## カスタムエントリーのテンプレート作成について
plugins/bc-front/templates/plugin/BcCustomContent/CustomContent/default/ 内のテンプレートを参考にする

値表示は次のように表示する
```php
$this->BcBaser->getCustomFieldValue($customEntry, 'カスタムフィールド名')
```

## ヘルパの利用
テンプレートで利用するカスタムコンテンツのヘルパは、`CustomContentHelper` を利用できますので参考にします。
ただし、実際の利用は、`BcBaserHelper` を通して行います。
`BcBaserHelper` を通して実行する方法は、`CustomContentBaserHelper` を利用して、マジックメソッドで関連付けを行っています。

```php
// CustomContentBaserHelper の定義例
// $this->BcBaser->getCustomFieldValue() で、CustomContentHelper::getFieldValue を呼び出す
'getCustomFieldValue' => ['CustomContent', 'getFieldValue'],
```

## アーカイブページ

### URLについて
アーカイブページのURLは、次のようになります。選択リストのフィールドを利用すると自動生成されます。
`/{カスタムコンテンツ名}/archives/{フィールド名}/{フィールド値}`

### タイトルについて
$archivesName で参照できます。

## 前後ナビゲーションについて
- 前のエントリーの存在確認: `$this-BcBaser->hasPrevCustomEntry($customEntry)`
- 次のエントリーの存在確認: `$this-BcBaser->hasNextCustomEntry($customEntry)`
- 前のエントリーへのリンク（echo 不要）: `$this->BcBaser->prevCustomEntryLink($customEntry)`
- 次のエントリーへのリンク（echo 不要）: `$this->BcBaser->nextCustomEntryLink($customEntry)`
- リンクについては内部的に echo されるので、echo は不要です。
- リンクについて第二引数を省略すると、デフォルトで対象のタイトルが入ります。

## 画像の表示について
画像の表示は、次のタグでそのまま img タグを出力します。

```php
echo $this->BcBaser->getCustomFieldValue($customEntry, 'field_name');
```

## textarea の表示について
内部的に nl2br() が適用されているので、改行はそのまま表示されます。

## CSSの作成
- 既存のテーマにテンプレートを追加作成する場合、CSSの作成時、Aタグの文字色は親設定を引き継いでいる可能性があるので、important を付けておく。

## その他
- テキストエリアは h() でエスケープすると改行が<br> として表示されてしまうので使わない。
