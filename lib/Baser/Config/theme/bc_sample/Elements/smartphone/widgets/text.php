<?php
/**
 * テキスト（スマホ用）
 * 呼出箇所：ウィジェット
 */
?>


<div class="widget widget-text widget-text-<?php echo $id ?>">
	<?php if ($name && $use_title): ?>
		<h2><?php echo $name ?></h2>
	<?php endif ?>
	<?php echo $text ?>
</div>
