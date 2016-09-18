<?php
/**
 * [ADMIN] ページ管理メニュー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 2.0.0
 * @license			http://basercms.net/license/index.html
 */
?>


<tr>
	<th>固定ページ管理メニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link('固定ページ一覧', array('controller' => 'pages', 'action' => 'index')) ?></li>
			<?php if ($newCatAddable): ?>
				<li><?php $this->BcBaser->link('固定ページ新規追加', array('controller' => 'pages', 'action' => 'add')) ?></li>
			<?php endif; ?>
			<li><?php $this->BcBaser->link('固定ページテンプレート読込', array('controller' => 'pages', 'action' => 'entry_page_files'), array('class' => 'submit-token', 'confirm' => 'テーマ ' . Inflector::camelize($this->BcBaser->siteConfig['theme']) . " フォルダ内のページテンプレートを全て読み込みます。\n本当によろしいですか？")) ?></li>
			<li><?php $this->BcBaser->link('固定ページテンプレート書出', array('controller' => 'pages', 'action' => 'write_page_files'), array('class' => 'submit-token', 'confirm' => 'データベース内のページデータを、' . 'テーマ ' . Inflector::camelize($this->BcBaser->siteConfig['theme']) . " のページテンプレートとして全て書出します。\n本当によろしいですか？")) ?></li>
		</ul>
	</td>
</tr>
