<?php
/* SVN FILE: $Id$ */
/**
 * baserCMSシェル
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.vendors.shells
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
/**
 * baserCMSシェル
 * 
 * [cakeにパスが通っている場合]
 * cake -app /absolute/path/to/myapp install
 * 
 * [cakeにパスが通っていない場合]
 * /absolute/path/to/cake/console/cake -app /absolute/path/to/myapp install
 * 
 * [phpにパスが通っていない場合]
 * /absolute/path/to/php /absolute/path/to/cake/console/cake.php -app /absolute/path/to/myapp install
 * 
 * @package baser.vendors.shells
 */
class BcAppShell extends Shell {
/**
 * startup
 */
	function startup() {
		$this->out("\nWelcome to baserCMS v" . getVersion() . " Console");
		$this->out("---------------------------------------------------------------");
		$this->out('App : '. $this->params['app']);
		$this->out('Path: '. $this->params['working']);
		$this->hr();		
	}
	
}