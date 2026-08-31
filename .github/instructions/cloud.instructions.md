# クラウド実行環境における特記事項

Claude Code on the web（クラウドセッション）で作業する場合の特記事項です。
セットアップ手順の全体像は [`docs/cloud/README.md`](../../docs/cloud/README.md) を参照してください。

## 自分がクラウドセッションかどうかの判定

環境変数 `CLAUDE_CODE_REMOTE_SESSION_ID` が設定されていればクラウドセッションです。
また、ローカル専用の `.github/instructions/local.instructions.md` は `.gitignore` 対象のため、
**クラウドVMには存在しません**。

## 実行環境

リポジトリ同梱の `docker/docker-compose.yml.default` を元にした `docker compose` で実行されます。

| 項目 | 値 |
|---|---|
| PHP コンテナ名 | `bc-php` |
| DB コンテナ名 | `bc-db`（MySQL 8.0） |
| baserCMS の配置先 | `/var/www/html` |
| その他のコンテナ | `bc-smtp`（mailcatcher） / `bc-pma`（phpMyAdmin） / `bc-pg`（PostgreSQL） / `bc-pga`（pgAdmin） |
| DB 接続情報 | host `bc-db` / user `root` / password `root` / database `basercms` |

> **コンテナ名は環境によって異なる**。ローカルでは `local.instructions.md` の記載（例: `basercms`）に従う。
> クラウドでは `bc-php`。いずれの場合も、実行前に `docker ps` で実際の稼働コンテナを確認すること。

## 作業開始前の確認

セッション開始時に `.claude/settings.json` の SessionStart フックが `docker/bin/cloud-up.sh` を
**バックグラウンドで**起動しており、`docker compose up` と `composer run-script app-install` が
進行中の場合があります。**作業を始める前に必ず次を実行して完了を待つこと。**

```bash
docker/bin/cloud-status.sh
```

`READY` が出るまでは、テストもコマンド実行も失敗します。
`FAILED` / `TIMEOUT` の場合は `tmp/cloud-up.log` を確認してください。

## ユニットテスト

コンテナ名以外はローカルと同じです（詳細は `basercms-unittest` スキル）。

```bash
# 全件
docker exec bc-php sh -c 'cd /var/www/html && composer run-script test'

# ファイル単位
docker exec bc-php sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/baser-core/tests/TestCase/Model/Table/PagesTableTest.php 2>&1 | tail -20'
```

## 画面の確認

```bash
curl -I http://localhost/
```

クラウドVMにはブラウザのGUIはありませんが、`curl` によるレスポンス確認と、
管理画面のHTMLを取得しての検証は可能です。

## 注意点

- Xdebug は `cloud-up.sh` が `XDEBUG_MODE: "off"` に書き換えるため無効です（デバッガの接続先が無いため）。
- VM のリソースは 4 vCPU / 16GB RAM / 30GB disk です。
- ネットワークは既定の **Trusted** で、Docker Hub・packagist.org・GitHub に到達できます。
- `docker/docker-compose.yml` / `docker/.env` / `config/.env` / `vendor/` は `.gitignore` 対象のため、
  clone 直後には存在しません。`cloud-up.sh` が生成・インストールします。
