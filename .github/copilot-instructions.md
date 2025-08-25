# GitHub Copilot Instructions

このディレクトリには、開発指示書をまとめています。プロジェクトの開発において、これらのガイドラインに従って作業を進めてください。

## 指示書一覧

### [basic.instructions.md](./instructions/basic.instructions.md)
**基本的な開発ガイドライン**

GitHub Copilot が開発を行う際の基本的なルールとベストプラクティスを定義しています。

- **基本ルール**: 要件に従った作業進行、自律的な問題解決、既存コードの尊重
- **技術スタック**: プロジェクト定義に従った技術選定
- **セキュリティ**: 機密情報の適切な取り扱い、ユーザー入力検証
- **コーディング**: シンプルで読みやすいコード、適切な命名、エラーハンドリング
- **ベストプラクティス**: 再利用可能なコンポーネント作成、パフォーマンス・アクセシビリティ対応

### [basercms.instructions.md](./instructions/basercms.instructions.md)
**baserCMS 開発全般のガイドライン**

baserCMS（PHP8 + CakePHP5 ベース）の開発についての包括的な指示書です。

- **アーキテクチャ**: プラグインベースの構成、Contents/CustomContent サービス分離
- **開発・テスト**: Docker コンテナでのユニットテスト実行、API テストのトークン認証
- **コーディング規約**: クラスメソッド追加時の配置ルール、テスト構成
- **ディレクトリ構成**: プラグイン/テーマの配置、設定ファイルの場所
- **API 連携**: REST API の認証方式、エンドポイント構成

### [basercms-custom-content.instructions.md](./instructions/basercms-custom-content.instructions.md)
**baserCMS カスタムコンテンツ開発ガイド**

baserCMS のカスタムコンテンツ機能に特化した開発指示書です。

- **テンプレート配置**: テンプレートの配下場所
- **データ表示**: カスタムエントリーの値表示方法
- **アーカイブページ**: 選択リストフィールドによる自動URL生成、でのタイトル参照方法など
- **ナビゲーション**: 前後エントリーリンクの実装パターン
- **特殊表示**: 画像表示、テキストエリアの改行処理
- **CSS 作成**: 既存テーマへの追加時の注意点

### [local.instructions.md](./instructions/local.instructions.md)
**ローカル開発環境の設定**

ローカル環境での特有のユニットテスト実行の方法などを記載しています。
基本的に .gitignore にて、コミット対象外になっており、存在しない場合があります。

## 使用方法

1. プロジェクトの種類に応じて、該当する指示書を参照してください
2. 基本的な開発ルールは `basic.instructions.md` を基準とします
3. baserCMS 開発では `basercms.instructions.md` を主要ガイドラインとして使用
4. カスタムコンテンツ開発時は `basercms-custom-content.instructions.md` を追加参照
5. ローカル環境でのテスト実行時などは `local.instructions.md` の設定を確認

これらのガイドラインに従って、一貫性のある高品質なコードの開発を行ってください。
