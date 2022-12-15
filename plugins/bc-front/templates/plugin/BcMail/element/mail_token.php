<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * @var \BcMail\View\MailFrontAppView $this
 * @checked
 * @noTodo
 * @unitTest
 */
echo $this->Html->script(
	$this->request->is('ajax') ? 'mail_token_ajax' : 'mail_token',
	['defer']
);
?>
<script>
	var getTokenUrl = '<?php echo $this->BcBaser->getUrl('/bc_form/ajax_get_token?requestview=false') ?>';
</script>
