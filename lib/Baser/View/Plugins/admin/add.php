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


<script>
$(function(){
	$("#BtnSave").click(function(){
		if(confirm('プラグインをアップロードし、そのままインストールします。よろしいですか？')) {
			$.bcUtil.showLoader();
			return true;
		}
		return false;
	});
});
</script>


<p>ZIP 形式のプラグインファイルをお持ちの場合、こちらからアップロードしてインストールできます。</p>
<?php echo $this->BcForm->create('Plugin', array('type' => 'file')) ?>

<div class="submit">
	<?php echo $this->BcForm->file('Plugin.file', array('type' => 'file')) ?>
	<?php echo $this->BcForm->submit('インストール', array('class' => 'button', 'div' => false, 'id' => 'BtnSave')) ?>
</div>
			
<?php echo $this->BcForm->end() ?>
