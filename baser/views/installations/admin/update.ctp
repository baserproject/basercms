<h2>
	<?php $baser->contentsTitle() ?>
</h2>
<div class="corner10" style="background-color:#f2f2f2;padding:15px 5px;">
	<p> <strong>BaserCMSのバージョン</strong>：<?php echo $baserVer ?><br />
		<strong>WEBサイトのバージョン</strong>：<?php echo $siteVer ?> </p>
	<?php if($baserVer != $siteVer): ?>
	<p>WEBサイトのアップデートを実行してBaserCMSのアップデートを完了させてください。</p>
		<?php if($scriptNum): ?>
	<p>アップデートプログラムが <strong><?php echo $scriptNum ?> 個</strong> あります。<br />
		<strong>実行する前には必ずデータベースをバックアップしておいてください。</strong></p>
	<p>
		<?php $baser->link('≫ バックアップはこちらから','/admin/site_configs/backup_data') ?>
	</p>
		<?php endif ?>
	<?php echo $form->create(array('action'=>'update')) ?> <?php echo $form->hidden('Installation.update',array('value',true)) ?> <?php echo $form->end(array('label'=>'アップデート実行','class'=>'button btn-red')) ?>
	<?php else: ?>
	<p>WEBサイトのバージョンは最新です。</p>
	<p>
		<?php $baser->link('≫ 管理画面に移動する','/admin/users/login') ?>
	</p>
	<?php endif ?>
</div>
