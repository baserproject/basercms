<?php
/**
 * 404エラーページ
 * 呼出箇所：エラー発生時
 */
?>


<h2><?php echo $message; ?></h2>
<p class="error">
	<strong><?php echo __d('cake', 'Error'); ?>: </strong>
	<?php printf(
		__d('cake', 'The request sent to the address %s was invalid.'),
		"<strong>'{$url}'</strong>"
	); ?>
</p>
<?php
if (Configure::read('debug') > 0):
	/* /lib/Cake/View/Elements/exception_stack_trace.ctp */
	echo $this->element('exception_stack_trace');
endif;