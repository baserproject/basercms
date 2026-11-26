---
name: basercms5-claude-workflow-setup
description: 'baserCMS5（CakePHP5）の開発・移行を Claude Code で進めるときに、着手前に一度参照する「推奨ワークフロー環境セットアップ」スキル（提案ベース・実行は opt-in）。「5系プラグインの開発や移行をこれから始める」「どう進めるのがベストか」「設計→計画→実装の進め方/環境を整えたい」「パーミッションを整理して Auto mode で進めたい」等のときに最初に参照する。superpowers brainstorming での設計、permissions-audit でのパーミッション整理、その上での Auto mode 利用、spec/plan などレビュー用の文書をコメントできるアーティファクト（Claude Docs のドキュメント）で渡すこと、brainstorming→plan プレビュー→spec 書き出しの順序を、強制せず"提案"する（やるかはユーザー判断、既に整っていればスキップ）。技術的な実装パターンは basercms5-plugin-development / basercms5-theme-development（共通ルールは basercms5-development）、移行の書き換えは basercms-plugin-4-to-5-upgrade / basercms-plugin-5x-update、テスト実行は basercms-unittest を参照。'
license: MIT
---

# baserCMS5 × Claude Code 開発/移行ワークフロー 環境セットアップ

baserCMS5（CakePHP5）の開発・移行を **Claude Code で進めるときの「ベストな進め方の環境」を提案する**ための入口スキル。技術的な実装・書き換えパターンは別スキル（`basercms5-plugin-development` / `basercms5-theme-development`、共通ルールは `basercms5-development` ほか）に任せ、本スキルは**どう進めるか＝設計→パーミッション整理→Auto mode→実装**の段取りだけを扱う。

## ★大前提（このスキルの性格）
- **ユーザーに"学習してもらう（教える）"のが主目的**: このスキルは作業を代行するためのものではなく、**推奨ワークフロー（brainstorming で設計／permissions-audit で権限整理／Auto mode／spec・plan をコメントできるドキュメントでレビュー）を、ユーザー自身が今後も使えるように「伝えて学習してもらう」**ためのもの。各ツールは「私（アシスタント）が黙って回す」のでなく、「**こういうときはこれを使うとよい**」とユーザーに教え、ユーザーが選んで使えるようにする。
- **提案であって強制ではない**: ここで示すのは「推奨」。`permissions-audit` の実行も Auto mode への切替も**ユーザーが選ぶ（opt-in）**。「今回はやらない」も尊重し、そのまま実装に進んでよい。
- **冪等・低摩擦**: 技術スキルから毎回最初に呼ばれても繰り返さない。**既に整っていれば即スキップ**。下記「現状チェック」で整っている項目は飛ばし、足りない項目だけを軽く提案する。
- **スキルから自動で環境を変えない**: Auto mode の切替や設定の最終適用は**ユーザー操作**。本スキルは案内と、ユーザーが望んだときの作業（permissions-audit の実行等）に留める。

## 前提: 必要なプラグインの導入（未導入なら案内・ユーザー操作）
このワークフローは2つの **Claude Code プラグイン**を使う。未導入なら**導入を提案**し（やるかはユーザー判断）、導入コマンドは**ユーザーが Claude Code 上で実行**する（スキルからは導入できない）。

- **superpowers**（`brainstorming` / `writing-plans` / `subagent-driven-development` 等を提供）
  - 導入: `/plugin marketplace add anthropics/claude-plugins-official` → `/plugin install superpowers@claude-plugins-official`
  - （別ソース可: `/plugin marketplace add obra/superpowers-marketplace` → `/plugin install superpowers@superpowers-marketplace`）
- **permissions-audit**（`.claude/settings.json` の allow/deny を整理）
  - 導入: `/plugin marketplace add https://github.com/makikub/claude-code-plugin.git` → `/plugin install permissions-audit@makikub-plugins`
- 導入確認: `/plugin`（一覧）で superpowers / permissions-audit が有効か。既に有効ならこの節はスキップ。
- **★VSCode（Visual Studio Code の拡張）で作業している場合**: プラグイン導入が完了したら、**VSCode（または拡張）の再起動を促す**。導入直後はプラグイン／スキルが読み込まれないことがあるため、「インストールが完了しました。VSCode を利用中の場合は、一度 VSCode（拡張）を再起動してから続けてください」とユーザーに案内する。
- ※ spec / plan をレビューに渡すには **Artifact ツールと Claude Docs のコネクタ**が要る（Claude デスクトップアプリ等）。無い環境では代替手段を相談。

## 前提: 常時ON規則を CLAUDE.md に入れる（提案・opt-in・置き場所はユーザー選択）
本スキルは「設計→計画→spec の順序」「spec/plan などレビュー用の文書の渡し方」の**詳細手順の正本**を持つ（下記セクション）。ただし**スキルは呼び出されたときだけ読み込まれる**ため、これらを**全セッションで自動発火（常時ON）**——特に「毎回言われなくても自動でコメントできるドキュメントで渡す」——にしたい場合は、**CLAUDE.md（毎回自動読込）に短いトリガ規則を入れる**必要がある。これも環境セットアップの一部として**提案**する（opt-in・やるかはユーザー判断・既にあればスキップ）。

**★追記先はユーザーに選んでもらう**（どちらに入れるか必ず確認する）:
- **(A) グローバル `~/.claude/CLAUDE.md`** → **全プロジェクトで常時ON**（baserCMS5 を複数プロジェクトで触るなら基本こちら）
- **(B) プロジェクトの CLAUDE.md**（プロジェクト直下の `CLAUDE.md` 等） → **そのプロジェクトだけ常時ON**（このプロジェクトに閉じて効かせたいとき）

選んでもらった側の CLAUDE.md に、次の2ブロックを追記することを提案する（既にあればスキップ）:

```markdown
> 下記2つは全プロジェクト共通の常時ONルール（要点のみ）。詳細手順は `basercms5-claude-workflow-setup` スキルに集約（あるプロジェクトはそれを正本に参照／無くても下記要点で運用可）。

## spec / plan などのレビュー用の文書（常時ON・要点）
- spec（設計書）/ plan（実装計画）・報告書・計画書など、人にレビューしてもらう文書を作成・更新したら、コメントできるアーティファクト（Claude Docs のドキュメント）で開いて渡すまで自動で行う（毎回「見せて」と言われなくても）。HTML にしてブラウザで開く形は使わない。
  - 作り方：Artifact ツールの quickstart（intent: "document"）→ 示された Docs の type_url と題名で作る → Claude Docs のコネクタで本文を書く。
  - 正本はリポジトリの Markdown のまま持つ。コメントで直したら Markdown とドキュメントの両方へ反映する。更新したら既存のドキュメントを直す（作り直して URL を変えない）。
  - Claude Docs のコネクタが無い環境では代替手段を相談する。

## brainstorming（設計）→ plan プレビュー → spec 書き出し（常時ON・要点）
- superpowers:brainstorming の設計は、いきなり docs/superpowers/specs/ に書き出さず、plan モードで plan ファイル記述 → ExitPlanMode でレビュー → 承認＆反映 → docs/superpowers/specs/YYYY-MM-DD-<topic>-design.md 書き出し → 上記のコメントできるドキュメントで渡す → writing-plans の順（plan モードでレビューを挟むとき）。通常対話の brainstorming は従来どおり直接 spec でよい。
```

- ※ ドキュメントで渡すには Artifact ツールと Claude Docs のコネクタが必要（無い環境では代替を相談）。

## 使い方（提案フロー）
5系の開発/移行に着手する前に、次を**順に提案**する。各ステップは「現状チェック→整っていなければ提案→ユーザーが望めば実行」。

1. **現状チェック（まず確認）**: 「設計フェーズか実装フェーズか」「`.claude/settings.json` のパーミッションは整理済みか」「Auto mode を使う意向はあるか」。整っていれば該当ステップは飛ばす。
2. **`permissions-audit` でパーミッション整理（opt-in）**: `permissions-audit` で `.claude/settings.json` の allow/deny を整理することを**提案**する。やるか確認し、望めば実行（危険操作は deny、頻用コマンドは allow）。**★ユーザーに伝える**: パーミッションを整理しておくと **Auto mode（自動承認）を安心して使える**（危険操作は deny 済みなので、自動承認でも暴走しない）。＝permissions-audit は Auto mode を安全に回すための準備。
3. **Auto mode への切替を案内（ユーザー操作）**: パーミッション整理が済んだら、Claude Code の **Auto mode**（自動承認）に切り替えて進めると効率的、と案内する。**Auto mode はスキルからは有効化できない**ので、切替はユーザーが行う。整理前に Auto mode を勧めない（未整理のまま自動承認は危険）。
4. **設計は superpowers `/brainstorming` を使うことを"ユーザーに推奨として伝える"**: このスキルが勝手に brainstorming を起動するのではない。**「新規開発や大きめの改修など、何かをやりたいときは、いきなり実装せず、プランモードに切り替えて `/brainstorming` スキルで設計してから進めるのがおすすめです」とユーザーに伝える**（提案）。これにより、ユーザーが「brainstorming で設計したい」と判断できるようになる（実際の設計対話は、探索→1問ずつ質問→2〜3案→セクションごと合意 の流れ）。
   - 補足: `brainstorming` は自動では起動せず、ユーザーがそれで進めたいとき（または「設計から」と依頼したとき）に呼び出される。superpowers 未導入なら前提節の導入案内へ誘導する。
5. **実装フェーズへ**: 設計が固まったら下記「設計→計画→spec の順序」に従い、実装は技術スキル（正本は `basercms5-plugin-development` / `basercms5-theme-development`、共通ルールは `basercms5-development`、移行は `basercms-plugin-4-to-5-upgrade` / `basercms-plugin-5x-update`）＋テストは `basercms-unittest` で進める。大規模プラグイン移行の進め方（横断コードチェック→横断構文5系化→テスト&ブラウザ）は `basercms-plugin-4-to-5-upgrade`「移行の進め方」を参照。

## 設計→計画→spec の順序（plan モードでレビューを挟む場合）
> この手順の**正本はこのスキル**（`~/.claude/CLAUDE.md` 側は要点＋本スキルへの参照に集約済み・重複防止）。

`superpowers:brainstorming` で設計するときは、最終設計を**いきなり `docs/superpowers/specs/` に書き出さず**、plan プレビューでレビューする。
> **★前提: plan プレビューでのレビューには `ExitPlanMode` が必要で、これは plan モードでしか使えない。だから、まず plan モードに切り替えてから brainstorming を呼ぶことを推奨する**（plan プレビューでユーザーがレビューしやすくなる）。plan モードへの切替はユーザー操作（Shift+Tab で plan モードに）。アシスタントは「**先に plan モードへ切り替えて設計しましょう**」と案内する。

1. **plan モードに切り替える** → その中で brainstorming の対話（探索→1問ずつ質問→2〜3案→セクションごと合意）を進める。
2. 合意した最終設計を **plan ファイルに記述**し、**ExitPlanMode** で承認（plan プレビュー）に出す（この段階では `docs/superpowers/specs/` への書き出し・`writing-plans` への遷移はまだしない）。
3. ユーザーがプランプレビューでレビューし、指摘を反映（必要なら直して再度 ExitPlanMode）。
4. 承認されたら、**レビュー反映後の最終設計を `docs/superpowers/specs/YYYY-MM-DD-<topic>-design.md` に書き出す** → 下記の手順でコメントできるドキュメントで渡す → `superpowers:writing-plans` で実装計画へ。
- まとめ: brainstorming 対話 → plan ファイル → ExitPlanMode（レビュー）→ 承認＆反映 → `docs/superpowers/specs/` へ spec 書き出し → ドキュメントで渡す → `writing-plans`。
- 補足: plan モードでない通常対話の brainstorming は従来どおり（直接 spec 書き出し）でよい。本順序は「plan モードでレビューを挟みたいとき」のもの。

## spec / plan などのレビュー用の文書の渡し方
spec（設計書）や plan（実装計画）・報告書・計画書など**人にレビューしてもらう文書を作成・更新したら、コメントできるアーティファクト（Claude Docs のドキュメント）で開いて渡す**まで行う（毎回「見せて」と言われなくても自動で）。HTML に変換してブラウザで開く形は使わない（読むだけで、その場でコメントを返せないため）。
- **作り方**:
  1. Artifact ツールの `quickstart`（`intent: "document"`）を呼ぶ。
  2. 示された Docs の `type_url` と題名（文書の題名）でドキュメントを作る。
  3. Claude Docs のコネクタで本文を書く（Markdown の見出し・箇条書き・表を保つ）。
- **正本はリポジトリの Markdown**（`docs/superpowers/specs/` など）のまま持つ。ドキュメントは見せてコメントをもらうための写し。
- **コメントで直したら Markdown とドキュメントの両方へ反映**する（片方だけ直して食い違わせない）。
- **更新したら既存のドキュメントを直す**（作り直して URL を変えない。レビューする人が同じリンクで追えるように）。
- **Claude Docs のコネクタが無い環境**（Artifact ツールが無い CLI など）では、その旨を伝えて代替手段をユーザーと相談する（Markdown のまま読んでもらう、など）。黙って HTML 化に戻さない。

## Auto mode の前提と注意
- **前提**: Auto mode（自動承認）を使う前に `permissions-audit` でパーミッションを整理し、**危険操作は deny 済み**にしておく。未整理のまま自動承認は危険。
- **スキルからは有効化できない**: Auto mode は Claude Code の CLI 機能。本スキルは「ここで切り替えると効率的」と案内するだけで、切替はユーザーが行う。
- 整理済みなら、設計合意後の反復実装（横断書き換え・テスト・台帳更新等）は Auto mode で効率よく回る。

## ユーザーへブラウザ動作確認を依頼する際は対象URLを明示する（常時ON）
- サブエージェントがサンドボックス制約でブラウザ確認できなかった場合や、実装完了後にユーザー自身の目視確認を依頼する場合は、**確認してほしいページのURLを必ず明示する**（`/recruit/採用情報/` のようなパスの言及だけで済ませない）。ローカル開発環境のドメイン（例 `https://<project>.localhost/...`）まで含め、実際にクリック/コピーしてアクセスできる形で提示する。
- URLはMarkdownリンク付きで提示する。
- 理由: パスだけの言及だと、ユーザーが都度URLを組み立て直す手間が生じる。動作確認依頼は「何を」だけでなく「どこで」をワンアクションで開始できる形にする。
- **★baserCMS5の管理画面URLは、渡す前に必ず疎通確認する（推測で渡さない）**。実例（同一セッションで2回発生）: (a) 別プラグインのURLパターンから類推した`plugin-a/plugin_bs/index`が404（正しくは対象プラグインのadminNavigation設定に基づく別のダッシュ区切り）、(b) BcBlogの記事編集URLが`/edit/{postId}`ではなく`/edit/{blogContentId}/{postId}`が正しい形式（コントローラの`beforeFilter`がpass.0をblogContentIdとして要求するため）で、`/edit/{postId}`だと「コンテンツデータが見つかりません」というBcExceptionになる。
  - 確認手順: ①`docker exec <container> sh -c 'cd <v5> && bin/cake routes 2>&1 | grep -i <plugin名またはcontroller名>'`で実際のルートパターン（plugin/prefix/controller/action）を確認、②未ログイン状態で`curl -sk -o /dev/null -w '%{http_code}'`を打ち、**302（ログインへのリダイレクト）なら経路として正しい**、404ならルート自体が誤り、500系ならアプリ側の例外（curlの段階でここまで検知できることもある）。
  - このひと手間で「不具合と思ったらURL指定ミスだった」という往復を防げる（basercms-theme-4-to-5-upgrade F-23の「本番実測」姿勢と同じく、推測より実測を優先する）。

## 関連スキル
- 設計: `superpowers:brainstorming` / 計画: `superpowers:writing-plans`
- パーミッション整理: `permissions-audit`
- 技術パターン（実装）: `basercms5-development`（5系共通ルールの基底）/ `basercms5-plugin-development`（プラグイン開発の正本）/ `basercms5-theme-development`（テーマ開発の正本）/ `basercms-plugin-4-to-5-upgrade`（プラグイン4→5の書き換え＋「移行の進め方」）/ `basercms-plugin-5x-update`（5.x 間の更新）/ `basercms4-to-5-upgrade`（サイト全体の4→5）
- テスト実行: `basercms-unittest`
- レビュー用の文書の受け渡し: Artifact ツール（quickstart `intent: "document"`）＋ Claude Docs のコネクタ
