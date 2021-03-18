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
 * baserCMS初期化ページ
 */
$adminPrefix = Configure::read('Routing.prefixes.0');
$this->BcBaser->i18nScript([
	'message' => __d('baser', '本当にbaserCMSを初期化してもよろしいですか？')
]);
?>


<script type="text/javascript">
	$(function () {
		$("#InstallationResetForm").submit(function () {
			if (confirm(bcI18n.message)) {
				return true;
			} else {
				return false;
			}
		});
	});
</script>

<?php if (!$complete): ?>

	<p class="bca-main__text"><?php echo __d('baser', 'baserCMSを初期化します。データベースのデータも全て削除されます。') ?></p>
	<?php if (BC_INSTALLED): ?>
		<p class="bca-main__text"><?php echo __d('baser', 'データベースのバックアップをとられていない場合は必ずバックアップを保存してから実行してください。') ?></p>
		<ul>
			<li><?php $this->BcBaser->link(__d('baser', 'バックアップはこちらから'), ['admin' => true, 'controller' => 'tools', 'action' => 'maintenance', 'backup', '?' => ['backup_encoding' => 'UTF-8']]) ?></li>
		</ul>
	<?php endif ?>
	<div class="submit bca-actions">
		<?php echo $this->BcForm->create('Installation', ['url' => ['action' => 'reset']]) ?>
		<?php echo $this->BcForm->input('Installation.reset', ['type' => 'hidden', 'value' => true]) ?>
		<?php echo $this->BcForm->end(['label' => __d('baser', '初期化する'), 'class' => 'button', 'class' => 'button bca-btn bca-actions__item']) ?>
	</div>
<?php else: ?>

	<div class="section">
		<p class="bca-main__text"><?php echo __d('baser', '引き続きbaserCMSのインストールを行うには、「インストールページへ」ボタンをクリックしてください。') ?></p>
	</div>
	<div class="submit bca-actions">
		<?php $this->BcBaser->link(__d('baser', 'インストールページへ'), '/', ['class' => 'button btn-red bca-btn bca-actions__item']) ?>
	</div>
<?php endif ?>
