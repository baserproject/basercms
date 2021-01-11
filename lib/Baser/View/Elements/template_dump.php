<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 3.0.10
 * @license         https://basercms.net/license/index.html
 */

/**
 * 利用しているテンプレート一覧とビュー変数一覧
 *
 * デバッグモード２以上で表示
 */
if (empty($this->_viewFilesLog)) return;
?>


<table class="cake-sql-log" id="baserTempaltes_%s" summary="baserCMS Templates" cellspacing="0">
	<tr>
		<th>Nr</th>
		<th>Template</th>
	</tr>
	<?php $count = 1 ?>
	<?php foreach($this->_viewFilesLog as $log): ?>
		<tr>
			<td><?php echo $count ?>.</td>
			<td><?php echo $log ?></td>
		</tr>
		<?php $count++ ?>
	<?php endforeach ?>
</table>


<table class="cake-sql-log" id="baserViewVars_%s" summary="baserCMS View Variables" cellspacing="0"
	   style="white-space: pre-wrap;">
	<tr>
		<th>Nr</th>
		<th>View Variable</th>
		<th>Type</th>
		<th>Value</th>
	</tr>
	<?php $count = 1 ?>
	<?php foreach($this->viewVars as $name => $value): ?>
		<tr>
			<td><?php echo $count ?>.</td>
			<td><?php echo $name ?></td>
			<td><?php echo is_object($value)? get_class($value) : gettype($value) ?></td>
			<td style="word-break: break-all"><?php echo h(print_r($value, true)) ?></td>
		</tr>
		<?php $count++ ?>
	<?php endforeach ?>
</table>
