<?php
/**
 * フッター
 */
?>

<div id="Footer">
    <div id="footer-menu"><?php $this->BcBaser->element('global_menu') ?>
		<p id="copyright"> Copyright(C)
			<?php $this->BcBaser->copyYear(2008) ?>
			baserCMS All rights Reserved.
		</p>
		<p id="copyright">
			<a href="http://basercms.net/" target="_blank"><?php $this->BcBaser->img('baser.power.gif', ['alt' => 'baserCMS : Based Website Development Project']); ?></a>
			&nbsp; 
			<a href="http://cakephp.org/" target="_blank"><?php $this->BcBaser->img('cake.power.gif', ['alt' => 'CakePHP(tm) : Rapid Development Framework']); ?></a>
			&nbsp; 
			<a href="http://flagsystem.co.jp" target="_blank"><?php $this->BcBaser->img('footer/flag.gif', ['alt' => 'flagsystem.co.jp']); ?></a>
		</p>
    </div>
</div>