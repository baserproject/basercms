<?php
/**
 * フッター
 */
?>

<div id="Footer">
    <div id="footer-menu"><?php $bcBaser->element('global_menu') ?>
	<p id="copyright"> Copyright(C)
        <?php $bcBaser->copyYear(2008) ?>
        baserCMS All rights Reserved.
    </p>
    <p id="copyright">
        <a href="http://basercms.net/" target="_blank"><?php $bcBaser->img('baser.power.gif', array('alt'=> 'baserCMS : Based Website Development Project')); ?></a>
        &nbsp; 
        <a href="http://cakephp.org/" target="_blank"><?php $bcBaser->img('cake.power.gif', array('alt'=> 'CakePHP(tm) : Rapid Development Framework')); ?></a>
        &nbsp; 
        <a href="http://flagsystem.co.jp" target="_blank"><?php $bcBaser->img('./footer/flag.gif', array('alt'=> 'flagsystem.co.jp')); ?></a>
        &nbsp; 
        <a href="http://nadadesigns.flagsystem.co.jp/" target="_blank"><?php $bcBaser->img('./footer/nada.gif', array('alt'=> 'nada designs')); ?></a>
	</p>
    </div>
</div>