---
name: basercms5-claude-workflow-setup
description: 'baserCMS5（CakePHP5）の開発・移行を Claude Code で進めるときに、着手前に一度参照する「推奨ワークフロー環境セットアップ」スキル（提案ベース・実行は opt-in）。「5系プラグインの開発や移行をこれから始める」「どう進めるのがベストか」「設計→計画→実装の進め方/環境を整えたい」「パーミッションを整理して Auto mode で進めたい」等のときに最初に参照する。superpowers brainstorming での設計、permissions-audit でのパーミッション整理、その上での Auto mode 利用、spec/plan の Markdown プレビュー、brainstorming→plan プレビュー→spec 書き出しの順序を、強制せず"提案"する（やるかはユーザー判断、既に整っていればスキップ）。技術的な書き換えパターンは basercms5-development / basercms-plugin-4-to-5-upgrade / basercms-plugin-5x-update、テスト実行は basercms-unittest を参照。'
license: MIT
---

# baserCMS5 × Claude Code 開発/移行ワークフロー 環境セットアップ

baserCMS5（CakePHP5）の開発・移行を **Claude Code で進めるときの「ベストな進め方の環境」を提案する**ための入口スキル。技術的な書き換えパターンは別スキル（`basercms5-development` ほか）に任せ、本スキルは**どう進めるか＝設計→パーミッション整理→Auto mode→実装**の段取りだけを扱う。

## ★大前提（このスキルの性格）
- **ユーザーに"学習してもらう（教える）"のが主目的**: このスキルは作業を代行するためのものではなく、**推奨ワークフロー（brainstorming で設計／permissions-audit で権限整理／Auto mode／spec・plan プレビュー）を、ユーザー自身が今後も使えるように「伝えて学習してもらう」**ためのもの。各ツールは「私（アシスタント）が黙って回す」のでなく、「**こういうときはこれを使うとよい**」とユーザーに教え、ユーザーが選んで使えるようにする。
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
- ※`markdown-to-html` はプラグインでなく**プロジェクトのスキル**（`.claude/skills/markdown-to-html/`）。無いプロジェクトでは代替手段を相談。

## 前提: 常時ON規則を CLAUDE.md に入れる（提案・opt-in・置き場所はユーザー選択）
本スキルは「設計→計画→spec の順序」「spec/plan の Markdown プレビュー」の**詳細手順の正本**を持つ（下記セクション）。ただし**スキルは呼び出されたときだけ読み込まれる**ため、これらを**全セッションで自動発火（常時ON）**——特にプレビューの「毎回言われなくても自動で開く」——にしたい場合は、**CLAUDE.md（毎回自動読込）に短いトリガ規則を入れる**必要がある。これも環境セットアップの一部として**提案**する（opt-in・やるかはユーザー判断・既にあればスキップ）。

**★追記先はユーザーに選んでもらう**（どちらに入れるか必ず確認する）:
- **(A) グローバル `~/.claude/CLAUDE.md`** → **全プロジェクトで常時ON**（baserCMS5 を複数プロジェクトで触るなら基本こちら）
- **(B) プロジェクトの CLAUDE.md**（プロジェクト直下の `CLAUDE.md` 等） → **そのプロジェクトだけ常時ON**（このプロジェクトに閉じて効かせたいとき）

選んでもらった側の CLAUDE.md に、次の2ブロックを追記することを提案する（既にあればスキップ）:

```markdown
> 下記2つは全プロジェクト共通の常時ONルール（要点のみ）。詳細手順は `basercms5-claude-workflow-setup` スキルに集約（あるプロジェクトはそれを正本に参照／無くても下記要点で運用可）。

## spec / plan などの Markdown プレビュー（常時ON・要点）
- spec/plan などレビュー目的の Markdown を作成・更新したら、HTML 化してブラウザで開くまで自動で行う（毎回言われなくても）。`markdown-to-html`（scripts/md_to_html.py、完全HTML文書・--fragment なし）で scratchpad へ出力 → `open` 表示（生成HTMLはコミットしない、更新時は再生成）。スキルが無いPJは代替を相談。

## brainstorming（設計）→ plan プレビュー → spec 書き出し（常時ON・要点）
- superpowers:brainstorming の設計は、いきなり docs/superpowers/specs/ に書き出さず、plan モードで plan ファイル記述 → ExitPlanMode でレビュー → 承認＆反映 → docs/superpowers/specs/YYYY-MM-DD-<topic>-design.md 書き出し → 上記プレビュー → writing-plans の順（plan モードでレビューを挟むとき）。通常対話の brainstorming は従来どおり直接 spec でよい。
```

- ※ プレビューに使う `markdown-to-html` は各プロジェクトにスキルとして必要（無ければ用意 or 代替を相談）。

## 使い方（提案フロー）
5系の開発/移行に着手する前に、次を**順に提案**する。各ステップは「現状チェック→整っていなければ提案→ユーザーが望めば実行」。

1. **現状チェック（まず確認）**: 「設計フェーズか実装フェーズか」「`.claude/settings.json` のパーミッションは整理済みか」「Auto mode を使う意向はあるか」。整っていれば該当ステップは飛ばす。
2. **`permissions-audit` でパーミッション整理（opt-in）**: `permissions-audit` で `.claude/settings.json` の allow/deny を整理することを**提案**する。やるか確認し、望めば実行（危険操作は deny、頻用コマンドは allow）。**★ユーザーに伝える**: パーミッションを整理しておくと **Auto mode（自動承認）を安心して使える**（危険操作は deny 済みなので、自動承認でも暴走しない）。＝permissions-audit は Auto mode を安全に回すための準備。
3. **Auto mode への切替を案内（ユーザー操作）**: パーミッション整理が済んだら、Claude Code の **Auto mode**（自動承認）に切り替えて進めると効率的、と案内する。**Auto mode はスキルからは有効化できない**ので、切替はユーザーが行う。整理前に Auto mode を勧めない（未整理のまま自動承認は危険）。
4. **設計は superpowers `/brainstorming` を使うことを"ユーザーに推奨として伝える"**: このスキルが勝手に brainstorming を起動するのではない。**「新規開発や大きめの改修など、何かをやりたいときは、いきなり実装せず、プランモードに切り替えて `/brainstorming` スキルで設計してから進めるのがおすすめです」とユーザーに伝える**（提案）。これにより、ユーザーが「brainstorming で設計したい」と判断できるようになる（実際の設計対話は、探索→1問ずつ質問→2〜3案→セクションごと合意 の流れ）。
   - 補足: `brainstorming` は自動では起動せず、ユーザーがそれで進めたいとき（または「設計から」と依頼したとき）に呼び出される。superpowers 未導入なら前提節の導入案内へ誘導する。
5. **実装フェーズへ**: 設計が固まったら下記「設計→計画→spec の順序」に従い、実装は技術スキル（`basercms5-development` / `basercms-plugin-4-to-5-upgrade` / `basercms-plugin-5x-update`）＋テストは `basercms-unittest` で進める。大規模プラグイン移行の進め方（横断コードチェック→横断構文5系化→テスト&ブラウザ）は `basercms-plugin-4-to-5-upgrade`「移行の進め方」を参照。

## 設計→計画→spec の順序（plan モードでレビューを挟む場合）
> この手順の**正本はこのスキル**（`~/.claude/CLAUDE.md` 側は要点＋本スキルへの参照に集約済み・重複防止）。

`superpowers:brainstorming` で設計するときは、最終設計を**いきなり `docs/superpowers/specs/` に書き出さず**、plan プレビューでレビューする。
> **★前提: plan プレビューでのレビューには `ExitPlanMode` が必要で、これは plan モードでしか使えない。だから、まず plan モードに切り替えてから brainstorming を呼ぶことを推奨する**（plan プレビューでユーザーがレビューしやすくなる）。plan モードへの切替はユーザー操作（Shift+Tab で plan モードに）。アシスタントは「**先に plan モードへ切り替えて設計しましょう**」と案内する。

1. **plan モードに切り替える** → その中で brainstorming の対話（探索→1問ずつ質問→2〜3案→セクションごと合意）を進める。
2. 合意した最終設計を **plan ファイルに記述**し、**ExitPlanMode** で承認（plan プレビュー）に出す（この段階では `docs/superpowers/specs/` への書き出し・`writing-plans` への遷移はまだしない）。
3. ユーザーがプランプレビューでレビューし、指摘を反映（必要なら直して再度 ExitPlanMode）。
4. 承認されたら、**レビュー反映後の最終設計を `docs/superpowers/specs/YYYY-MM-DD-<topic>-design.md` に書き出す** → 下記プレビューで HTML 化＋表示 → `superpowers:writing-plans` で実装計画へ。
- まとめ: brainstorming 対話 → plan ファイル → ExitPlanMode（レビュー）→ 承認＆反映 → `docs/superpowers/specs/` へ spec 書き出し → `writing-plans`。
- 補足: plan モードでない通常対話の brainstorming は従来どおり（直接 spec 書き出し）でよい。本順序は「plan モードでレビューを挟みたいとき」のもの。

## spec / plan の Markdown プレビュー
spec（設計書）や plan（実装計画）など**レビュー目的の Markdown を作成・更新したら、HTML に変換してブラウザで開く**まで行う（毎回「プレビューして」と言われなくても自動で）。出力先は scratchpad 等の一時ディレクトリ、`open <output.html>` で表示（**生成 HTML はコミットしない**）、Markdown 更新時は再生成。
- **変換手段（優先順・無ければ次へフォールバック）**:
  1. **`markdown-to-html` スキル**（`scripts/md_to_html.py`、外部ライブラリ不要の自己完結）で**完全な HTML 文書**を出力（`--fragment` なし）。← 推奨。
  2. スキルが**無いプロジェクト**では（＝他PJで起きやすい）、次のいずれかにフォールバック:
     - (a) **環境セットアップとして `markdown-to-html` スキルをそのプロジェクトに用意することを提案**（依存なしの python スクリプト1本。opt-in）。
     - (b) `pandoc <in>.md -o <out>.html` がインストール済みならそれで変換 → `open`。
     - (c) いずれも無ければ、最小の変換（python の markdown ライブラリ、または簡易変換スクリプトを scratchpad に書いて実行）で HTML 化 → `open`。
  3. どれも難しい場合は、その旨を伝えて**ユーザーに選んでもらう**（プレビュー無しで進める／変換手段を用意する）。
- 要は「**HTML 化してブラウザ表示**」という目的が達成できればよく、特定スキルの有無で止めない。

## Auto mode の前提と注意
- **前提**: Auto mode（自動承認）を使う前に `permissions-audit` でパーミッションを整理し、**危険操作は deny 済み**にしておく。未整理のまま自動承認は危険。
- **スキルからは有効化できない**: Auto mode は Claude Code の CLI 機能。本スキルは「ここで切り替えると効率的」と案内するだけで、切替はユーザーが行う。
- 整理済みなら、設計合意後の反復実装（横断書き換え・テスト・台帳更新等）は Auto mode で効率よく回る。

## 関連スキル
- 設計: `superpowers:brainstorming` / 計画: `superpowers:writing-plans`
- パーミッション整理: `permissions-audit`
- 技術パターン（実装）: `basercms5-development`（5系開発ルール）/ `basercms-plugin-4-to-5-upgrade`（プラグイン4→5の書き換え＋「移行の進め方」）/ `basercms-plugin-5x-update`（5.x 間の更新）/ `basercms4-to-5-upgrade`（サイト全体の4→5）
- テスト実行: `basercms-unittest`
- プレビュー変換: `markdown-to-html`
- プラグイン固有: `cpm-plugin-development`（Cpm の業務知識・横断移行の成果物）
