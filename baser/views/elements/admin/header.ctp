<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] ヘッダー
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<div id="header">
	<div id="headMain">
		<?php if(isset($baser->siteConfig['name'])): ?>
		<h1>
			<?php $baser->link($baser->siteConfig['name'],'/',array('target'=>'_blank')) ?>
		</h1>
		<?php else: ?>
		<h1><?php echo Configure::read('Baser.title') ?></h1>
		<?php endif ?>
		<p id="fontChanger">フォントサイズ： <a href="#" onclick="setActiveStyleSheet('large'); return false;">大</a>｜ <a href="#" onclick="setActiveStyleSheet('medium'); return false;">中</a>｜ <a href="#" onclick="setActiveStyleSheet('small'); return false;">小</a> </p>
	</div>
	<div id="glbMenus">
		<h2 class="display-none">グローバルメニュー</h2>
		<?php if($this->params['url']['url'] != 'admin/users/login' &&
					$this->params['url']['url'] != 'installations/update' &&
					($this->name != 'CakeError' || isset($_SESSION['Auth']['User']))): ?>
		<?php $baser->element('global_menu',array('menuType'=>'admin')) ?>
		<?php endif; ?>
	</div>
</div>
