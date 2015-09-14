<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
$prefix = '';
if (Configure::read('BcRequest.agent')) {
	$prefix = '/' . Configure::read('BcRequest.agentAlias');
}
?>
<script type="text/javascript">
$(document).ready(function(){
	$('input[type="submit"]').attr('disabled', 'disabled');
});
$(window).unload(function(){});
$(window).load(function(){
	var getTokenUrl = '<?php echo $this->BcBaser->getUrl($prefix . '/' . $mailContent['MailContent']['name'] . '/ajax_get_token') ?>';
	$.ajaxSetup({cache: false});
	$.get(getTokenUrl, function(result) {
		$('input[name="data[_Token][key]"]').val(result);
		$('input[type="submit"]').removeAttr('disabled');
	});
});
</script>
