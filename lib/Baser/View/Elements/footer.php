<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] フッター
 * 
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


<div id="Footer">
	<p id="copyright"> Copyright(C)
		<?php $this->BcBaser->copyYear(2008) ?>
		baserCMS All rights Reserved. <a href="http://basercms.net/" target="_blank"><?php echo $this->BcHtml->image('baser.power.gif', array('alt'=> 'baserCMS : Based Website Development Project', 'border'=> "0")); ?></a>&nbsp; <a href="http://cakephp.org/" target="_blank"><?php echo $this->BcHtml->image('cake.power.gif', array('alt'=> 'CakePHP(tm) : Rapid Development Framework', 'border'=> "0")); ?></a>
	</p>
</div>