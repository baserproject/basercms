<p>ZIP 形式のプラグインファイルをお持ちの場合、こちらからアップロードしてインストールできます。</p>
<?php echo $this->BcForm->create('Plugin', array('type' => 'file')) ?>

<div class="submit">
	<?php echo $this->BcForm->file('Plugin.file', array('type' => 'file')) ?>
	<?php echo $this->BcForm->submit('インストール', array('class' => 'button', 'div' => false)) ?>
</div>
			
<?php echo $this->BcForm->end() ?>
