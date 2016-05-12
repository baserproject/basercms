<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Uploader.View
 * @since			baserCMS v 3.0.10
 * @license			http://basercms.net/license/index.html
 */
$this->BcBaser->css('Uploader.uploader', array('inline' => false));
?>


<script type="text/javascript">
$(window).load(function() {
	$("#UploaderFileFile").focus();
});
</script>

<?php $this->BcBaser->element('uploader_files/index') ?>
