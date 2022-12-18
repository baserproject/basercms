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
$this->BcBaser->js(
	$this->request->is('ajax') ? 'BcMail.mail_token_ajax' : 'BcMail.mail_token',
	true,
	['defer']
);
?>
<script>
	var getTokenUrl = '<?php echo $this->BcBaser->getUrl('/baser-core/bc_form/get_token?requestview=false') ?>';
</script>
