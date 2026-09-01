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
| その他のコンテナ | `bc-smtp`（mailcatcher） / `bc-pma`（phpMyAdmin） |
| DB 接続情報 | host `bc-db` / user `root` / password `root` / database `basercms` |

> **クラウドでは `bc-pg`（PostgreSQL）と `bc-pga`（pgAdmin）を起動しない。**
> PostgreSQL はユニットテストで使われておらず、pgAdmin はその管理画面なので出番がない。
> さらに pgAdmin はバインドする `docker/pgadmin` を compose が root 所有で作るため、
> uid 5050 のコンテナが書き込めず起動直後に落ちる。`docker-compose.yml.default` は
> ローカル・CI と共通のまま、`cloud-up.sh` が起動するサービスを明示して外している。
> クラウドで PostgreSQL を触る必要が出たら、`cloud-up.sh` の `CLOUD_SERVICES` を見直すこと。

> **コンテナ名は環境によって異なる**。ローカルでは `local.instructions.md` の記載（例: `basercms`）に従う。
> クラウドでは `bc-php`。いずれの場合も、実行前に `docker ps` で実際の稼働コンテナを確認すること。

## 作業開始前の確認

**セッション開始時と、アイドル再開後の両方で、必ず次を実行して完了を待つこと。**

> **「作業開始前」だけでは足りない。** アイドルで環境が再起動されると
> SessionStart フックが発火しないことがある（matcher は `startup|resume` だが、
> 同一 VM が継続する再開では発火しない実測あり）。会話が続いていても、
> 「セッションを再開しました」の通知を見たら必ずもう一度実行すること。

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

出力は40行を超えるため、`| tail` で切ると先頭の判定行が流れる。**末尾にも `RESULT: READY` の
ように1行のサマリが出る**ので、`head` でも `tail` でもそこを見れば判定できる。
終了コードでも判定できる（READY は 0、FAILED / TIMEOUT は 1）。

READY でも、`bc-php` / `bc-db` 以外のコンテナが落ちている場合は
`WARNING: 起動していないコンテナがあります。` が併記される。baserCMS 本体は動いているので
作業は進められるが、そのコンテナが要る作業をするなら `docker logs <名前>` で原因を見ること。

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
3. `gh: 未インストール` と出ていたら入れる（Ubuntu の universe にあるので数十秒）

   ```bash
   apt-get update -qq || true
   apt-get install -y -qq --no-install-recommends gh
   ```

   > **`&&` で繋がないこと。** このイメージには deadsnakes / ondrej-php の PPA が入って
   > おり、`ppa.launchpadcontent.net` が許可リストに無いため `apt-get update` が 403 で
   > 失敗する。環境によっては警告ではなくエラー扱いになって非ゼロ終了するため、
   > `&&` で繋ぐと `gh` のインストールまで巻き添えで落ちる。
   > `gh` は Ubuntu 本体（`noble-updates/universe`）にあるので、PPA が落ちても入る。
   >
   > `-o Dir::Etc::sourceparts=/dev/null` で PPA を避けるのは**誤り**。noble では
   > メインアーカイブも `/etc/apt/sources.list.d/ubuntu.sources` にあるため
   > （`/etc/apt/sources.list` はコメントのみ）、本体ごと無効化されて
   > `Unable to locate package gh` になる。

4. PR は `gh pr create` が GraphQL で 403 になるため、**REST で作る**

   ```bash
   gh api repos/baserproject/basercms/pulls --method POST --input /tmp/pr.json
   ```

   `/tmp/pr.json` は `{"title": "...", "head": "<ブランチ名>", "base": "5.4.x", "body": "..."}`。
   本文に日本語や記号が入るため、コマンドラインで渡さず JSON ファイルにすること。

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

**`cloud-status.sh` が確実な入口です。** SessionStart フックはプロジェクトルート側の
`/workspace/.claude/settings.json` から登録されるため、環境によっては登録されておらず
発火しません（リポジトリ同梱の `.claude/settings.json` は、クローン先が
`/workspace/repo` のようにプロジェクトルートの配下になると読み込まれません）。
その場合 `cloud-status.sh` は `STARTING` を表示して自分で `cloud-up.sh` を起動するので、
**フックが動いたかどうかを気にする必要はありません**。
フックは先回りして起動しておくための最適化に過ぎません。

## ユニットテスト

コンテナ名以外はローカルと同じです（詳細は `basercms-unittest` スキル）。

```bash
# 全件
docker exec bc-php sh -c 'cd /var/www/html && composer run-script test'

# ファイル単位
docker exec bc-php sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/baser-core/tests/TestCase/Model/Table/PagesTableTest.php 2>&1 | tail -20'
```

> **`bin/cake setup test` は `cloud-up.sh` が起動時に実行済みのため、
> `vendor/bin/phpunit` をそのまま直接叩ける。**
> 何らかの理由で弾かれた場合（`cloud-up.sh` のログに `WARNING: setup test failed.` が
> 出ているときなど）は、手で実行してから再試行する。
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
