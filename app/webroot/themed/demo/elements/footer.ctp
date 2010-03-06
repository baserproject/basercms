<?php
/* SVN FILE: $Id$ */
/**
 * フッター
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2009, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2009, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<div id="footer">

	<div id="footerInner">
	
		<?php $baser->element('global_menu') ?>
		<p id="copyright">
			Copyright(C) <?php $baser->copyYear(2008) ?> BaserCMS All rights Reserved.
			<a href="http://basercms.net/" target="_blank"><?php echo $html->image('baser.power.gif', array('alt'=> 'BaserCMS : Based Website Development Project', 'border'=> "0")); ?></a>&nbsp;
			<a href="http://cakephp.org/" target="_blank"><?php echo $html->image('cake.power.gif', array('alt'=> 'CakePHP(tm) : Rapid Development Framework', 'border'=> "0")); ?></a>
		</p>
	
	</div>

</div>