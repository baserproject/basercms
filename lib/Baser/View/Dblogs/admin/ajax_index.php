<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<?php if ($dblogs): ?>
	<?php $this->passedArgs['action'] = 'ajax_index' ?>
	<?php $this->BcBaser->element('pagination', ['modules' => 4, 'options' => ['url' => ['action' => 'ajax_index']]]) ?>
	<ul class="clear">
		<?php foreach ($dblogs as $record): ?>
			<li><span class="date"><?php echo $this->BcTime->format('Y.m.d', $record['Dblog']['created']) ?></span>
				<small><?php echo $this->BcTime->format('H:i:s', $record['Dblog']['created']) ?>&nbsp;
					<?php
					$userName = $this->BcBaser->getUserName($record['User']);
					if ($userName) {
						echo '[' . h($userName) . ']';
					}
					?>
				</small><br />
				<?php echo nl2br(h($record['Dblog']['name'])) ?></li>
		<?php endforeach; ?>
	</ul>
	<?php $this->BcBaser->element('list_num') ?>
	<?php if(BcUtil::isAdminUser()): ?>
	<div class="submit clear">
		<?php
		$this->BcBaser->link(__d('baser', '削除'), ['action' => 'del'], ['class' => 'btn-gray button submit-token'], __d('baser', '最近の動きのログを削除します。いいですか？'))
		?>
	</div>
	<?php endif ?>	
<?php endif ?>