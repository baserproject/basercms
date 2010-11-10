<h2>
	<?php $baser->contentsTitle() ?>
</h2>
<div class="corner10" style="background-color:#f2f2f2;padding:15px 5px;">
	<p> <strong><?php echo $updateTarget ?> のバージョン</strong>：<?php echo $baserVer ?><br />
		<strong>現在のバージョン</strong>：<?php echo $siteVer ?>
	</p>
	<?php if($baserVer != $siteVer || $scriptNum): ?>
	<p>「アップデート実行」をクリックして <?php echo $updateTarget ?> のアップデートを完了させてください。</p>
		<?php if($scriptNum): ?>
	<p>アップデートプログラムが <strong><?php echo $scriptNum ?> 個</strong> あります。<br />
		<strong>実行する前には必ずバックアップを実行しておいてください。</strong></p>
	<p>
		<?php $baser->link('≫ バックアップはこちらから','/admin/site_configs/backup_data') ?>
	</p>
		<?php endif ?>
	<?php echo $form->create(array('action'=>$this->action, 'url'=>array($plugin))) ?>
	<?php echo $form->hidden('Installation.update',array('value'=>true)) ?>
	<?php echo $form->end(array('label'=>'アップデート実行','class'=>'button btn-red')) ?>
		<?php else: ?>
	<p>WEBサイトのバージョンは最新です。</p>
	<p>
		<?php if(!$plugin): ?>
			<?php $baser->link('≫ 管理画面に移動する','/admin/users/login') ?>
		<?php else: ?>
			<?php $baser->link('≫ プラグイン一覧に移動する','/admin/plugins/index') ?>
		<?php endif ?>
	</p>
	<?php endif ?>
</div>
