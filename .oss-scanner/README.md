# Anthropic OSS Scanner 用の設定

このディレクトリは、Anthropic の [OSS Scanner](https://github.com/anthropics/oss-scanner)
（オープンソースプロジェクトを AI が継続的にスキャンし、脆弱性の再現手順と修正案を
メンテナーへメールで届けるサービス）に baserCMS を登録するためのファイルを置いている。

| ファイル | 役割 |
|---|---|
| `Dockerfile` | スキャナが使うビルド手順。PHP 8.4 + Apache + MariaDB を 1 イメージにまとめ、`/var/www/html（/src はそのシンボリックリンク）` に baserCMS をインストールする |
| `bin/basercms-start` / `basercms-stop` | コンテナ内（init なし・ネットワークなし）で MariaDB と Apache を起動・停止する |
| `bin/basercms-test` | `bin/cake setup test` と PHPUnit を実行する |
| `threat_model.md` | baserCMS の脅威モデル（信頼境界・重要コンポーネント・重大度の基準・対象外） |

登録そのものは [anthropics/oss-scanner](https://github.com/anthropics/oss-scanner) に
`projects/basercms/project.yaml` を追加するプルリクエストで行う。`project.yaml` から
このディレクトリの `Dockerfile` と `threat_model.md` を参照するため、ビルド手順や脅威モデルの
更新は baserCMS リポジトリ側だけで完結する。

## 手元で確認する方法

```bash
git clone https://github.com/anthropics/oss-scanner.git
cd oss-scanner
# projects/basercms/project.yaml を用意したうえで
python3 tools/validate.py
tools/check basercms          # スキャナと同じ手順でビルドし、ネットワークなしのシェルを開く
```

シェルに入ったら次を実行してテストが通ることを確認する。

```bash
basercms-start
curl -sk -o /dev/null -w '%{http_code}\n' https://localhost/
basercms-test --testsuite BcSeo
```

## 注意

- `project.yaml` に書いたメールアドレスは公開される。
- スキャナのレポートは AI が生成し、人のレビューは入らない。公開はされない。
- 一時停止は `project.yaml` の `disabled: true`、登録解除は `projects/basercms/` の削除。
