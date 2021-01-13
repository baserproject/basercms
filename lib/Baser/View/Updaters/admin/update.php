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
 * [ADMIN] アップデート
 * @var BcAppView $this
 */
if (!($baserVerPoint === false || $siteVerPoint === false) && ($baserVer != $siteVer || $scriptNum)) {
	$requireUpdate = true;
} else {
	$requireUpdate = false;
}
$this->BcBaser->i18nScript([
	'confirmMessage1' => __d('baser', 'アップデートを実行します。よろしいですか？'),
]);
$this->BcBaser->js('admin/updaters/update', false);
?>


<?php $this->start('script') ?>
<style type="text/css">
	.em-box {
		margin-top: 20px;
		font-weight: normal;
	}

	.em-box h3 {
		font-size: 16px;
		border: 0;
		color: #000;
		margin-bottom: 0;
		line-height: 24px;
	}

	.em-box table {
		margin-top: 10px;
	}

	.em-box th {
		width: 60px;
	}

	.em-box td {
		text-align: left;
	}
</style>
<?php $this->end('script') ?>


<div class="corner10 panel-box section">
	<h2><?php echo __d('baser', '現在のバージョン状況') ?></h2>
	<ul class="version">
		<li><?php echo sprintf(__d('baser', '%1$sのバージョン： <strong>%2$s</strong>'), $updateTarget, $baserVer) ?></li>
		<li><?php echo sprintf(__d('baser', '現在のデータベースのバージョン：<strong> %s </strong>'), $siteVer) ?></li>
	</ul>
	<?php if ($scriptNum || $scriptMessages): ?>
		<div class="em-box">
			<?php if ($baserVerPoint === false || $siteVerPoint === false): ?>
				<h3><?php echo __d('baser', 'α版の場合はアップデートサポート外です。') ?></h3>
			<?php elseif ($baserVer != $siteVer || $scriptNum): ?>
				<?php if ($scriptNum): ?>
					<h3><?php echo sprintf(__d('baser', 'アップデートプログラムが <strong>%s つ</strong> あります。'), $scriptNum) ?></h3>
				<?php endif ?>
			<?php else: ?>
				<h3><?php echo __d('baser', 'データベースのバージョンは最新です。') ?></h3>
			<?php endif ?>
			<?php if ($scriptMessages): ?>
				<table>
					<?php foreach($scriptMessages as $key => $scriptMessage): ?>
						<tr>
							<th><?php echo $key ?></th>
							<td><?php echo $scriptMessage ?></td>
						</tr>
					<?php endforeach ?>
				</table>
			<?php endif ?>
		</div>
	<?php endif ?>
</div>

<?php if ($scriptNum): ?>
	<div class="corner10 panel-box section">
		<div class="section">
			<h2><?php echo __d('baser', 'データベースのバックアップは行いましたか？') ?></h2>
			<p>
				<?php if (!$plugin): ?>
					<?php echo __d('baser', 'バックアップを行われていない場合は、アップデートを実行する前に、プログラムファイルを前のバージョンに戻しシステム設定よりデータベースのバックアップを行いましょう。') ?>
					<br/>
				<?php else: ?>
					<?php echo __d('baser', 'バックアップを行われていない場合は、アップデートを実行する前にデータベースのバックアップを行いましょう。') ?><br/>
				<?php endif ?>
				<small>※ <?php echo __d('baser', 'アップデート処理は必ず自己責任で行ってください。') ?></small><br/>
			</p>
		</div>
		<div class="section">
			<h2><?php echo __d('baser', 'リリースノートのアップデート時の注意事項は読まれましたか？') ?></h2>
			<p><?php echo __d('baser', 'リリースバージョンによっては、追加作業が必要となる場合があるので注意が必要です。<br />公式サイトの <a href="https://basercms.net/news/archives/category/release" target="_blank" class="outside-link">リリースノート</a> を必ず確認してください。') ?></p>
		</div>
	</div>
<?php endif ?>

<div class="corner10 panel-box section">
	<?php if ($requireUpdate): ?>
		<p><?php echo __d('baser', '「アップデート実行」をクリックしてデータベースのアップデートを完了させてください。') ?></p>
		<?php if (empty($plugin)): ?>
			<?php echo $this->BcForm->create('Updater', ['url' => ['action' => $this->request->action]]) ?>
		<?php else: ?>
			<?php echo $this->BcForm->create('Updater', ['url' => ['action' => $this->request->action, $plugin]]) ?>
		<?php endif ?>
		<?php echo $this->BcForm->input('Installation.update', ['type' => 'hidden', 'value' => true]) ?>
		<?php echo $this->BcForm->end(['label' => __d('baser', 'アップデート実行'), 'class' => 'button btn-red', 'id' => 'BtnUpdate']) ?>
	<?php else: ?>
		<p>
		<?php if (!$plugin): ?>
			<p><?php echo sprintf(__d('baser', 'baserCMSコアのアップデートがうまくいかない場合は、%sにご相談されるか、前のバージョンの baserCMS に戻す事をおすすめします。'), $this->BcBaser->getLink('baserCMSの制作・開発パートナー', 'https://basercms.net/partners/', ['target' => '_blank'])) ?></p>
			<?php if (!$requireUpdate): ?>
				<?php $this->BcBaser->link('≫ 管理画面に移動する', '/' . BcUtil::getAdminPrefix()) ?>
			<?php endif ?>
		<?php else: ?>
			<?php $this->BcBaser->link(__d('baser', 'プラグイン一覧に移動する'), ['controller' => 'plugins', 'action' => 'index'], ['class' => 'outside-link']) ?>
		<?php endif ?>
		</p>
	<?php endif ?>
</div>

<?php if ($log): ?>
	<div class="corner10 panel-box section" id="UpdateLog">
		<h2><?php echo __d('baser', 'アップデートログ') ?></h2>
		<?php echo $this->BcForm->textarea('Updater.log', [
			'value' => $log,
			'style' => 'width:99%;height:200px;font-size:12px',
			'readonly' => 'readonly'
		]); ?>
	</div>
<?php endif; ?>
