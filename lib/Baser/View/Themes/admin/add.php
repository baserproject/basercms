<p>ZIP 形式のテーマファイルをお持ちの場合、こちらからアップロードして適用できます。</p>
<?php echo $this->BcForm->create('Theme', array('type' => 'file')) ?>

<div class="submit">
	<?php echo $this->BcForm->file('Theme.file', array('type' => 'file')) ?>
	<?php echo $this->BcForm->submit('適用', array('class' => 'button', 'div' => false)) ?>
</div>
			
<?php echo $this->BcForm->end() ?>
