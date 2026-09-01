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

**作業を始める前に必ず次を実行して完了を待つこと。**

```bash
docker/bin/cloud-status.sh
```

`READY` が出るまでは、テストもコマンド実行も失敗します。

このスクリプトは状態を表示するだけでなく、**未起動なら `cloud-up.sh` を起動して復旧まで面倒を見ます**。
SessionStart フックが発火していてもいなくても、VM が再起動してコンテナが消えていても、
これ一本で `READY` まで持っていけます（多重起動は `cloud-up.sh` 側のロックで防がれます）。

出力の意味は次のとおりです。

| 出力 | 意味 |
|---|---|
| `READY` | dockerd と `bc-php` / `bc-db` の**稼働を実際に確認済み**。作業を開始してよい |
| `STALE` | 完了マークは残っていたが実体が消えていた（VM 再起動）。破棄して起動し直す |
| `STARTING` | 未起動だったので `cloud-up.sh` を起動した。完了まで待つ |
| `FAILED` | 起動に失敗している。`tmp/cloud-up.log` を確認する。再試行は `rm -f tmp/cloud-up.failed` してから |
| `TIMEOUT` | 既定 900 秒で完了しなかった。`TIMEOUT=1800 docker/bin/cloud-status.sh` で待ち直す |

> **`tmp/cloud-up.done` の有無だけで起動を判断しないこと。** このマークはディスクに残るが、
> dockerd とコンテナは VM 再起動で消える。マークだけを見ると「起動済み」と誤判定し、
> 直後の `docker exec bc-php ...` が原因不明のエラーになる。
> 必ず `cloud-status.sh`（実体の生存を確認する）を経由すること。

## push / PR 作成の可否を最初に確認する

`cloud-status.sh` の出力末尾に `--- git / PR ---` として、`remote` / `branch` / `gh` の
状態が出ます。**作業を始める前にここを見ること。**

`WARNING` が出ていても、**環境の作り直しは不要です。このセッションのまま復旧できます。**

1. `git remote add origin git@github.com:baserproject/basercms.git`
2. push が `access denied by the git proxy` の 403 になったら、`add_repo` ツールで
   リポジトリをセッションに追加する（`owner: baserproject` / `repo: basercms` /
   `access: push`）。追加後は `git push -u origin <ブランチ名>` が通る
3. PR は `gh pr create` が GraphQL で 403 になるため、**REST で作る**

   ```bash
   gh api repos/baserproject/basercms/pulls --method POST --input /tmp/pr.json
   ```

`gh auth status` が `GH_TOKEN is invalid` を返すのは正常です。認証は proxy が注入します。
詳細は [`docs/cloud/README.md`](../../docs/cloud/README.md) の「PR まで作れる環境にする」を参照。

どうしても復旧できない場合のみ、**VM は揮発するため**成果物をパッチとして書き出し、
`SendUserFile` でユーザーへ渡してください。

```bash
git checkout -b <ブランチ名>
git add -A
git commit -m "<メッセージ>"
git format-patch -1 -o /tmp/patch
```

**`cloud-status.sh` が確実な入口です。** SessionStart フックは `$CLAUDE_PROJECT_DIR` の解決が
環境によって変わり、起動していないことがあります（作業ディレクトリが `/workspace` で
リポジトリが `/workspace/repo` にクローンされるケース等）。その場合 `cloud-status.sh` は
`NOT STARTED` を表示して自分で `cloud-up.sh` を起動するので、**フックが動いたかどうかを
気にする必要はありません**。フックは先回りして起動しておくための最適化に過ぎません。

## ユニットテスト

コンテナ名以外はローカルと同じです（詳細は `basercms-unittest` スキル）。

```bash
# 全件
docker exec bc-php sh -c 'cd /var/www/html && composer run-script test'

# ファイル単位
docker exec bc-php sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/baser-core/tests/TestCase/Model/Table/PagesTableTest.php 2>&1 | tail -20'
```

> **`vendor/bin/phpunit` を直接叩く場合は、事前に一度 `bin/cake setup test` が必要。**
> `composer run-script test` はこれを内部で実行するが、phpunit を直接呼ぶと未実行のまま弾かれる。
>
> ```bash
> docker exec bc-php sh -c 'cd /var/www/html && bin/cake setup test'
> ```

## 画面の確認

```bash
curl -s -o /dev/null -w "%{http_code}\n" http://localhost/
```

> **`docker exec` は root で走る。** root で `bin/cake` や `composer` を叩くと `tmp/cache` 配下に
> root 所有のキャッシュファイルが残り、www-data で動く Web 側がそれを上書きできず
> `_bc_env_ cache was unable to write 'enable_plugins'` で **500 になる**。
> 500 が出たら次で復旧する（`cloud-up.sh` も起動のたびに同じことをしている）。
>
> ```bash
> docker exec bc-php sh -c 'chown -R www-data:www-data /var/www/html/tmp/cache /var/www/html/logs'
> ```

クラウドVMにはブラウザのGUIはありませんが、`curl` によるレスポンス確認と、
管理画面のHTMLを取得しての検証は可能です。

## 注意点

- Xdebug は `cloud-up.sh` が `XDEBUG_MODE: "off"` に書き換えるため無効です（デバッガの接続先が無いため）。
- VM のリソースは 4 vCPU / 16GB RAM / 30GB disk です。
- ネットワークは **Custom**（`*.docker.com` を追加＋既定リストを含める）で運用します。既定の Trusted のままだと
  Docker Hub の blob 配信先が `production.cloudfront.docker.com` にリダイレクトされたときに `403 Forbidden` となり、
  イメージの pull が全滅します。
- `docker/docker-compose.yml` / `docker/.env` / `config/.env` / `vendor/` は `.gitignore` 対象のため、
  clone 直後には存在しません。`cloud-up.sh` が生成・インストールします。
- SessionStart フックは **プロジェクトルート（`/workspace`）側の `.claude/settings.json`** で登録されます。
  リポジトリ同梱の `.claude/settings.json` は、クローン先が `/workspace/repo` のように
  プロジェクトルートの配下になるため**読み込まれません**。プロジェクトルート側への設置は
  環境の Setup script（`docs/cloud/setup-script.sh`）が行います。
  フックが発火したかどうかは `/tmp/cloud-up-hook.log` の有無で判別できます。
