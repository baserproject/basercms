<?php
/**
 * PHPテンプレート
 * 呼出箇所：ウィジェット
 */
if(!isset($subDir)) {
	$subDir = true;
}
?>


<div class="widget widget-php-template widget-php-template-<?php echo $id ?>">
	<?php if ($name && $use_title): ?>
		<h2><?php echo $name ?></h2>
	<?php endif ?>
	<?php $this->BcBaser->element('widgets' . DS . $template, array(), array('subDir' => $subDir)) ?>
</div>