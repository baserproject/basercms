<?php
/**
 * フッター
 *
 * BcBaserHelper::footer() で呼び出す
 * （例）<?php $this->BcBaser->footer() ?>
 */
?>


<footer>
	<p id="Copyright"> Copyright(C)
		<?php $this->BcBaser->copyYear(2008) ?>
		baserCMS All rights Reserved.
		<a href="http://basercms.net/" target="_blank"><?php echo $this->BcHtml->image('baser.power.gif', array('alt' => 'baserCMS : Based Website Development Project', 'border' => "0")); ?></a>&nbsp;
		<a href="http://cakephp.org/" target="_blank"><?php echo $this->BcHtml->image('cake.power.gif', array('alt' => 'CakePHP(tm) : Rapid Development Framework', 'border' => "0")); ?></a>
	</p>
</footer>