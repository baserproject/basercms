# クラウド開発環境（Claude Code on the web）

baserCMS の開発を、ローカルPCではなくクラウドVM上の Claude Code セッションで行うための設定手順です。

**考え方は「判断はローカル、実行はクラウド」**。設計・計画立案・UIの微調整はローカルで行い、
実装・ユニットテスト・PR作成はクラウドセッションに任せます。

## 仕組み

| 段階 | 実行されるもの | 内容 |
|---|---|---|
| 環境の初回セットアップ | `docs/cloud/setup-script.sh`（**claude.ai の環境設定に貼る**） | dockerd 起動と Docker イメージの事前 pull。結果は環境キャッシュに保存され、2回目以降はスキップされる |
| セッション開始のたび | `docker/bin/cloud-up.sh`（`.claude/settings.json` の SessionStart フックからバックグラウンド起動） | compose ファイル生成 → `docker compose up -d` → MySQL 待機 → `composer run-script app-install` |
| 作業開始前の確認 | `docker/bin/cloud-status.sh` | 起動完了を待って状態を表示する |

`docker/docker-compose.yml` は `.gitignore` 対象でクラウドVMには存在しないため、
`cloud-up.sh` が `docker-compose.yml.default` からコピーして生成します
（GitHub Actions の `.github/workflows/test.yml` と同じ手順）。
起動されるサービスはローカルと同一のフルスタック（bc-db / bc-php / bc-smtp / bc-pma / bc-pg / bc-pga）です。

## セットアップ手順

### 1. GitHub 連携

ターミナルで `/web-setup` を実行して `gh` のトークンを同期するか、
claude.ai のオンボーディングで Claude GitHub App を認可します。

### 2. クラウド環境の作成

1. [claude.ai/code](https://claude.ai/code) を開く
2. 環境セレクタから **Add cloud environment**（既存環境を編集する場合は設定アイコン）
3. 次のとおり設定する

   | 項目 | 値 |
   |---|---|
   | Name | `basercms` など任意 |
   | Network access | **Custom** にして、Allowed domains に `*.docker.com` を1行追加する。あわせて **「Also include default list of common package managers」に必ずチェックを入れる**（外すと packagist.org 等が落ちる） |
   | Environment variables | **設定不要**（DBは compose 内の root/root、GitHub は proxy が認証する） |
   | Setup script | [`docs/cloud/setup-script.sh`](./setup-script.sh) の中身をそのまま貼り付ける |

> **`*.docker.com` の追加は必須。** 既定の Trusted 許可リストには `registry-1.docker.io` や
> `production.cloudflare.docker.com` は入っていますが、Docker Hub がイメージ本体（blob）の配信を
> CloudFront 側（`production.cloudfront.docker.com`）にリダイレクトすると許可リストから外れ、
> `403 Forbidden` で pull が全滅します。配信先 CDN は切り替わるため、ホスト単体ではなく
> `*.docker.com` で許可しておくこと。

> **Setup script が5分に収まらない場合**は、`dpage/pgadmin4` と `phpmyadmin` の `docker pull` 行を削ってください。
> `docker compose up` の時に遅延 pull されるだけで、動作は変わりません。

### 3. SessionStart フックを `.claude/settings.json` に追加する

セッション開始時に `docker/bin/cloud-up.sh` をバックグラウンドで起動するためのフックです。
`app-install` は数分かかりフックのタイムアウトに収まらないため、`nohup ... &` で必ずバックグラウンド起動します。
ローカルでは `cloud-up.sh` が冒頭で即終了するので無害です。

```json
{
  "enabledPlugins": {
    "superpowers@claude-plugins-official": true
  },
  "hooks": {
    "SessionStart": [
      {
        "matcher": "startup|resume",
        "hooks": [
          {
            "type": "command",
            "command": "nohup bash \"$CLAUDE_PROJECT_DIR/docker/bin/cloud-up.sh\" >/dev/null 2>&1 &"
          }
        ]
      }
    ]
  }
}
```

### 4. リポジトリ側の設定をコミットする

`.claude/settings.json`（SessionStart フック）・`docker/bin/cloud-up.sh`・`docker/bin/cloud-status.sh` は、
**コミットして push されていないとクラウドVMに届きません**。
`claude --cloud` はローカルの作業ツリーではなく GitHub のリモートを clone するためです。

## 使い方

### ローカルで計画 → クラウドで実行

```bash
# 1. ローカルで設計・計画（プランモード）
claude --permission-mode plan

# 2. 計画をリポジトリに保存してコミット・push

# 3. クラウドで実行
claude --cloud "docs/superpowers/plans/xxx.md の実装計画を実行して"
```

### クラウドセッションでの作業開始

セッション開始直後は `cloud-up.sh` がバックグラウンドで動いています。まず状態を確認します。

```bash
docker/bin/cloud-status.sh
```

`READY` が出たら準備完了です。以降はローカルと同じ操作ができます
（コンテナ名だけ異なります。[`.github/instructions/cloud.instructions.md`](../../.github/instructions/cloud.instructions.md) 参照）。

```bash
# ユニットテスト（全件）
docker exec bc-php sh -c 'cd /var/www/html && composer run-script test'

# ユニットテスト（絞り込み）
docker exec bc-php sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/baser-core/tests/TestCase/Model/Table/PagesTableTest.php'

# 画面の疎通確認
curl -I http://localhost/
```

### 進行中のセッションへの追加指示

```bash
claude -p "composer run-script test をフルで流して、失敗の集計を出して" --cloud <session-id>
```

### ローカルへの引き戻し

```bash
claude --teleport            # セッションを選んで引き戻す
claude --teleport <session-id>
```

引き戻しには作業ツリーがクリーンであること、同じリポジトリのチェックアウトであることが必要です。

## トラブルシューティング

| 症状 | 対処 |
|---|---|
| `cloud-status.sh` が `FAILED` を返す | `tmp/cloud-up.log` を確認する |
| Setup script やイメージ pull が `403 Forbidden`（`production.cloudfront.docker.com` 等） | 環境の Network access が **Custom + `*.docker.com`**（かつ既定リストのチェックあり）になっているか確認する。手順2を参照 |
| `cloud-status.sh` が `TIMEOUT` を返す | `app-install` に時間がかかっている可能性がある。`TIMEOUT=1800 docker/bin/cloud-status.sh` で待ち直す |
| `cloud-status.sh` が `LOCAL` を返す | クラウドセッションではない。ローカルの手順は `.github/instructions/local.instructions.md` を参照 |
| 起動をやり直したい | `rm -f tmp/cloud-up.done tmp/cloud-up.failed && CLOUD_FORCE=1 docker/bin/cloud-up.sh` |

> **`CLOUD_FORCE=1` はローカルで使わないこと。** ローカルでは既に別の開発環境（`bc-db` / `bc-smtp` 等）が
> 稼働していることがあり、ポート衝突や既存コンテナの巻き込みが起きます。クラウドセッション内でのみ使ってください。

## 制約

- VM のリソースは 4 vCPU / 16GB RAM / 30GB disk。大規模なビルドやメモリを大量に使うテストは停止されることがある。
- セッションは一定時間の無操作でVMが回収される。再開すると会話履歴は復元されるが、起動中のコンテナは失われるため `cloud-status.sh` から再度立ち上げ直す。
- クラウドセッションのレート制限は、通常の Claude / Claude Code の利用枠と共有される。
