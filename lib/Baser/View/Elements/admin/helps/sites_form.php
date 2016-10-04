<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] サブサイト編集　ヘルプ
 */
?>


<p>メインサイトに関連するサブサイトの登録を行います。<br>サブサイトのデザインを切り替えるには次の方法があります。</p>
<ul>
	<li>テーマ設定：サブサイト用のテーマを準備し、サブサイトにテーマを設定する。</li>
	<li>レイアウトテンプレート設定：サブサイト用のレイアウトテンプレートを準備し、関連するフォルダ、コンテンツで個別にレイアウトテンプレートを設定する。</li>
	<li>サブフォルダ配置：サブサイト用の各種テンプレートを準備し、設定されいているテーマにサブサイト識別名称と同じフォルダを作成し配置する。</li>
</ul>
<div class="example-box">
	<p class="head">サブフォルダ配置の設定例</p>
	<p>&nbsp;</p>
	<p>　レイアウトテンプレートの場合・・・ theme/テーマフォルダ/Layouts/smartphone/default.php を配置</p>
	<p>　エレメントテンプレートの場合・・・ theme/テーマフォルダ/Elements/smartphone/crumbs.php を配置</p>
	<p>　コンテンツテンプレートの場合・・・ theme/テーマフォルダ/Blog/smartphone/default/index.php を配置</p>
</div>