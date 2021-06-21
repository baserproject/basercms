<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Mail.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */
echo $this->Html->script(
	$this->request->is('ajax') ? 'Mail.mail_token_ajax' : 'Mail.mail_token',
	['defer']
);
?>
<script>
	var getTokenUrl = '<?php echo $this->BcBaser->getUrl('/bc_form/ajax_get_token?requestview=false') ?>';
</script>
