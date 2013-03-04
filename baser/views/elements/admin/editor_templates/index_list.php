<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] エディタテンプレート一覧　テーブル
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


<table cellpadding="0" cellspacing="0" class="list-table" id="ListTable">
	<thead>
		<tr>
			<th style="width:140px" class="list-tool">
				<div>
					<?php $bcBaser->link($bcBaser->getImg('admin/btn_add.png', array('width' => 69, 'height' => 18, 'alt' => '新規追加', 'class' => 'btn')), array('action' => 'add')) ?>
				</div>
			</th>
			<th>NO</th>
			<th>テンプレート名</th>
			<th>説明文</th>
			<th>登録日<br />
				更新日</th>
		</tr>
	</thead>
	<tbody>
		<?php if(!empty($datas)): ?>
			<?php foreach($datas as $data): ?>
				<?php $bcBaser->element('editor_templates/index_row', array('data' => $data)) ?>
			<?php endforeach; ?>
		<?php else: ?>
		<tr>
			<td colspan="5"><p class="no-data">データが見つかりませんでした。</p></td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>
