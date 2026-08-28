---
name: basercms4-to-5-upgrade
description: 'baserCMS 4 (CakePHP 2ベース) のコード・サイトを baserCMS 5 (CakePHP 5ベース) へアップグレード／移行する際に AIエージェントが遵守すべきルールとパターン集。「baserCMS 4 から 5 へ移行」「4系を5系にアップグレード」「BcDbMigrator でデータ移行」「BcAddonMigrator でテーマ／プラグイン変換」「CakePHP 2 → CakePHP 5 への書き換え」「admin_ プレフィックスメソッドの Controller/Admin への移動」「config/setting.php・config.php の return 配列化」「init.php 廃止と PluginClass 作成」「migration_snapshot 生成」「site_configs theme → sites theme」「移行の標準フロー／工程の順序」「プラグインを DB で直接有効化」「テーマを DB で直接適用」「request->data／query／params の getter 化」「File/Folder クラス廃止」「ClassRegistry → TableRegistry」「FrozenTime → DateTime」「FixtureManager → FixtureFactory」等、4→5 アップグレード作業全般で参照する。プラグイン内部コード（Controller/Table/Entity/View/Helper/フォーム/Vue・JS）の具体的な書き換えパターンは basercms-plugin-4-to-5-upgrade、テーマ（templates 中心）の移行は basercms-theme-4-to-5-upgrade、5.2→5.3 のプラグイン移行は basercms-plugin-5x-update、CakePHP本体起因は cakephp-migration、PHP本体起因は php-migration、テスト実行は basercms-unittest スキルを参照。移行先である5系での正しい書き方（開発の正本）は basercms5-plugin-development / basercms5-theme-development を参照。'
license: MIT
---

# baserCMS 4 から 5 へのアップグレード/移行ルール

このファイルは、baserCMS 4 (CakePHP 2ベース) のコードを baserCMS 5 (CakePHP 5ベース) に移行する際、AIエージェントが遵守すべきルールとパターンを定義します。

> **推奨: 移行に着手する前に一度 `basercms5-claude-workflow-setup`（環境セットアップ）を参照し、進め方の環境（権限整理=permissions-audit／その上での Auto mode／設計=superpowers brainstorming／spec・plan の Markdown プレビュー）を整える。提案ベースで、整っていればスキップ。**

## 基本方針

1.  **フレームワークの差異**: baserCMS 4 は CakePHP 2、baserCMS 5 は CakePHP 5 ベースです。CakePHP 2 の記法は基本的に動作しません。CakePHP 5 の記法に完全に書き換えてください。
2.  **クラスの置換**: 廃止されたクラスは、代替クラスまたはPHP標準クラスに置き換えてください。
3.  **アクセサの利用**: プロパティへの直接アクセスは避け、Getter/Setter を使用してください。
4. アップグレードの詳細情報については https://baserproject.github.io/5/ver5_migration を参考にする。
5. **【最重要・横断対応の原則】1画面の修正で見つけた不具合のうち、同じ原因がプラグイン/サイト全体に散在するものは、その場で“横断的に”一括対応する**。1箇所だけ直して次の画面で同じエラーに当たる、を繰り返さない。手順: ①エラーを直したら **同じパターンを `grep -rn` で全件洗い出す**（例: `$this->Form->input(`、`'multiple' => 'checkbox'`、単数 `get('Sample.Sample...')`、`searches/`、`Time->format($x)` 第2引数なし 等）→ ②機械的に一意な変換は `perl -pi` で一括適用 → ③**変更ファイルを全て `php -l` で検証** → ④非自明な箇所だけ個別対応。横断一括できる代表例は **basercms-plugin-4-to-5-upgrade スキルの C-0（機械的に一括変換できるパターン）** にカタログ化してある（見つけ次第追記する）。新しい横断パターンを見つけたらまず C-0 に追加してから一括実行する。プラグイン内部コード（Controller/Table/Entity/View/Helper/フォーム/Vue・JS）の具体的な書き換えは同スキルを参照。

## 標準移行フロー（この順で進める・正本）

**移行全体はこのチェックリストの順序で進める**。順序依存の罠（★印）が複数あるため、節の並びや思い込みで順番を変えないこと。各工程の詳細は参照先の節に書いてある。

- [ ] **0. 実行環境の確認** — Docker利用有無・コンテナ名・PHPバージョン・プロキシ構成をユーザーに確認 → 「前提: 実行環境の確認」節
- [ ] **1. 4系の現状調査** — プラグイン一覧（`app/Plugin/`）・テーマ（`theme/`）・DB名/プレフィックス・マルチサイト有無・サブモジュール有無（`find app/Plugin theme -name .git`）・管理プレフィックス（`app/Config/core.php` の `Routing.prefixes`）を洗い出す
- [ ] **2. 移行対象の確定（棚卸し）** — プラグイン・テーマを1つずつ (a)既存5系版利用/(b)4系を変換/(c)廃止 に分類し、**表でユーザーの合意を取る** → 「プラグイン棚卸し」節
- [ ] **3. 移行計画の作成** — 棚卸し結果を反映した計画を作成しユーザー承認（進め方の環境は `basercms5-claude-workflow-setup` 参照）
- [ ] **4. トピックブランチ作成** — 移行作業用ブランチを切る（例 `git switch -c basercms5-migration`）。v5 配置物の git 取り込み時の注意は「Git / リポジトリ運用」節（G-1〜G-4）
- [ ] **5. 4系バックアップ作成** — v4 管理画面「データメンテナンス」で作成 → zip を `v5/tmp/` へ。**★PHP切替（7.4→8.1）を伴う環境では、v5 インストール＝PHP を上げる【前】に必ず取る**（上げた後だと v4 管理が不調になり得る）→ 「データベース移行手順」節 1.
- [ ] **6. 5系を `/v5/` にインストール** — パッケージ取得 → composer → .env → `bin/cake install` → 実ブラウザでログイン確認 → 「baserCMSの最新版の取得」〜「baserCMSのインストール」節
- [ ] **7. BcDbMigrator 導入 → DB変換** — 変換zipの出力まで。**★復元はまだしない**（工程12まで待つ）→ 「データベース移行手順」節 2.-3.
- [ ] **8. BcAddonMigrator 導入** — 「テーマの変換」節 2.
- [ ] **9. (a)既存5系版の導入 ＋ (b)テーマ・プラグインの変換・配置** — (b) は zip 化 → `bc_addon_migrator` → `v5/plugins/` 展開。**★(a) の導入もここ（復元前）で行う**。終盤に回すとそのプラグインのテーブルにデータが復元されない → 「テーマの変換」「プラグインの変換」節
- [ ] **10. プラグインを DB で直接有効化** — `plugins` テーブル INSERT（Migrations を持つものは `migrations migrate -p` 併走）。**★有効化で boot 経路の4系残骸が fatal 化するので、`bin/cake version` スモークが通るまで最小修正**。検証は CLI＋SQL まで（**ブラウザ確認はまだしない** — 画面は工程15-17の5系化まで出なくて正常）→ 「プラグインの有効化（DB直接・標準）」節
- [ ] **11. テーマを DB で直接適用** — `sites.theme` UPDATE ＋ キャッシュ全消去 → 「テーマの適用（DB直接）」節
- [ ] **12. DB復元** — v5 管理画面「データメンテナンス」で変換済み zip をブラウザ復元（復元CLIなし・ユーザー実施）。**★必ず工程10-11（有効化・適用）の後**。直前に管理画面ログインの生存だけ確認（フロントは見ない）。復元後にテーブル生成・件数を SQL で検証 → 「データベース移行手順」節 4.
- [ ] **13. アップロードファイルの移行** — `/files` → `/v5/webroot/files` → 「アップロードファイルの移行」節
- [ ] **14. 移行台帳（ファイル一覧）の作成** — 対象プラグイン・テーマごとに構成ファイル一覧と状態を記した台帳を `docs/superpowers/` に作る（`.superpowers/` はSDD内部スクラッチなので置かない）。以降の横断チェック・動作確認の進捗と懸案はすべてここに記録する。**状態の語彙は「未着手／修正済み／非該当（理由付き）」を必ず区別する** — 横断チェックで grep にヒットしなかったファイルは「未着手」ではなく「非該当（既知パターンhitなし・最終検証は工程17の描画確認）」。集計表の「完了」は「既知パターンの横断適用が完了」の意味であり全ファイル個別検査済みではない、と台帳に明記する（誤解の実例あり）
- [ ] **15. テーマの横断チェック → 5系化（下ごしらえまで）** — TH-系パターン（`siteConfig[]`廃止・`fullUrl()`廃止 等）を grep で全件洗い出して一括対応 → `basercms-theme-4-to-5-upgrade` スキル（TH-系＋テンプレート・テーマ描画系の F-系）。**完了基準は php -l 全通過まで。描画確認はここではしない**（工程17の最初にユーザーと行う）。テンプレのビュー変数は事前の全量マッピングをせず、grep で見つかる「4系特有で5系コアが供給しない変数（`$siteConfig` 等）」だけ先に書き換え、コア供給分は工程17の実描画＋ログ検出に任せる
- [ ] **16. プラグインの横断チェック → 5系化（下ごしらえまで）** — C-0（機械一括変換カタログ）を先に全プラグインへ適用 → T-/C-系を個別対応。完了基準は php -l 全通過まで。**無理な改善（動くものの過剰リファクタ）はせず、気づきは台帳にメモして先へ進む** → `basercms-plugin-4-to-5-upgrade` スキル
- [ ] **17. テーマ→プラグインの順に1つずつ動作確認（ユーザー協働）** — **★開始前に移行対象プラグインを一旦全部無効化し（`UPDATE plugins SET status=0 WHERE name IN (...)`＋キャッシュクリア）、まず「テーマのみ有効」の素の状態でフロント描画をユーザーと確認**（レイアウト→トップ→一覧→詳細の画面単位。500 は `error.log`、穴あき描画は `grep "Undefined variable"` で機械検出し、警告ゼロまで直す）。その後、確認対象プラグインを1つずつ有効化しながら進める。イベントリスナー等で他プラグインの影響を受けやすく、全部有効のままだと不具合の切り分けができないため。対象が他プラグインに依存する場合は**その依存プラグインだけ**を併せて有効化する。プラグインごとに次の順で:
  - a. コントローラ/テンプレート以外（Table/保存/集計）をユニットテストで確認（依存が重いものはスキップ可・台帳にメモ）→ `basercms-unittest` スキル
  - b. コントローラの統合テストを作成して実行（**この時点の失敗は許容**して台帳に記録）。**★スタンドアロンUT基盤が入っているプラグインでは b をブラウザ確認より先に回すと費用対効果が高い（実証済み）**: GET描画テストが「setting.php の snake_case 由来の全管理画面404」や「control() 自動id とインラインJSセレクタの不一致（C-F2）」をブラウザ往復ゼロで検出した。基盤未導入のプラグインまで b のために基盤導入するかは、画面数とロジック量で判断（イベントグルー中心の小型はブラウザ確認で足りる）
  - c. ユーザーにブラウザ動作確認を依頼（URLはリンク付きで提示）→ エラーはスクリーンショット/文言をもらって解決
  - d. b. で失敗していたコントローラテストを解決（緑化）
  - e. 対象の確認が終わったら有効のまま次のプラグインへ（最終的に全対象が有効・全確認済みになる）
- [ ] **18. 懸案プラグインの方針決定・対応** — 棚卸しで「要確認」だったもの・台帳の懸案メモを、ユーザーと方針決定（対応/延期/廃止）して消化する
- [ ] **19. 最終統合テスト** — ユーザーが実ブラウザでフロント・管理・フォーム・ブログを通し確認。問題があれば解決
- [ ] **20. Git 取り込み → 完了** — G-1〜G-4 の確認をして v5 をコミット → 「Git / リポジトリ運用」節

## 前提: 実行環境の確認（最初に必ず行う）

**移行の具体作業（パッケージ取得・composer・`bin/cake install`・DB移行・ツール変換）に入る前に、実行環境を必ずユーザーに確認する。推測・探索（`docker inspect` 等）で当てにいかない。**

1.  **Docker を利用しているか、コンテナ名は何かを尋ねる（最重要）**: このスキルのコマンド例は `docker exec <container-name> ...` を多用するが、コンテナ名は環境ごとに異なる。**「Docker を利用していますか？ 利用している場合はコンテナ名を教えてください」と最初に質問する**こと。`docker ps` や `docker inspect` で当てにいく前に、まずユーザーに聞く（リポジトリ内に compose 定義が無く別リポジトリにある構成も多く、探索は誤判定・遠回りの原因になる）。DB コンテナ名・DBユーザー/パスワード・`VIRTUAL_HOST` 等も併せて確認する。
2.  **既存コンテナの PHP バージョンを確認する（baserCMS 5 は PHP 8.1+ 必須）**: baserCMS 4 は php7.4 等で動いていることが多いが、**baserCMS 5 は PHP 8.1 以上が必須**。v5 を既存コンテナに同居させる場合、そのコンテナの PHP が 8.1 未満（例 `baserproject/basercms:php7.4`）なら **image を php8.1 以上へ上げる必要がある**（compose の image タグを変更 → 再起動）。この image 変更は多くの場合 compose 定義側（別リポジトリのこともある）の編集になるため、**編集場所を確認し、リポジトリ外ならユーザーに実施を依頼するか明示許可を得てから行う**。
    - **★image を上げる前に、必ず「v4 が動作しなくなる可能性」を警告して確認を取る**: PHP を 8.1 に上げると、同居している v4 も 8.1 上で動くことになり、**v4 が正常に動作しなくなる可能性がある**。切替を実行する前に「**コンテナを 8.1 に変更しますが、v4 が動作しなくなる場合があります。それでもよいですか？**」と明示的に確認し、了承を得てから変更する。事後に v4 の動作確認を回すのではなく、事前に承諾を得る順序にする（v4 はいずれ廃止する前提のため、**切替後の v4 動作確認は不要**）。
3.  **v5 は「既存コンテナ・既存マウントの中のサブディレクトリ」として構築する（別コンテナ/別 vhost を新設しない）**: `v5` フォルダはプロジェクト直下に作り、既存コンテナのマウント越しに `/var/www/html/v5` として同じコンテナで配信する。ログインURLが `https://<既存ホスト>/v5/baser/admin/...` になるのはこのため。並行稼働のために別サービスを増やす過剰設計をしない（v4 はそのまま無停止で残る）。

## コミュニケーションと言語 (重要)

**全てのコミュニケーションと作成するドキュメントは日本語で行ってください。**

具体的には以下の項目は必ず**日本語**で記述してください：

1.  **チャットの返答**: ユーザーへのメッセージ、状況報告、質問。
2.  **Task Artifact (task.md)**: 進捗管理のチェックリスト、タスク名、サマリー。
3.  **Implementation Plan Artifact**: 実装計画の詳細、ゴール、変更内容、検証計画。
4.  **Walkthrough Artifact**: 作業の振り返り、検証結果の報告。

**[重要・URLはリンク付きで提示]** ユーザーに**ブラウザでの動作確認を依頼する場合**（管理ログインURL・データメンテナンス画面・フロント確認 等）は、URL を**必ず Markdown のリンク付き**（`[https://...](https://...)`）で表示する。プレーンテキストのURLで出さない（ユーザーがクリックしてすぐ開けるようにするため）。例: `👉 [https://<project>.localhost/v5/baser/admin/baser-core/users/login](https://<project>.localhost/v5/baser/admin/baser-core/users/login)`。

**[重要・ユーザーへの質問は毎回 AskUserQuestion（選択式プルダウン）で]** 方針確認・A/B選択・作業依頼の完了確認など**ユーザーの応答を待つ場面では、本文テキストで問いかけるだけでなく必ず AskUserQuestion ツールを使う**。プルダウンが出るとユーザーのスマホに通知が届くが、テキストだけの質問は通知されず気づかれない。移行は長丁場でユーザーが席を外すことが多いため、通知の有無が往復のリードタイムを大きく左右する（ユーザー要望・実運用で確立）。選択肢に収まらない場合も「その他」で自由入力できるので、迷ったらプルダウンにする。


## baserCMSの最新版の取得

GitHubから clone するコードは、開発版となっており、実際のプロジェクトでは利用しません。
次のURLを利用して、パッケージングされたコードを利用します。
https://basercms.net/packages/download_exec/basercms-x.x.x.zip


取得したコードは、プロジェクト内に、`v5` というフォルダを作成し、そこに配置してください。

**取得方法（ホストの `curl`/`wget` が deny されている場合の回避）**: `download_exec` URL はブラウザ用の生成ダウンロードだが、`WebFetch` は「ページを Markdown 化して要約する」ツールのため zip 等バイナリを保存できない。ホストの `curl`/`wget` がパーミッションで deny されていることも多い。その場合、**Docker を使っているなら「コンテナ経由」で取得できる**（`Bash(docker:*)` が許可されていれば、`docker exec ... curl` は `Bash(curl:*)` の deny に掛からない）。プロジェクトはコンテナにマウントされているので、コンテナ内で落とせばホスト側にも実体が出る。最新安定版のバージョンは GitHub Releases（`baserproject/basercms` の latest）で確認する。
```bash
# 例: コンテナ内でパッケージを取得（マウント先 /var/www/html にプロジェクトがある想定）
docker exec -w /var/www/html <container> bash -lc \
  'curl -fL -A "Mozilla/5.0" -o basercms-<x.x.x>.zip "https://basercms.net/packages/download_exec/basercms-<x.x.x>.zip"'
# 取得後: file / unzip -l で zip 妥当性を確認 → unzip -q -o basercms-<x.x.x>.zip -d v5/
```
コンテナ取得が難しい場合のみ、ユーザーにブラウザDLを依頼してパスをもらう。

## composer でパッケージのインストール

`composer install` を実行します。

- **パッケージ版の `vendor/` は空（`.gitkeep` のみ）なので `composer install` は必須**。展開直後に `test -d vendor` が真でも中身は空のことがある（`.gitkeep` だけでディレクトリが存在するため）。`vendor/autoload.php` の有無で判定し、無ければ `composer install` する。

- **4系プラグインが使っていたサードパーティ製パッケージは v5 に明示的に `composer require` する**: 4系 `composer.json` 由来のライブラリ（例 `phpoffice/phpspreadsheet`）は v5 の `composer.json`/vendor に入っていないことがあり、`Class "PhpOffice\PhpSpreadsheet\Reader\Xlsx" not found` 等になる。4系の `composer.json` を参照し、PHP バージョン（v5 は 8.1+）に合うバージョンで導入する（例 `docker exec -w <v5> <container> composer require "phpoffice/phpspreadsheet:^1.29"` → 1.30.x が入る）。実行後 `php -r 'class_exists(...)'` で解決を確認。
- **Excel/PDF 等の雛形アセット（.xlsx 等）は `templates/Admin/Excel/...` に移行され、4系の `View/Excel/admin/...` とはパス構成が違う**: 生成系コンポーネント（SampleExcel 等）が `Plugin::templatePath('Sample') . 'Excel' . DS . 'admin' . DS . ...`（4系配置）を組み立てていると `File "..." does not exist`。`templatePath()` は**末尾スラッシュ付き**なので `. DS .` を足すと `//` になる点も注意。5系の実配置 `Plugin::templatePath('Sample') . 'Admin' . DS . 'Excel' . DS . <controller> . DS . <file>.xlsx` に直す（実ファイル位置を `find <plugin> -iname "*.xlsx"` で確認）。

## .env のコピー

`v5` フォルダ内の `/config/.env.example` を `/config/.env` にコピーします。

`HASH_TYPE` を `sha1` に設定します。

**★`SECURITY_SALT` に v4 の `Security.salt` と同じ値を必ず設定する**（`app/Config/core.php` の `Configure::write('Security.salt', '...')` から転記。`.env` の該当行はコメントアウトされているので外す）。4系のパスワードは sha1(salt.password) なので、**salt が一致しないと DB 復元後に v4 ユーザーで一切ログインできなくなる**（実際にハマった。`.env` 内のコメントにも「4系で利用していたセキュリティーソルトを設定する」と明記されている）。

また、`nginx-proxy` コンテナを利用している場合は、 `.env` の `TRUST_PROXY` の値を `true` に設定します。

## baserCMSのインストール
5系用の新しいデータベースを作成し、`v5` フォルダ内でインストールコマンドを実行します。

```
# 引数の並び: <設置URL> <管理者メール> <管理者パスワード> <データベース名>
#   ★第4引数がDB名。--database というオプションは存在せず、付けると `Unknown option `database`` で失敗する
#   ★サブディレクトリ設置では --baseurl も付ける
bin/cake install [設置URL（例：https://localhost/v5/）] [管理者メール] [管理者パスワード] [DB名（例 <既存DB>_v5）] \
  --baseurl [/v5/] --host [DBホスト名] --username [DBユーザー名] --password [DBパスワード]
```

- **データベース名は第4位置引数**（`--database` オプションは無い）。既存のデータベース名にサフィックスとして `_v5` を付与したものを作成した上で渡す。
- **サブディレクトリ設置では `--baseurl /v5/` を付ける**。

`.htaccess` の RewriteBase は**多くの場合そのままで動く**（5系既定の `.htaccess` は相対リライトのため、`/v5/` サブディレクトリでも無調整で通ることが多い＝実証済み）。**`/v5/v5` 二重化(404) やリダイレクトループが出た場合にのみ** `webroot/.htaccess` の `RewriteBase` を調整する（まずは下記 SITE_URL とキャッシュ残りを疑う）。

ログインURLは、`https://localhost/v5/baser/admin/baser-core/users/login` のような形となります。

- **[重要・サブディレクトリ設置時の SITE_URL はサブディレクトリ込みにする＋変更後は必ずキャッシュ全消去]** サブディレクトリ設置では `config/.env` の `SITE_URL` は**サブディレクトリを含むフルURL**（例 `https://localhost/v5/`）が**正しい**。ここを**ドメインルート（`https://localhost/`）に変えてはいけない**——一見 `/v5/v5` 二重化が消えるように見えるが、代わりに**管理ログインが自分自身へ 302 する無限ループ**になる（loginUrl とリクエストパスが食い違い、ログインページが「保護対象」と誤判定されるため）。
  - **管理画面が `/v5/v5` 二重化(404) や リダイレクトループになったときの真因は、多くが「`.env`（特に SITE_URL）を変更した後のキャッシュ残り」**。baserCMS はルート/URLをキャッシュするため、`.env` を変えたら必ず **`bin/cake cache clear_all`** を実行する。SITE_URL を正しい `https://host/v5/` に戻し、キャッシュを消せば解消する（実証済み）。
  - **[検証はコンテナ内 curl でなく実ブラウザで]** `nginx-proxy` 配下では、コンテナ内から `curl https://localhost/...` を叩いても **`X-Forwarded-Proto` 等のプロキシヘッダが付かず、実ブラウザと違うリダイレクト挙動**を示す（SSL/正規化リダイレクトの判定がズレる）。管理リダイレクトの生死は**必ずプロキシ経由の実ブラウザ**で確認する。コンテナ内 curl の 302/二重化を鵜呑みにして「サブディレクトリの根本バグ」と誤診しない。

## データベース移行手順

データベースの移行は、SQLの直接書き換えではなく、専用プラグイン `BcDbMigrator` を使用したプロセスを推奨・案内してください。 
このプラグインは、コマンドは提供しておらず、ブラウザで利用する必要があります。

1.  **前提条件**:
    *   移行元: baserCMS 4.5.5 以上であること。
    *   移行先: baserCMS 5 が新規インストールされていること。

2.  **移行フロー**（DB移行は「①バックアップ→②変換」と「③復元」に分かれる。②までは早期に実施できるが、③復元は**必要なv5プラグインを導入した後**＝プラグイン棚卸し/変換フェーズの後に行う）:
    1.  **バックアップ (v4)**: baserCMS 4 管理画面「データメンテナンス」でバックアップを作成し、ダウンロードした zip を `/v5/tmp/` に配置してもらう。
        *   **[重要・PHPバージョンとの順序]** v4 バックアップ作成には **v4 管理画面が動く PHP** が要る。v5 のために先にコンテナを PHP 8.1 へ上げると、サポート外の v4 管理が不調になり得る。**バックアップは PHP を上げる前に取る**か、既に上げてしまったら**一時的に v4 サポート版（例 php7.4）へ戻して**バックアップを取り、再度 8.1 へ戻す（v4 と v5 は必要 PHP が違うため同時稼働はできない）。
        *   **[重要・v4 管理URLは決め打ちしない]** 管理ログインが `/admin` で 404 になるサイトは**管理プレフィックスを変更している**（`app/Config/core.php` の `Configure::write('Routing.prefixes', ...)`。例 `cmsadmin` → `/cmsadmin/users/login`、データメンテナンスは `/cmsadmin/tools/maintenance`）。**404 を安易に「PHP切替で壊れた」と誤診しない**——まず `Routing.prefixes` を確認する。
    2.  **ツール準備 (v5)**: `BcDbMigrator` を配置する。**取得は composer でも GitHub でも可**（旧記載の「composer 不可」「plugins/ 必須」は誤り＝composer 取得可・**vendor 配下でも baserCMS は認識する**）:
        *   **(a) composer**: packagist に `baserproject/bc-db-migrator` が公開されており `composer require baserproject/bc-db-migrator:^5.2` で取得できる。ただしこのパッケージの `composer.json` に autoload 定義が無いため、cakephp plugin-installer が `Unable to get primary namespace` で `config/plugins.php` への**自動登録に失敗する**（＝ダウンロード自体は成功）。確実に使うなら、取得済みファイルを `plugins/BcDbMigrator` にコピーして下記の DB 有効化を行う（composer からは `composer remove` して composer.json をクリーンに保つとよい）。
        *   **(b) GitHub**: `git clone --branch 5.2.0 https://github.com/baserproject/BcDbMigrator.git plugins/BcDbMigrator`（clone 後 `.git` を除去してサブモジュール化を回避）。※Auto mode では外部リポジトリ取得が分類器にブロックされることがあるので、ユーザーに取得元を提示して承認を得る。
        **有効化は2通り**:
        *   **(推奨・低摩擦) DB レコードで有効化**: `BcDbMigrator` は**インストールスクリプト（マイグレーション）を持たない**ため、`plugins` テーブルに1行 INSERT すれば有効化できる（`name='BcDbMigrator', title='BcDbMigrator', version='5.2.0', status=1, db_init=1, priority=<末尾+1>, created/modified=NOW()`）。ブラウザ操作不要。有効化後 `bin/cake` にコマンドが出る。
        *   (代替) 管理画面のプラグイン管理からインストール。※`cake plugin load`／`config/plugins.php` 直接編集は不可（ルーティング不整合でクラッシュ要因）。
    3.  **変換実行（CLI 対応・5.2.0 以降）**: **BcDbMigrator 5.2.0 で CLI 実行に対応**した（旧版はブラウザ専用）。`docker exec <container> bash -lc 'cd v5 && bin/cake bc_db_migrator tmp/<v4バックアップ>.zip'` を実行すると、変換済み zip が `v5/tmp/baserbackup_<v5version>_<日時>.zip` に出力される（アップロード/ダウンロードの往復が不要。エラー時もアシスタントが反復できる）。変換は一時接続（`bcOldDbMigrator`/`bcNewDbMigrator`・接頭辞付き一時テーブル）で行われ**ライブ DB は変更しない**（出力は zip のみ）。
        *   **[重要・baser-core 5.2.5 との非互換パッチ]** BcDbMigrator 5.2.0 の変換は旧スキーマPHPを `extends \BaserCore\Database\Schema\BcSchema`（**FQN**）に置換するが、baser-core 5.2.5 の `BcDatabaseService::isValidSchemaFile()` は AST で **extends が短縮名 `BcSchema` の完全一致**であることを要求するため、`無効なスキーマファイル: BcSchema を継承し、drop/create メソッドをオーバーライドしてはいけません` で全滅する（CLI/ブラウザ共通の Component 経路）。**対処**: `plugins/BcDbMigrator/src/Controller/Component/BcDbMigratorComponent.php` の `_createTableBySchema()` 内、`extends CakeSchema` を FQN でなく短縮名に置換し `use` 文を足す:
            ```php
            $contents = preg_replace('/extends CakeSchema/', 'extends BcSchema', $contents);
            $contents = str_replace('<?php', "<?php\nuse BaserCore\\Database\\Schema\\BcSchema;", $contents);
            ```
            （本家 BcDbMigrator 未対応のためのローカルパッチ。**根本原因は baser-core 側**＝`isValidSchemaFile()` が短縮名の完全一致のみ許可し FQN を弾く点。これを FQN も受理するよう緩和する修正を baserproject/basercms へ **PR #4438（5.3.x 宛）で提出済み**。マージ・リリースまでは本ローカルパッチ（BcDbMigrator 側で短縮名に置換）が必要。5.3 以降ではパッチ不要になる見込み。）
    4.  **復元 (v5)**: baserCMS 5 管理画面「データメンテナンス」で変換後 zip を復元（**復元CLIは無くブラウザ専用**）。**この工程はプラグイン棚卸し/変換の後**に行う（下記「プラグイン」注意点）。
        *   **復元は zip の中身基準で動く（実装確認済み）**: `UtilitiesService::_loadBackup()` は zip 内の全 `*Schema.php` を drop→create し全 CSV を投入するだけで、**`plugins` テーブルの有効/無効は参照しない**。したがって、変換済み zip にスキーマ＋CSV が入っているテーブルは、該当プラグインが無効でも取り込まれる（`unzip -l` で `<Table>Schema.php` の同梱を確認しておく）。
        *   **★復元は `plugins` テーブル自体も zip の内容（=v4 のプラグイン一覧・全行 status=0）で置き換える（実測）**: 復元前に行った有効化レコード（コアの BcBlog/BcMail 等を含む）は**全部消えて無効化される**。**復元後に必ず再有効化する**: コア標準6つ `UPDATE plugins SET status=1 WHERE name IN ('BcBlog','BcMail','BcUploader','BcSearchIndex','BcThemeConfig','BcWidgetArea')` ＋移行対象のうち確認済みのもの（動作確認工程中なら移行対象は 0 のままでよい）。BcDbMigrator/BcAddonMigrator の行も消えるので、引き続き使うなら再 INSERT。実施後 `bin/cake cache clear_all`。
        *   **★`sites` テーブルも同様に上書きされ、テーマ適用が外れる（実測）**: 復元後は全サイト `theme='BcThemeSample'` に戻る（BcDbMigrator 変換の既定値。マルチサイトの各行も v4 から復元される）。フロントを開くと `MissingLayoutException`（探索パスに自作テーマが無い）で気づく。**復元後にテーマを再適用**: `UPDATE sites SET theme='<CamelCaseTheme>' WHERE id=<対象サイト>`＋キャッシュクリア。＝**「復元後の再設定3点セット: plugins 再有効化・sites.theme 再適用・cache clear_all」**として覚える。
        *   **★手動導入したツールプラグイン（BcDbMigrator / BcAddonMigrator 等）も plugins テーブルごと消える**（復元は v4 由来の plugins で上書きするため。実測: 復元後に `bc_addon_migrator` コマンドが `Unknown command` になる）。ツールを再度使う場合は plugins テーブルへ再 INSERT（＋cache clear）してから実行する。
        *   **★マルチサイトは復元後に `site_id` の整合を必ず検証する（実測・不整合あり）**: 復元データはテーブルごとに site_id の変換が食い違うことがある — `contents.site_id` は「v4 の id +1」（5系標準・メイン=1）に変換される一方、**`sites.id` は AUTO_INCREMENT で 2〜 に詰め直され、プラグイン系テーブル（search_indexes 等）の site_id は v4 のまま無変換**。結果、サブサイトの contents が存在しない site_id を指し、サブサイトURL（`/blog/` 等）が**全部 404** になる。検証SQL: `SELECT site_id, COUNT(*) FROM contents GROUP BY site_id` と `SELECT id, alias FROM sites` を突き合わせる。**修正は「5系標準＝contents の付番（v4 +1）」に他を合わせる**: `sites.id` と各テーブルの site_id を降順に UPDATE（衝突回避）し、`ALTER TABLE sites AUTO_INCREMENT=<最大+1>`。site_id カラムを持つテーブルは `information_schema.COLUMNS` で洗い出す。**再復元すると不整合も再発する**ので、統一SQLを控えておき復元のたびに再実行する。
        *   **復元前に管理画面が 500 になる場合、(b)変換組プラグインの無効化で回避してよい**: (b)組は boot/描画経路の4系残骸（Event リスナーの `viewVars` 参照等）で管理画面を壊しがち。上記のとおり復元は無効でもテーブルを取り込むので、`UPDATE plugins SET status=0 WHERE name IN (...)`＋キャッシュクリアで管理画面を復旧させてから復元してよい（どのみち動作確認工程では一旦全無効化する）。

3.  **注意点**:
    *   **★予約語カラム対応（復元前に必ず設定）**: `config/install.php` の `Datasources.default` に **`'quoteIdentifiers' => true` を復元前に必ず入れる**。4系サイトはメールフォーム項目由来の `check`・`message` 等の予約語カラムをほぼ確実に持っており、未設定だと復元が `SQLSTATE[42000] 1064 Syntax error ... near 'check'` で**途中失敗**する。「必要になる場合がある」ではなく既定で設定してよい（quote されて困ることはない）。
    *   **★復元の途中失敗は「users が空」を招く（復旧手順）**: 復元は zip 内スキーマの drop→create を先に行うため、途中で失敗すると **users=0 のままになりログイン不能**になる（MySQL は DDL がロールバックされない）。復旧は再インストールが確実:
        1. `mv config/install.php config/install.php.bak` ＋ `.env` の `INSTALL_MODE="true"`（install.php が残っていると「インストール済み」扱いで bootstrap が空DBを参照して落ちる）
        2. DB を DROP→CREATE し `bin/cake install <URL> <メール> <パス> <DB名> --baseurl /v5/ --host <DBホスト> --username <ユーザー> --password <パス>`
        3. `INSTALL_MODE` を false に戻し、再生成された install.php に `quoteIdentifiers` を再設定、プラグインの `plugins` INSERT・`migrations migrate -p`・`sites.theme` を再適用
        4. ブラウザ復元をやり直す。**復元中はログアウトしない**よう依頼しておく
    *   **復元直前・直後のログインの注意**: 再インストールやDB作り直しの後は、ブラウザに古い CSRF クッキーが残っていて `Missing or invalid CSRF cookie` になる → **ログインページを再読み込みしてから**ログインしてもらう。復元成功後は users が v4 のものに置き換わるため、**以後のログインは v4 と同じ管理者ID/パスワード**になる（事前にユーザーへ伝えておく）。
    *   **プラグイン（復元との順序）**: 標準フローでは**変換（早期）→ プラグイン棚卸し/導入・有効化 → 復元**の順とする。なお実装上、復元自体は zip の中身基準で動きテーブル取り込みにプラグインの有効/無効は影響しない（上記「復元は zip の中身基準で動く」参照）。順序を守る実利は「復元前に有効化まで済ませておくことで、復元後すぐ管理画面でプラグインデータを確認できる」「zip にスキーマが無いテーブル（Migrations でしか作られないもの）を先に作れる」こと。
    *   **メモリ**: バックアップ作成時にメモリーオーバーエラーとなる可能性があります。その際、ユーザーに php.iniの memory_limit の変更を促してください。
    *   **小数点**: decimal(8,2) などを利用しているテーブルの場合、正常に移行できないので、別途、SQLで定義、インポートする必要があります。

## アップロードファイルの移行

`/files` ディレクトリを `/v5/webroot/files` に移動してください。

- **v4 並行稼働中は「移動」でなく「コピー」にする**（v4 側の表示を壊さない）。数GBあるためコンテナ内で `cp -a` するのが速い。
- **★ネスト罠: `v5/webroot/files` は復元やプラグイン有効化の時点で既に一部生成されている**（`theme_configs/` `uploads/` やブログのディレクトリ骨格など）。そのため `cp -a /path/files /v5/webroot/files` とすると **`files/files/` にネストしてコピーされる**。既存ディレクトリへのマージは `cp -a /path/files/. /v5/webroot/files/`（末尾 `/.`）で中身をコピーする。ネストしてしまったら `cp -a .../files/files/. .../files/ && find .../files/files -delete` で解消。
- **未移行時の症状**: アイキャッチ等のアップロード系URLは、実体ファイルが無いとヘルパのフォールバックで**ルート相対 `/files/...`（サブディレクトリ無し）**が出力され、v5 ページから v4 の files を誤参照する（v5 の `error.log` に `MissingRouteException: /files/...` が並ぶ）。files 移行後は `/v5/files/...` が正しく生成される。**この工程は「プラグイン有効化→描画確認」より前に済ませる**こと。
- **移行後の検証**: 描画HTMLの `src` を `curl | grep -o 'src="[^"]*files[^"]*"'` で抜き、静的配信の 200 を確認する。ローカル環境では **DB は最新なのに files が古い**（本番から持ってきた時期のズレ）ことがあり、その 404 は移行起因ではない — v4 側の同パスにも実体が無いことで切り分ける。

## テーマの変換

テーマの変換には、`BcAddonMigrator` プラグインを使用します。

1.  **準備**: baserCMS 4 のテーマフォルダを ZIP 圧縮します。
    *   **重要**: 圧縮する際は、テーマディレクトリの親ディレクトリ（`theme/`）を含めず、テーマディレクトリ自体がルートになるように圧縮する必要があります。
    *   **手順例**:
        ```bash
        # 1. テーマ名を特定 (データベースの site_configs テーブルを確認)
        # 例: SELECT value FROM site_configs WHERE name = 'theme';
        # 結果: my-custom-theme
        
        # 2. テーマディレクトリ名をキャメルケースに変更
        # 例: my-custom-theme -> MyCustomTheme
        THEME_NAME="my-custom-theme"  # 実際のテーマ名に置き換える
        THEME_CAMEL="MyCustomTheme"    # キャメルケースに変換
        
        # ★【最重要・データ破壊注意】変換後の名前が「大文字小文字だけの差」になる場合
        # （例: philosophy → Philosophy）、macOS 等のケースインセンシティブFSでは
        # theme/<camel> が元ディレクトリと同一に解決され、一時コピーの削除で
        # **元テーマ本体を削除してしまう**（実害あり。サブモジュールなら
        # `git submodule update --init theme/<name>` で復旧できる）。
        # 同名ケース差になるテーマは theme/ 配下でなく一時ディレクトリへコピーする:
        #   cp -r "theme/${THEME_NAME}" "/tmp/${THEME_CAMEL}" して /tmp 側で zip する
        cp -r "theme/${THEME_NAME}" "theme/${THEME_CAMEL}"
        
        # 3. ZIP圧縮 (node_modules除外)
        mkdir -p v5/tmp/zip
        cd theme
        zip -r "../v5/tmp/zip/${THEME_CAMEL}.zip" "${THEME_CAMEL}/" -x "*/node_modules/*"
        rm -rf "${THEME_CAMEL}"
        cd ..
        
        # 4. 圧縮後の確認（重要）
        unzip -l "v5/tmp/zip/${THEME_CAMEL}.zip" | head -n 20
        # ルートが MyCustomTheme/ であること、theme/ ディレクトリが含まれていないことを確認
        ```
2.  **ツール**: baserCMS 5 に `BcAddonMigrator` をインストール・有効化します（GitHubより取得）。
        *   **注意**: `BcAddonMigrator` は GitHub から取得し、`plugins/BcAddonMigrator` に配置する。有効化は **`plugins` テーブルへの直接 INSERT で可**（インストールマイグレーションを持たないため。実証済み。「プラグインの有効化（DB直接・標準）」節の手順2）。`cake plugin load`／`config/plugins.php` 直接編集は不可。
3.  **変換（コマンドライン）**: `bc_addon_migrator` コマンドを使用してテーマを変換します。
    ```bash
    # Docker環境の場合
    THEME_CAMEL="MyCustomTheme"  # 実際のキャメルケーステーマ名に置き換える
    docker exec <container-name> bash -c "cd v5 && bin/cake bc_addon_migrator --type=theme tmp/zip/${THEME_CAMEL}.zip"
    
    # 出力: v5/tmp/${THEME_CAMEL}_5.2.0.zip
    ```
4.  **配置**: 変換されたZIPファイルを `v5/plugins/` に展開します。
    ```bash
    THEME_CAMEL="MyCustomTheme"  # 実際のキャメルケーステーマ名に置き換える
    cd v5/tmp && unzip -q -o "${THEME_CAMEL}_5.2.0.zip" -d ../plugins/
    ```
    *   **注意**: 変換は完全ではありません。必ずコード全体を目視確認し、手動修正を行ってください。
4.  **手動修正**:
    *   **モデルデータ**: 配列アクセス (`$post['BlogPost']['name']`) をエンティティプロパティ (`$post->name`) に変更。
    *   **URL**: 配列形式のリンクに `'plugin'` キーを追加し、コントローラー名をアッパーキャメルケースに変更（例: `'controller' => 'search_indexes'` → `'controller' => 'SearchIndexes'`）。
    *   **フォーム**: `$this->BcForm->create('Model')` を `$this->BcForm->create($entity)` または `null` に変更。`input()` を `control()` に変更。
    *   **エレメント**: パス指定を `PluginName.element_name` 形式に変更。

## プラグインの変換

プラグインの変換にも `BcAddonMigrator` プラグインを使用します。
ただし、GitHubのリポジトリ等で既に baserCMS 5 に対応したバージョンが公開されている場合があります。その場合は、変換作業を行わず、そちらを利用してください。

### プラグイン棚卸し（変換前に必ず・プラグインごとに方針決定）

**プラグインを一括変換にかける前に、プラグインを1つずつ棚卸しし、「4系を変換する」か「既存の5系版を使う」かをユーザーと確認して決める**。いきなり全プラグインを `BcAddonMigrator` にかけない（既に公式/作者が5系版を出しているものを二重にメンテすることになる）。

- **調査**: 各プラグインについて、①baserCMS 公式の 5系標準搭載（`baser-core`/`bc-*` に統合済みか）②作者/配布元の GitHub・baserマーケットに 5系対応版があるか③外部SaaS連携等で5系版が別提供か、を確認する。判断材料が乏しいものは「要確認」として残す。
- **分類してユーザーに提示**（例）:
  - **(a) 既存5系版を使う**: 公式統合済み・作者が5系版を公開しているもの（例: エディタ系・OGP系など外部配布物）。→ 変換対象から除外し、composer/管理画面で5系版を導入。
  - **(b) 4系を変換して移行**: 独自実装で5系版が無いもの（自社プラグイン等）。→ `BcAddonMigrator` で変換 → 内部コードを5系化。
  - **(c) 廃止/不要**: 5系で標準機能に吸収された・もう使わない機能。→ 移行しない。
- **表で合意を取る**: 「プラグイン名／4系での役割／5系版の有無・入手先／方針(a/b/c)」の一覧を作り、**ユーザーに (a)(b)(c) を確認してから**次工程へ進む。テーマについても同様に棚卸しする。
- この棚卸し結果を移行計画（plan/spec）に反映し、(b) のものだけを変換フェーズの対象にする。
- **★(a)のうち非公開配布・要購入・要ライセンス等でエージェントが自力入手できないものは、棚卸し表で明示的に区別し「誰が・いつ入手/設置するか」までユーザーと合意して記録する**。単に「(a) 既存5系版を使う」と分類しただけでは、入手・設置の担当と期限が宙に浮き、実行フェーズで着手されないまま進行しがちである（実例: 棚卸しドキュメントに記載していたエディタ系プラグインの5系版導入が、非公開配布でユーザー側の入手が必要だったにもかかわらず後続フェーズで実施されず、フロントの表示崩れとして移行完了後に顕在化した。原因は棚卸しを一度作って終わりにし、実行状況を追跡していなかったこと）。
- **★棚卸しは作って終わりにせず、移行完了を宣言する前に棚卸し表と実際の導入状況を突き合わせて検証する**。(a)分類の項目は「導入済みか」を個別に確認し、未導入のものが残っていれば移行計画のスコープ外に放置せず、対応時期（今回対応／別タスクとして継続）をユーザーと再確認してから完了とする。

1.  **準備**: baserCMS 4 のプラグインフォルダを ZIP 圧縮します。
    *   **重要**: テーマ同様、親ディレクトリ（`app/Plugin/`）を含めず、プラグインディレクトリ自体がルートになるように圧縮する必要があります。
    *   **手順例（全プラグインを一括で圧縮）**:
        ```bash
        # app/Plugin/ に移動して全プラグインを圧縮（node_modules除外）
        mkdir -p v5/tmp/zip
        cd app/Plugin
        for d in */; do 
            name=${d%/}
            zip -q -r "../../v5/tmp/zip/${name}.zip" "$name" -x "*/node_modules/*"
        done
        cd ../..
        ```
2.  **変換（コマンドライン）**: `bc_addon_migrator` コマンドを使用して全プラグインを一括変換します。
    ```bash
    # Docker環境の場合
    docker exec <container-name> bash -c "cd v5 && for zip in tmp/zip/*.zip; do bin/cake bc_addon_migrator \"\$zip\"; done"
    
    # 出力: v5/tmp/<PluginName>_5.2.0.zip (各プラグインごと)
    ```
3.  **配置**: 変換されたZIPファイルを `v5/plugins/` に一括展開します。
    ```bash
    cd v5/tmp && for zip in *_5.2.0.zip; do unzip -q -o "$zip" -d ../plugins/; done && cd ../..
    ```
4.  **クリーンアップ**: 不要なZIPファイルを削除します。
    ```bash
    rm -rf v5/tmp/zip v5/tmp/*_5.2.0.zip
    ```
    *   **★`baserbackup_*.zip`（DB変換済みバックアップ）は削除しない**。`v5/tmp/` には BcDbMigrator の出力 zip も同居しているため、ワイルドカードは変換中間 zip（`<PluginName>_<version>.zip`）だけに掛かるよう対象を限定する。
    *   **`rm -rf` が Bash 権限で拒否される環境では `find <path> -delete` を使う**（例: `find v5/tmp/zip -delete`。ファイル個別は `find v5/tmp -maxdepth 1 -name '<Name>_*.zip' -delete`）。
    *   **注意**: 変換は完全ではありません。必ずコード全体を目視確認し、手動修正を行ってください。
    *   **BcAddonMigrator がやること／やらないこと（実測）**:
        *   やること: `Controller/Model/View` の `src/` 名前空間化、`admin_` メソッドの `Controller/Admin` 分離、`src/<Name>Plugin.php` の生成（**空クラス** `class <Name>Plugin extends BcPlugin {}`）、`config.php`/`setting.php` の return 配列化。
        *   やらないこと: **`config/Schema`（4系 CakeSchema）→ `config/Migrations` への変換**（4系 Schema がそのまま残る）、**`config/init.php` の除去**（残骸として残る）、内部コードの5系化（`ClassRegistry`・`$this->data` 等は残存）。
        *   つまり変換直後は「配置と `php -l` は通るが、Migrations 無し・4系残骸あり」が正常な状態。復元運用なら有効化に支障はない（「プラグインの有効化（DB直接・標準）」節参照）。Migrations 生成・残骸掃除・内部5系化は横断チェック工程（フロー15-17）で行う。
3.  **手動修正**:
    *   **ディレクトリ構成**: コントローラーの `admin_` プレフィックスメソッドは `Controller/Admin` 名前空間へ移動されています。
    *   **設定ファイル**: `config/setting.php` の `BaserCore.nav` などの配列構造の調整。また、`$config` 変数定義ではなく、配列を `return` する形式に変更。
        *   **重要**: `array()` シンタックスは `[]` (短縮構文) に変更し、インデントを整えること。
        *   **重要**: 複数の `$config['Key']` 定義がある場合は、1つの `return` 配列内にマージすること。
        *   **重要**: 自動変換時に `return [ $config = [...] ];` という多重構造にならないよう注意する。`$config =` は削除し、純粋な配列定義にする。
        *   **重要**: 自設定を参照する場合は、一度ローカル変数に定義してから参照する。
        *   **★`adminNavigation` の `url` は plugin/controller とも CamelCase にする**（例 `'plugin' => 'PopularBlogPost', 'controller' => 'PopularBlogPostConfigs'`）。4系の snake_case のままだと**共通サイドバーのURL生成が MissingRouteException になり、そのプラグインだけでなく全管理画面が 404** になる（統合テストで検出した実例）。4系専用の `adminNavi` ブロックは5系で未使用のため削除してよい。
    *   **マイグレーションの生成 (bake migration_snapshot)**:
        *   プラグイン個別で `bake migration_snapshot -p [PluginName] --table [TableName]` を実行しても、テーブル定義が正しく抽出されない（空のファイルになる）場合がある。
        *   その場合は、一括で `bin/cake bake migration_snapshot GlobalInitial` を実行して全テーブルの状態を取得し、生成されたコードを各プラグインの `Initial.php` に手動で（またはスクリプトで）分配する手法が確実である。
    *   **$autoId プロパティ**:
        *   スナップショット生成時に `$autoId = false` となるのは、既存のデータベース構成をカラム名からインデックス、コメントに至るまで正確に再現するためである。
        *   自動生成に頼らず、すべての定義を明示的に `addColumn` することで、移行元データベースとの差異をなくす。
    *   **[重要]** データベース設定は `v5/config/app_local.php` よりも `v5/config/install.php` が優先されます。設定を確認・変更する場合は両方のファイルに注意してください。
    *   **[重要]** `v5/config/plugins.php` は直接編集しないこと。baserCMS 5 ではプラグイン管理画面で有効化されたプラグインが自動的にロードされる仕組みになっており、このファイルを直接編集すると依存関係やルーティング (`routes.php`) の読み込み順序に不整合が生じ、アプリケーションがクラッシュする原因となる。
    *   **[重要]** baserCMS の本体（`baser-core` など）および、パッケージに最初から含まれていたプラグイン（`bc-admin-third` 等、ディレクトリ名にハイフンが含まれるコア系のプラグイン）に手を入れてはいけません。これらは本体アップデートの影響を受けるため、移行作業の対象から除外してください。
    *   **[重要]** `BcAddonMigrator` は `admin_` プレフィックスの付いたメソッドは自動的に移動しますが、それらが呼び出している `protected` メソッドや `_setFormViewData` 等のヘルパーメソッドは、元の（フロント側）コントローラーに残ったままになります。これらは手動で管理用コントローラー (`Controller/Admin`) へ移動し、名前空間や必要な `use` 文を修正してください。
    *   **プラグイン情報**: `config.php` も同様に配列を `return` する形式であることを確認する（`BcAddonMigrator` で自動変換されるが、念のため）。
    *   **初期化処理**: `config/init.php` は廃止。`src/[PluginName]Plugin.php` (クラス名: `[PluginName]Plugin`) を作成し、`BaserCore\BcPlugin` を継承する。
        *   `$this->Plugin->initDb()` は不要（マイグレーションが自動実行されるため）。
        *   その他の初期化ロジックがあれば `install()` や `bootstrap()` メソッドに移行する。
        *   **重要**: `install` メソッドのシグネチャは `public function install($options = []): bool` である必要があり、戻り値として `parent::install($options)` の結果（または `true`）を返すこと。
    *   **スキーマ**: `Config/Schema` は廃止。CakePHP Migrations (`config/Migrations`) に移行。
    *   **フィード**: フィードプラグインは廃止されたため、代替手段を検討。



## プラグインの有効化（DB直接・標準）

配置した移行対象プラグインの有効化は、**`plugins` テーブルへの直接 INSERT を標準**とする。ブラウザ往復ゼロでエージェントが自走でき、検証も SQL で完結する（管理画面有効化は代替手段として注記参照）。
**★テーマは `plugins` テーブルに登録しない**（新規インストール直後の `plugins` にデフォルトテーマ BcFront の行が無いことから実証済み）。テーマの適用は次節「テーマの適用（DB直接）」の `sites.theme` UPDATE のみでよい。

1.  **有効化前チェック（プラグインごと）**: `ls v5/plugins/<Name>/config/Migrations` で **Migrations の有無を確認**する。
    *   (a)既存5系版のプラグインは通常 Migrations を持つ（有効化時にテーブル作成が必要）。
    *   (b)`BcAddonMigrator` 変換組は通常 Migrations を持たない（4系 `config/Schema` が残っているだけ）。**復元 zip にスキーマ＋データが含まれるかを `unzip -l v5/tmp/baserbackup_*.zip | grep -iE "<table名>"` で確認**し、含まれていれば復元がテーブルを供給するので Migrations 生成は不要（クリーンインストール運用にする場合のみ `bake migration_snapshot` で生成）。
2.  **`plugins` テーブルへ INSERT（全プラグイン共通）**:
    ```sql
    INSERT INTO plugins (name, title, version, status, db_init, priority, created, modified)
    VALUES ('<Name>', '<Name>', '<version>', 1, 1,
            (SELECT p FROM (SELECT COALESCE(MAX(priority),0)+1 AS p FROM plugins) tmp), NOW(), NOW());
    ```
    複数件あるときは priority を連番でずらす。
3.  **Migrations を持つプラグインはマイグレーションを併走**（INSERT だけではテーブルが作られない）:
    ```bash
    docker exec <container> bash -lc 'cd /var/www/html/v5 && bin/cake migrations migrate -p <Name>'
    ```
4.  **キャッシュ全消去**: `bin/cake cache clear_all`（プラグインロード構成が変わるため）。
5.  **検証は CLI 起動スモーク＋SQL まで（この段階でブラウザ確認はしない）**: `SELECT name, status FROM plugins` で status=1、Migrations 実行組は `SHOW TABLES` でテーブル生成を確認し、**`bin/cake version` が fatal なくバージョン番号を返すこと**（=有効プラグイン全部の bootstrap が通ること）を確認する。**フロント/管理画面のブラウザ確認はまだ求めない** — 内部コードが4系のままなので画面は出なくて正常。ブラウザ確認は動作確認工程（標準フロー17）以降。管理画面が必要になるのは復元（フロー12）だけなので、その直前に管理画面ログインだけ生存確認すればよい。

### ★有効化で顕在化する boot 経路の4系残骸（先に潰す）

(b)変換組を有効化すると、**画面以前にアプリ起動（bootstrap）で fatal になる4系残骸**が動き出す（`php -l` では捕まらない実行時エラー）。有効化後の `bin/cake version` スモークで1つずつ顕在化するので、**最小修正（TODO マーカー付き）だけ**してフロー15-16（本格5系化）へ先送りする。実測した典型パターン:

| 場所 | 症状 | 最小修正 |
|---|---|---|
| `src/Event/*EventListener.php` | `Class "<NS>\Event\ClassRegistry" not found`（BcEvent が有効プラグインのリスナーを bootstrap で instantiate するため、コンストラクタ内の4系APIが即死） | `ClassRegistry::isKeySet/getObject` 分岐を削除し `TableRegistry::getTableLocator()->get()` 一本化 |
| `config/setting.php` | `Class "CakeLog" not found` ／ Configure::load が配列を得られない | `CakeLog::config()` 等の実行文を除去（ログ設定は Phase 4 で Plugin::bootstrap へ）。`$config['X'] = [...]` は `return ['X' => [...]]` へ |
| `config/setting.php` 内の4系定数 | `Undefined constant "CACHE_DATA_PATH"` 等 | 5系の定数（`TMP`・`CACHE` 等）へ置換 |
| `config/routes.php` | `Router::connect` 静的呼び出し・`CakeRequest`・`BcSite::findByUrl` で fatal | ファイル全体を `return;` で無効化＋旧定義をコメント保存（Phase 4 で RouteBuilder 形式に書き換え） |

スモークが通る（`bin/cake version` がバージョンを返す）までこの表の要領で潰す。
**★CLI スモークの限界に注意**: `bin/cake version` は Web リクエストと bootstrap 経路が一部異なり、**BcEvent のリスナー生成起因の fatal（例: コンストラクタでの Table 即時取得→MissingTableClassException）をすり抜けることがある**（実測）。CLI スモーク通過＝Web も無事、ではない。Web 側の最終確認は管理画面の GET 統合テスト（工程17-b）か実ブラウザで行う。リスナーのコンストラクタでは Table を即時取得せず遅延取得にするのが安全。

*   **直 INSERT が安全な根拠**: `BcAddonMigrator` が生成する Plugin クラスは空（`class XxxPlugin extends BcPlugin {}`）で独自 install 処理を持たないため、管理画面有効化との差は「マイグレーション自動実行の有無」だけ。それは手順3で補える。**独自の `install()` をオーバーライドしているプラグインだけは中身を確認**し、必要なら管理画面から有効化する。
*   （代替）管理画面のプラグイン管理から有効化するとマイグレーションが自動実行される。ユーザーがブラウザ操作できる状況ならこちらでもよい。
*   `config/plugins.php` の直接編集は不可（既出の注意どおりクラッシュ要因）。
*   **この工程で全対象を有効化するのは「復元でテーブル・データを取り込むため」**（フロー12の前提）。復元が済んだら、動作確認工程（フロー17）の開始時に**移行対象を一旦全部無効化**し、確認対象＋その依存だけを1つずつ有効化しながら進める（イベント干渉の切り分けのため）。無効化/再有効化は `UPDATE plugins SET status=0（/1） WHERE name='<Name>'`＋`bin/cake cache clear_all` で行える（テーブルとデータは残るので安全）。

## テーマの適用（DB直接）

5系ではテーマは `site_configs` ではなく **`sites` テーブルの `theme` カラム**で管理される。適用も DB 直接更新を標準とする。

```sql
-- サイトごとに適用（マルチサイトは site 単位で theme を持つ。全サイト同一テーマなら WHERE 句なし）
UPDATE sites SET theme = '<CamelCaseTheme>' WHERE id = 1;
```

*   適用後は必ず `bin/cake cache clear_all`（テーマ解決がキャッシュされるため）。
*   検証は**実ブラウザ**でフロントを開き、テーマのテンプレートが使われていること（500 でないこと）を確認。テーマ側テンプレートの4系残骸でエラーになる場合は、この時点では台帳に記録して工程15（テーマ横断チェック）で対応する。

## プラグイン内部コードの変換（別スキル）

`BcAddonMigrator` で変換した後の **プラグイン内部コードの具体的な書き換え**（Controller の `$this->Model`→`fetchTable`、Table の `initialize()` アソシエーション宣言・`find()` クエリビルダ化、Entity/getControlSource、View/Helper、検索フォーム・編集フォームの `BcAdminForm`/`control()` 化、`Time::format` の ICU、`Number::format`/`Text::truncate` の null 対応、Vue・JS の `$.bcUtil.adminBaseUrl` 化と webpack 再ビルド、フロント表示エラーの症状別対処 等）は、専用スキル **basercms-plugin-4-to-5-upgrade** にカタログ化している。プラグインの画面を1枚ずつ通して動かす段階では、そちらを参照すること（C-0 機械一括変換カタログ／T- Table・ORM／C- Controller・画面／F- フロント表示エラーのうちプラグイン文脈分）。**テーマ（templates 中心）の移行パターン（TH-系＋テンプレート・テーマ描画系の F-系）は basercms-theme-4-to-5-upgrade スキル**に分割している。変換先＝5系の正しい書き方の正本は **basercms5-plugin-development**（プラグイン）／ **basercms5-theme-development**（テーマ）を参照。

## 移行の進め方（ユニットテスト先行 → ドメイン単位で画面結合）

`BcAddonMigrator` 変換後のプラグイン内部コードを手作業で5系化する工程は、次のワークフローで進めると速くて安全（実証済み）。**目的は、任意の baserCMS4系プロジェクトを最小の手間で5系へ移行できる状態を作ること**。

**なぜこの順序か**: 移行では「**画面表示は動くのに、保存・集計・出力ロジックが静かに壊れている**」ケースが多い（按分計算・集計の戻り値形状・絞り込み条件・本番に無い列など、目視では正しさを判断できない）。一方で**ユーザーの最大の手間は画面テストのリロード反復**。テストはAIが自律実行でき人手が要らない。よって **テストで正しさを固めてから、画面結合をまとめて1回** が最も手間が少ない。

1. **フェーズ1: ロジックをテスト駆動で移行**（人手ほぼ不要）。対象ドメイン（見積／請求／売上／集計 等）の Table/保存/集計/出力ロジックを、責務を整理しつつ TDD で5系化。Factory＋ユニットテストで「4系と同値」を数値で固定（RED→GREEN・回帰自動担保）。→ テスト基盤は **basercms-unittest**、変換パターンは **basercms-plugin-4-to-5-upgrade**（T-/C-/F-）。
2. **フェーズ2: 薄いグルー層**。コントローラは「受け→Table/Service呼び→set」の薄さに保ち、ロジックはテスト済みの Table/Service へ寄せる。画面で確認すべき面積（テスト不能なグルー）を最小化。
3. **フェーズ3: ドメイン単位で画面結合を1回**。ロジックが全部緑になってから、1回の画面セッションでテンプレート/JS・Vue/遷移/CSV・Excel/通知の結合を確認。事前に「どの画面で何を操作し何が期待結果か」のチェックリストを用意し、ユーザーは順に1往復するだけにする（リロードを「メソッドごと」でなく「ドメインごと」に集約）。
4. **フェーズ4: スキルへフィードバック**。一段落ごとに、再利用可能な知見を該当スキル（プラグイン内部コード→basercms-plugin-4-to-5-upgrade、テスト→basercms-unittest、サイト手順→本スキル、5系共通ルール→basercms5-development、5系の実装パターン→basercms5-plugin-development / basercms5-theme-development）へ反映。症状→原因→修正・コード断片・戒めまで残す。

**結合タイミングはドメインごと**（見積を固める→見積画面を1回確認→次は請求…）。問題を新鮮なうちに発見でき手戻りが小さい。

### 長丁場セッションの運用（コンテキスト肥大対策・こまめに /compact）
4→5移行は1セッションが**普通のタスクより長くなりやすい**（環境構築→DB移行→棚卸し→変換→ドメインごとの5系化…と工程が多い）。セッションが長引くと、**コンテキスト肥大でアシスタントのツール呼び出しが壊れ始める**ことがある（症状: `<invoke>`/`<parameter>` の名前空間プレフィックス欠落や、先頭に無関係な語＝例「court」が紛れ込んでコマンド／ツール呼び出しが失敗する。環境やシェルの不具合ではなく、アシスタント側の出力フォーマット崩れ）。
- **対処**: この種の失敗が数回続いたら、粘って再試行せず**早めに `/compact` でコンテキストを圧縮**してから続行する（要点は要約に引き継がれるので作業は継続できる）。工程の区切り（各フェーズ完了時など）でも予防的に `/compact` してよい。
- **予防**: 大きな中間成果物（計画・台帳・調査ログ）は会話に溜め込まず、spec/plan ファイルや scratchpad に書き出しておくと、compact 後も参照が楽で肥大も抑えられる。

## Git / リポジトリ運用（移行コードのバージョン管理）

> 4系プラグインを `BcAddonMigrator` で変換し v5 に配置した後、git に取り込む際の定番のハマりどころ。G-2〜G-4 はどのプロジェクトでも該当。G-1 は **4系で git サブモジュールを使っていたプロジェクト限定**（多くのプロジェクトはサブモジュール未使用なので該当しない）。

### G-1.【サブモジュールを利用している場合のみ】変換済みプラグインに残る4系サブモジュールの `.git`（gitlink）を除去
※ この項目は、4系でプラグインを git サブモジュール（`.gitmodules` に登録）として管理していたプロジェクトのみ対象。サブモジュールを使っていなければ `.git` は付いてこないので無関係（`find v5 -name .git` が 0 件なら該当なし）。

4系で git サブモジュールだったプラグイン（`app/Plugin/<X>`）をコピーすると、`.git`（gitlink ファイル）も付いてくる。これは `gitdir: ../../../.git/modules/app/Plugin/<X>`（=4系本体のサブモジュール格納庫）を指したままで、「同じ git モジュールに2つの作業ツリーが紐づく」「変換済み5系コードが4系HEADと全差分」等の矛盾を起こす。
- **対処**: `find v5 -name .git -type f -delete` で v5 配下の gitlink ファイルを削除（実コード・`.gitignore`・4系の実サブモジュール/`.git/modules`/`.gitmodules` には触れない＝安全）。これで変換済みプラグインは「普通のディレクトリ」になる。

### G-2. baserCMS5 標準 `.gitignore` は `/plugins/*` を無視する → 自作プラグインを negation
v5 同梱 `.gitignore` は `/plugins/*` を無視し、コア/サンプル（`!/plugins/baser-core`, `!/plugins/bc-*`, `!/plugins/BcColumn` 等）だけ negation で追跡する設計。**そのままでは変換済みの自作プラグインが追跡対象外**になる。
- **対処**: 自作プラグインを `!/plugins/Sample` のように negation 追記（`git check-ignore v5/plugins/Sample` が空＝追跡可、で確認）。`/plugins/*/vendor`・`/plugins/*/node_modules`・`/plugins/*/composer.lock`・`schema-dump-default.lock` は無視のまま。

### G-3. アップロード実体 `webroot/files/*` は git に含めない
v5 標準 `.gitignore` は `/webroot/files/*` を無視する（正しい既定）。4系から移行したアップロード（本プロジェクトは **1.7GB / 1万超ファイル**）を git に入れると repo 肥大化・clone 遅延・GitHub 制限で push 不可になり得る。**除外のまま**にし、実体は別途バックアップ／再コピーで運用する（`!/webroot/files/.gitkeep` で空ディレクトリ保持）。`git add -n v5 | wc -l` 等で巨大物の混入を事前確認するとよい。

### G-4. v5 のネスト/取り込み
`git add v5` 時、ネストした `v5/.gitignore` は親リポジトリでもそのまま効く（秘匿 `config/.env`・`app_local.php`・`vendor`・`tmp`・`logs` は自動除外される）。`git add -n`（dry-run）でステージ予定を必ず検証してからコミットする。プラグインを将来「再利用可能な独立リポジトリ」にしたい場合も、まずは親に通常ファイルで取り込み、安定後に `git subtree split` で切り出す段階移行が扱いやすい。

## 注意事項

*   コードを提案する際は、必ず **CakePHP 5.0** の公式ドキュメントおよび **baserCMS 5** のソースコード（`vendor/baserproject/` 以下）との整合性を確認してください。
*   「たぶん動く」CakePHP 2 のコードを残さないでください。
*   **[重要]** ターミナルコマンドを実行する際は、バックグラウンド実行（`&` を末尾につける、またはツールの `WaitMsBeforeAsync` を短く設定して放置するなど）を行わないでください。処理が滞留し、後続の操作を受け付けなくなる可能性があります。必ず同期的に完了させてください。
