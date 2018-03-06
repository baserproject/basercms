<?php
/**
 * 404エラーページ
 * 呼出箇所：エラー発生時
 */
?>


<h2><?php echo $message; ?></h2>
<p class="error">
	<strong><?php echo __('エラー'); ?>: </strong>
	<?php printf(
		__('アドレス %s に送信されたリクエストは無効です。'),
		"<strong>'{$url}'</strong>"
	); ?>
</p>
<?php
if (Configure::read('debug') > 0):
	/* /lib/Cake/View/Elements/exception_stack_trace.ctp */
	echo $this->element('exception_stack_trace');
endif;