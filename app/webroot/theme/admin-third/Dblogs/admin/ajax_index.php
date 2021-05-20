<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * @var array $dblogs
 * @var BcAppView $this
 */
if (empty($dblogs)) {
	return;
}
?>
<div class="bca-update-log">
	<?php $this->passedArgs['action'] = 'ajax_index' ?>
	<ul class="clear bca-update-log__list">
		<?php foreach($dblogs as $record): ?>
		<li class="bca-update-log__list-item">
			<span class="date">
				<?= $this->BcTime->format('Y.m.d', Hash::get($record, 'Dblog.created')) ?>
			</span>
			<small>
				<?= $this->BcTime->format('H:i:s', Hash::get($record, 'Dblog.created')) ?>&nbsp;
				<?php
				if (Hash::get($record, 'User.name')) {
					echo sprintf('[%s]', h(Hash::get($record, 'User.name')));
				}
				?>
			</small><br/>
			<?php echo nl2br(h(Hash::get($record, 'Dblog.name'))) ?>
		</li>
		<?php endforeach; ?>
	</ul>
	<div class="align-right">
		<a
			href="<?= Router::url(['controller'=>'dblogs','action'=>'/']) ?>/"
		>&gt; 全てのログを見る</a>
	</div>
</div>
