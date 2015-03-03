<?php
/**
 * [ADMIN] プラグイン一覧　テーブル
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<table cellpadding="0" cellspacing="0" class="list-table" id="ListTable">
<thead>
	<tr class="list-tool">
		<th>&nbsp;</th>
		<th>プラグイン名</th>
		<th style="white-space: nowrap">バージョン</th>
		<th>説明</th>
		<th>開発者</th>
		<th>登録日<br />更新日</th>
	</tr>
</thead>
<tbody>
	<?php if (!empty($baserPlugins)): ?>
		<?php foreach ($baserPlugins as $data): ?>
			<?php $this->BcBaser->element('plugins/index_row_market', array('data' => $data)) ?>
		<?php endforeach; ?>
	<?php else: ?>
		<tr>
			<td colspan="6">
		<?php if(strtotime('2014-03-31 17:00:00') >= time()): ?>
				<p class="no-data">baserマーケットは、2014年3月31日 17時に公開です。お楽しみに！</p>
		<?php else: ?>
				<p class="no-data">baserマーケットのテーマを読み込めませんでした。</p>
		<?php endif ?>
			</td>
		</tr>
	<?php endif; ?>
</tbody>
</table>
