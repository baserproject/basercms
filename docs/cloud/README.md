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

### 1. GitHub 連携（PR まで作りたいなら必須）

ターミナルで `/web-setup` を実行して `gh` のトークンを同期するか、
claude.ai のオンボーディングで Claude GitHub App を認可します。

> **ここを飛ばすと、セッション開始直後は push も PR 作成もできません。**
> GitHub に接続されていない環境では、clone に remote が付かず認証情報も入らないため、
> `git remote` が空・detached HEAD・`gh` 未認証という状態になります。
> 実装とテストはできてしまうので、**PR を作る段になって初めて詰まります**。
> しかも VM は揮発するため、コミットしただけの成果物は失われます。
>
> 詳細と確認方法は「[PR まで作れる環境にする](#pr-まで作れる環境にする)」を参照。

### 2. クラウド環境の作成

環境には2種類あります。用途で選んでください。

| | 個人環境 | 組織の共有環境 |
|---|---|---|
| 作る場所 | claude.ai/code の環境セレクタ | 管理設定 → クラウド環境（オーナーのみ） |
| 使える人 | 本人のみ | 組織メンバー全員（編集はオーナーのみ） |
| ネットワークアクセス | **Custom が使える** | **Custom が無い**（フル / 信頼済み / なし の3択） |
| Claude Tag（Slack）から利用 | 不可 | 可 |

#### 個人環境の場合

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

#### 組織の共有環境の場合

管理設定 → **クラウド環境** → **＋新規** から作成します（オーナーのみ）。

   | 項目 | 値 |
   |---|---|
   | 名前 | `basercms` |
   | ネットワークアクセス | **フルアクセス** |
   | 環境変数 | **空のまま**（プレースホルダは消す） |
   | セットアップスクリプト | [`docs/cloud/setup-script.sh`](./setup-script.sh) の中身をそのまま貼り付ける |

> **共有環境には Custom が無いため、フルアクセスを選ぶ。** 信頼済みアクセスのままだと上記の
> `production.cloudfront.docker.com` の 403 を全メンバーが踏みます。フルアクセスは egress の
> 制限が外れるので、この環境に秘密情報を置かないこと（環境変数は空のまま、キーが必要なら
> **API credentials** 欄を使う。そちらはサンドボックス外に保管され中身が見えません）。

> **環境変数欄に秘密情報を入れないこと。** 環境を使うメンバー全員が読めます。

作成しただけでは組織のデフォルト環境にはなりません。デフォルトの指定は
管理設定 → Claude Code で行います。

#### 共通の注意

> **Setup script が5分に収まらない場合**は、`dpage/pgadmin4` と `phpmyadmin` の `docker pull` 行を削ってください。
> `docker compose up` の時に遅延 pull されるだけで、動作は変わりません。

### 3. SessionStart フックの置き場所に注意する

セッション開始時に `docker/bin/cloud-up.sh` をバックグラウンドで起動するためのフックです。
`app-install` は数分かかりフックのタイムアウトに収まらないため、`nohup ... &` で必ずバックグラウンド起動します。
ローカルでは `cloud-up.sh` が冒頭で即終了するので無害です。

> **リポジトリ同梱の `.claude/settings.json` は読み込まれません。**
> プロジェクト設定として認識されるのはプロジェクトルート直下の `.claude/settings.json` だけですが、
> クラウドではプロジェクトルートが `/workspace`、リポジトリのクローン先が `/workspace/repo` になるため、
> `/workspace/repo/.claude/settings.json` は対象外になります。
> （実際に `/root/.claude/projects/` に作られるディレクトリは `-workspace` の1つだけです。）
> そのため**フックは手順2の Setup script が `/workspace/.claude/settings.json` に設置します。**
> リポジトリ側の `.claude/settings.json` は、リポジトリを直接プロジェクトルートとして開いた場合の
> 保険として同じ内容を保持しています。

設置されるフックは次の内容です。`$CLAUDE_PROJECT_DIR` に依存せず `/workspace` 配下から
`cloud-up.sh` を探索するため、クローン先のディレクトリ名が変わっても動きます。
発火したかどうかは `/tmp/cloud-up-hook.log` の有無で判別できます。

```json
{
  "hooks": {
    "SessionStart": [
      {
        "matcher": "startup|resume",
        "hooks": [
          {
            "type": "command",
            "command": "nohup bash -c 'for d in \"$CLAUDE_PROJECT_DIR\" \"$PWD\" /workspace/*; do [ -n \"$d\" ] && [ -f \"$d/docker/bin/cloud-up.sh\" ] && { echo \"[$(date +%FT%T)] SessionStart hook fired. root=$d\"; exec bash \"$d/docker/bin/cloud-up.sh\"; }; done' >>/tmp/cloud-up-hook.log 2>&1 &"
          }
        ]
      }
    ]
  }
}
```

> **フックに頼り切らないこと。** フックが発火しなくても、`docker/bin/cloud-status.sh` が
> 未起動を検出して `cloud-up.sh` を起動するため作業は止まりません。手順は「使い方」を参照。

### 4. リポジトリ側の設定をコミットする

`.claude/settings.json`（SessionStart フック）・`docker/bin/cloud-up.sh`・`docker/bin/cloud-status.sh` は、
**コミットして push されていないとクラウドVMに届きません**。
`claude --cloud` はローカルの作業ツリーではなく GitHub のリモートを clone するためです。

> **`docs/cloud/setup-script.sh` を変更した場合は、コミットするだけでは反映されません。**
> このファイルは「コピー元の正本」であり、実際に実行されるのは claude.ai の環境設定に
> 貼り付けた内容です。変更を効かせるには**環境設定の Setup script 欄へ貼り直し**が必要です。
> 既存環境ではキャッシュ済みのファイルシステムが使われるため、
> `/workspace/.claude/settings.json` を新規に配置したい場合は**環境の再作成**が要ります。

## PR まで作れる環境にする

「判断はローカル、実行はクラウド」を成立させるには、セッション内で
実装 → ユニットテスト → **PR 作成**まで完結できる必要があります。
そのために必要なのは次の3つです。

| 必要なもの | どこで入るか | 後から足せるか |
|---|---|---|
| `origin`（push 先） | 環境作成時に GitHub リポジトリを接続する | **足せる**（`git remote add`） |
| push 認証 | 手順1の GitHub 連携（Claude GitHub App の認可 / `/web-setup`） | **足せる**（`add_repo` ツール） |
| `gh` コマンド | 標準環境には同梱。無ければ Setup script が導入する | **足せる**（`apt-get install gh`） |

揃っていない環境に当たっても、**セッションを作り直さずその場で復旧できます**。
手順は「[繋がっていなかった場合](#繋がっていなかった場合環境の作り直しは不要)」を参照。

### 接続できているかの確認

セッション開始後、`docker/bin/cloud-status.sh` の出力末尾に `--- git / PR ---` が出ます。
次のようになっていれば PR まで作れます。

```
--- git / PR ---
  remote: https://github.com/baserproject/basercms.git
  branch: fix/xxxxx
  gh:     認証済み
```

`remote: なし` / `branch: なし（detached HEAD）` / `gh: 未インストール` のいずれかが出て
`WARNING` が表示された場合、その環境からは push も PR 作成もできません。

### 繋がっていなかった場合（環境の作り直しは不要）

**そのセッションのまま復旧できます。** 環境を作り直す必要はありません。

#### 1. remote を追加する

```bash
git remote add origin git@github.com:baserproject/basercms.git
```

SSH 形式で書いても、agent proxy の設定で HTTPS に書き換えられます。

#### 2. リポジトリをセッションの許可対象に加える

この時点で push すると、次のように 403 で弾かれます。

```
remote: access denied by the git proxy: baserproject/basercms is not in
        this session's authorized repository set, so the proxy will not
        inject a credential for it. To fix, add the repository to the
        session's sources.
```

**エラーが指しているとおり、リポジトリをセッションのソースに追加すれば通ります。**
Claude Code のセッションからは `add_repo` ツール（`owner: baserproject` /
`repo: basercms` / `access: push`）で追加します。
追加後は `git push -u origin <ブランチ名>` がそのまま成功します。

> 既にフルクローンが手元にある場合、`add_repo` の案内に従って別途クローンし直す必要は
> ありません。クレデンシャルの注入対象になるだけなので、既存のクローンから push できます。

#### 3. PR は REST API で作る

`gh pr create` は **GraphQL を使うため 403 になります**。

```
HTTP 403: This GraphQL query (RepositoryInfo, sent by gh pr create ...)
is not enabled for this session — only the pinned set of PR-review
operations is served. Use REST via `gh api repos/{owner}/{repo}/...` instead.
```

REST なら通るので、`gh api` で作成します。本文に日本語や記号が入るため、
JSON ファイルにして `--input` で渡すのが確実です。

```bash
gh api repos/baserproject/basercms/pulls \
  --method POST --input /tmp/pr.json
```

`/tmp/pr.json` は `{"title": "...", "head": "<ブランチ名>", "base": "5.4.x", "body": "..."}` の形式です。

> `gh auth status` は `GH_TOKEN is invalid` と出ますが、これは正常です。
> git の認証は proxy が注入しており、`gh api` も proxy 経由で通ります。
> `cloud-status.sh` の `gh: 未認証` 表示もこれを拾っているだけで、PR は作れます。

#### どうしても復旧できない場合

成果物は次のようにパッチとして書き出し、手元のクローンへ持っていってください。
VM は揮発するため、コミットしただけでは失われます。

```bash
git checkout -b <ブランチ名>
git add -A
git commit -m "<メッセージ>"
git format-patch -1 -o /tmp/patch
```

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

まず状態を確認します。これ一本で、未起動なら起動、VM 再起動で落ちていれば復旧まで行います。

```bash
docker/bin/cloud-status.sh
```

`READY` が出たら準備完了です。`READY` は完了マークではなく **dockerd と `bc-php` / `bc-db` の
実際の稼働を確認したうえで**表示されます。以降はローカルと同じ操作ができます
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
| `cloud-status.sh` が `FAILED` を返す | `tmp/cloud-up.log` を確認する。原因を潰したら `rm -f tmp/cloud-up.failed` してから再実行する（`FAILED` は自動で再試行しない） |
| `cloud-status.sh` が `STALE` を出す | 異常ではない。VM 再起動でコンテナが消えた状態を検出し、自動で起動し直している。そのまま `READY` を待つ |
| 起動していないのに `READY` が返る | 修正済み。`cloud-status.sh` が `tmp/cloud-up.done` の有無だけで判定していたのが原因。`.done` はディスクに残るがコンテナは VM 再起動で消えるため誤報していた。現在は実体の稼働を確認する |
| SessionStart フックが動いていない | `/tmp/cloud-up-hook.log` が無ければ発火していない。`/workspace/.claude/settings.json` の有無を確認する（無ければ手順2・3を参照）。なお発火しなくても `cloud-status.sh` で起動できるため作業は止まらない |
| Setup script やイメージ pull が `403 Forbidden`（`production.cloudfront.docker.com` 等） | 環境の Network access が **Custom + `*.docker.com`**（かつ既定リストのチェックあり）になっているか確認する。手順2を参照 |
| コンテナ内の HTTPS が `SSL certificate problem: self-signed certificate in certificate chain` | クラウドの通信は agent proxy 経由のため、その CA をコンテナが信頼している必要がある。`cloud-up.sh` が `/root/.ccr/ca-bundle.crt` を `bc-php` に導入する。ログに `No agent proxy CA found` が出ていれば CA のパスが変わっている可能性があるので確認する |
| `composer` が 300 秒で打ち切られる | `cloud-up.sh` は `composer run-script --timeout=3000` で実行している。手動で叩くときも `--timeout` を付ける |
| `cloud-status.sh` が `TIMEOUT` を返す | `app-install` に時間がかかっている可能性がある。`TIMEOUT=1800 docker/bin/cloud-status.sh` で待ち直す |
| `cloud-status.sh` の `--- git / PR ---` に `WARNING` が出る | 環境が GitHub リポジトリに接続されていない。**環境の作り直しは不要**で、その場で復旧できる。「[繋がっていなかった場合](#繋がっていなかった場合環境の作り直しは不要)」を参照 |
| `git push` で `'origin' does not appear to be a git repository` | remote が未設定。`git remote add origin git@github.com:baserproject/basercms.git` で追加する |
| `git push` が `access denied by the git proxy: ... not in this session's authorized repository set` | proxy がクレデンシャルを注入していない。`add_repo` ツールでリポジトリをセッションに追加すれば通る |
| `gh pr create` が `HTTP 403: This GraphQL query ... is not enabled for this session` | GraphQL が塞がれている。`gh api repos/{owner}/{repo}/pulls --method POST --input <json>` の REST で作る |
| `gh auth status` が `The token in GH_TOKEN is invalid` | 正常。git も `gh api` も proxy が認証を注入するため、この表示のままで push も PR 作成もできる |
| `gh: command not found` | Setup script が古い。更新後の `docs/cloud/setup-script.sh` を環境設定へ貼り直して環境を再作成する |
| `cloud-status.sh` が `LOCAL` を返す | クラウドセッションではない。ローカルの手順は `.github/instructions/local.instructions.md` を参照 |
| 起動をやり直したい | `rm -f tmp/cloud-up.done tmp/cloud-up.failed && CLOUD_FORCE=1 docker/bin/cloud-up.sh` |

> **`CLOUD_FORCE=1` はローカルで使わないこと。** ローカルでは既に別の開発環境（`bc-db` / `bc-smtp` 等）が
> 稼働していることがあり、ポート衝突や既存コンテナの巻き込みが起きます。クラウドセッション内でのみ使ってください。

## 制約

- VM のリソースは 4 vCPU / 16GB RAM / 30GB disk。大規模なビルドやメモリを大量に使うテストは停止されることがある。
- セッションは一定時間の無操作でVMが回収される。再開すると会話履歴は復元されるが、**起動中のコンテナは失われる**。
  `tmp/cloud-up.done` はディスクに残るため、マークだけを見ると起動済みに見える点に注意。
  `cloud-status.sh` はこれを `STALE` として検出し、自動で立ち上げ直す。
- クラウドセッションのレート制限は、通常の Claude / Claude Code の利用枠と共有される。
