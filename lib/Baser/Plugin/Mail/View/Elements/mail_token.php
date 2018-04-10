<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<script type="text/javascript">
$(document).ready(function(){
	$('input[type="submit"]').prop('disabled', true);
});
<?php if($this->request->is('ajax')): ?>
$(document).ready(function(){
<?php else: ?>
$(window).on('load', function() {
<?php endif ?>
	var getTokenUrl = '<?php echo $this->BcBaser->getUrl('/bc_form/ajax_get_token?requestview=false') ?>';
	$.ajaxSetup({cache: false});
	$.get(getTokenUrl, function(result) {
		$('input[name="data[_Token][key]"]').val(result);
		$('input[type="submit"]').removeAttr('disabled');
	});
});
</script>
